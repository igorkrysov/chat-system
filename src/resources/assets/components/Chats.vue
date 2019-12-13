<template>
    <div class="root">
        <div class="chats">
            <div class="chat">
                <form>
                    <div class="form-group">
                        <label for="participants">Chat participants:</label>
                        <select class="custom-select" id="participants" multiple size="1" v-model="participants">
                            <option v-for="user in users" v-bind:key="user.id" :value="user.id" multiple>{{ user.name }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name">Chat name:</label>
                        <input type="text" class="form-control" id="name" v-model="chatName" placeholder="name" required>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-success btn-create" @click.prevent="createChat()"  :disabled="participants.length < 1 || chatName == ''">create chat</button>
                    </div>
                </form>
            </div>
            <div class="chat" v-for="chat in chats" v-bind:key="chat.id" @click.prevent="loadMessages(chat.id)" :class="{active : activeChatId == chat.id}">
                {{ chat.name }}
            </div>
        </div>
        <div class="message-data">
            <div class="messages">
            <div class="message-row" :class="{own : message.src_id == userId}" v-for="message in messages" v-bind:key="message.id">
                <div class="message">
                    <div class="title-row">
                        <div class="title">
                            {{ from(message) }}
                        </div>
                        <div class="date">
                            {{ message.updated_at }}
                        </div>
                    </div>
                    <div class="text">{{ message.message }}</div>
                    <div class="files">
                        <div class="file" v-for="file in message.files" v-bind:key="file.id">
                            <a :href="'/' + file.file">
                                <img :src="typeFile(file)" alt="">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="new-message">
            <div class="form-group">
                <textarea v-model="message"
                            @keydown.enter.exact.prevent
                            @keyup.enter.exact="sendMessage"
                            @keydown.enter.shift.exact="newMessageLine"
                            class="form-control"
                            :readonly="activeChatId < 0" 
                            ></textarea>
            </div>
            <div class="form-group">
                <div class="files">
                    <div class="file" v-for="file in files" v-bind:key="file.id">
                        <a :href="'/' + file.file">
                            <img :src="typeFile(file)" alt="">
                        </a>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" accept=".jpg, .jpeg, .png" name="img" ref="photo" v-on:change="uploadFile()" :disabled="activeChatId < 0">
                    <label class="custom-file-label" for="validatedCustomFile">Choose file...</label>
                    <div class="invalid-feedback">Example invalid custom file feedback</div>
                </div>
            </div>
            
            <button class="btn btn-primary" :disabled="activeChatId < 0" @click.prevent="sendMessage">Send</button>
        </div>
        </div>
        
    </div>
    
</template>
<script>
export default {
    data() {
        return {
            users: [],
            chats: [],
            messages: [],
            files: [],
            message: "",
            participants: [],
            chatName: "",
            activeChatId: -1
        }
    },
    props: [
        'userId'
    ],
    mounted() {
        this.loadUsers();
        this.loadChats();
    },
    methods: {
        typeFile(file) {
            let re = /(?:\.([^.]+))?$/;
            let ext = re.exec(file.file)[1];
            console.log("file: " + file.file + " ext: " + ext);
            
            if (ext == "png" || ext == "jpeg" || ext == "jpg") {
                return "/storage/" + file.file
            }
            if (ext == "doc" || ext == "docx") {
                return "'background-image': 'url(word.png)'";
            }
        },
        from(message) {
            console.log(message)
            return message.src_id == this.userId ? "Вы писали: " : message.user.name + " писал(а):";
        },
        createChat() {
            if (this.participants.length > 0 && this.chatName !== "") {                
                let data = {
                    "name": this.chatName,
                    "participants": this.participants,

                }
                axios.post('/create-chat', data)
                    .then((response) => {
                        if (response.data.status) {
                            this.chats.push(response.data.chat)
                            this.chatName = ""
                            this.participants = []
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            }
        },
        loadUsers() {
            axios.get('/load-users')
                .then((response) => {
                    if (response.data.status) {
                        this.users = response.data.users
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        loadChats() {
            let that = this
            axios.get('/load-chats')
                .then(function (response) {
                    if (response.data.status) {
                        that.chats = response.data.chats
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        loadMessages(chatId) {
            this.activeChatId = chatId

            axios.get('/load-messages/' + chatId)
                .then((response) => {
                    if (response.data.status) {
                        this.messages = response.data.messages
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });

        },
        sendMessage() {
            if (this.activeChatId > 0 && this.message != "") {
                let data = {
                    "chatId": this.activeChatId,
                    "message": this.message,
                    "files_": this.files

                }
                axios.post('/send-message', data)
                    .then((response) => {
                        if (response.data.status) {
                            this.messages.push(response.data.message)
                            this.message = ""
                            this.files = []
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });                
            }
        },
        newMessageLine() {
             this.message += "\n";
        },
        uploadFile() {
            this.file = this.$refs.photo.files[0];
            let formData = new FormData();
            formData.append("file", this.file, this.file.name);
            axios.post("/upload-file", formData)
                .then(resp => {
                    if (resp.data.status) {
                        console.log("UPLOADED");
                        this.files.push(resp.data.file);
                        console.log(this.files);
                    } else if (resp.data.errors) {
                    }
                })
                .catch(err => {
                    console.log(err);
                });
        }
    }
}
</script>
<style lang="scss" scoped>
    .root {
        display: flex;
        flex-direction: row;
        width: 100%;
        // height: 100%;
    }
    .chats {
        display: flex;
        flex-direction: column;;
        width: 20%;
        border: 1px solid #E9E9E9;
        margin: 1px;
        min-height: 800px;
    }
    .chat {
        display: flex;
        width: 100%;
        border: 1px solid #F2F2F2;
        border-radius: 5px;
        min-height: 40px;
        margin: 1px;
        justify-content: center;
        cursor: pointer;
        .btn-create {
            width: 100%;
        }
        &.active {
            background: rgb(223, 223, 223);
        }
    }
    .message-data {
        // height: 80%;
        min-height: 800px;
        width: 80%;
    }
    .messages {
        width: 100%;
        height: 75%;
        border: 1px solid #E9E9E9;
        overflow-y: scroll;
    }
    .message-row {
        width: 100%;
        margin-top: 2px;
        display: flex;
        flex-direction: row-reverse;
        &.own {
            flex-direction: row;
            .message {  
                border: 1px solid #aeccb6;
            }

        }
    }
    .message {
        display: flex;
        flex-direction: column;
        width: 70%;
        border: 1px solid #aec9cc;
        border-radius: 5px;
        margin: 10px;
        padding: 20px;
        .title-row {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
        }
        .date {
            color: #949494;
            font-style: italic;
        }
        .title {
            color: #949494;
            font-style: italic;
        }
    }
    .new-message {
        border: 1px solid #E9E9E9;
    }
    .files {
        display: flex;
        flex-direction: row;
        justify-content: flex-start;
        flex-wrap: wrap;
    }
    .file {
        margin: 10px;
        img {
            width: 80px;
            height: 80px;
        }
    }
</style>