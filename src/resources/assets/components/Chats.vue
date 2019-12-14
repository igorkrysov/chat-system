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
            <div class="chat" v-for="chat in chats" v-bind:key="chat.id" @click.prevent="loadMessages(chat.id)" :class="{active : activeChatId == chat.id, 'new-message': chat.isNewMessage, 'online': chat.online}">
                {{ chat.name }}
            </div>
        </div>
        <div class="message-data">
            <div class="admin-pannel" v-if="activeChatId > 0 && activeChat.admin_id == userId">
                <button class="btn btn-info" @click.prevent="lock">{{ !activeChat.is_blocked ? 'to block' : 'to unblock'}}</button>
            </div>
            <div class="messages">
                <div class="message-row" :class="{own : message.src_id == userId}" v-for="message in messages" v-bind:key="message.id">
                    <div class="message">
                        <div class="title-row">
                            <div class="title">
                                {{ from(message) }}
                            </div>
                            <div class="date">
                                {{ message.updated_at }}
                                <span v-if="isEditable(message)">
                                    <a @click.prevent="edit(message)">Edit</a>
                                </span>
                                <span v-if="isEditable(message)">
                                    <a @click.prevent="remove(message)">Remove</a>
                                </span>
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
                                :readonly="activeChatId < 0 ||  activeChat.is_blocked == 1" 
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
                        <input type="file" class="custom-file-input" accept=".jpg, .jpeg, .png" name="img" ref="photo" 
                        v-on:change="uploadFile()" :disabled="activeChatId < 0 ||  activeChat.is_blocked == 1">
                        <label class="custom-file-label" for="validatedCustomFile">Choose file...</label>
                        <div class="invalid-feedback">Example invalid custom file feedback</div>
                    </div>
                </div>
                
                <button class="btn btn-primary" :disabled="activeChatId < 0 || activeChat.is_blocked == 1" @click.prevent="sendMessage">Send</button>
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
            activeChatId: -1,
            activeChat: -1,
            editMessageId: 0
        }
    },
    props: [
        'userId'
    ],
    mounted() {
        this.loadUsers();
        this.loadChats();
        this.listen();
    },
    methods: {
        lock() {
            this.activeChatId;
            axios.post('/block-chat/' +  this.activeChatId, {})
                .then((response) => {
                    if (response.data.status) {
                        this.activeChat.is_blocked = !this.activeChat.is_blocked;
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        remove(message) {
            axios.delete('/delete-message/' + message.id)
                .then((response) => {
                    if (response.data.status) {
                        let index = this.messages.indexOf(message);
                        this.messages.splice(index, 1)
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        edit(message) {
            this.editMessageId = message.id;
            this.message = message.message;
            this.files = message.files;
        },
        isEditable(message) {
            if (message.isEdit == undefined || message.src_id != this.userId) {
                return false
            }
            let now = new Date();
            let seconds = (now.getTime() - message.isEdit.getTime()) / 1000            
            return seconds > 10 ? false : true;
        },
        typeFile(file) {
            let re = /(?:\.([^.]+))?$/;
            let ext = re.exec(file.file)[1];
            
            if (ext == "png" || ext == "jpeg" || ext == "jpg") {
                return "/storage/" + file.file
            }
            if (ext == "doc" || ext == "docx") {
                return "data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHg9IjBweCIgeT0iMHB4Igp3aWR0aD0iMjYiIGhlaWdodD0iMjYiCnZpZXdCb3g9IjAgMCAyNiAyNiIKc3R5bGU9IiBmaWxsOiMwMDAwMDA7Ij48cGF0aCBkPSJNIDE1IDAgTCAwIDIuODc1IEwgMCAyMy4xMjUgTCAxNSAyNiBaIE0gMTYgMyBMIDE2IDUuOTY4NzUgTCAyMy4wMzEyNSA1Ljk2ODc1IEwgMjMuMDMxMjUgOCBMIDE2IDggTCAxNiAxMCBMIDIzIDEwIEwgMjMgMTIgTCAxNiAxMiBMIDE2IDE0IEwgMjMgMTQgTCAyMyAxNiBMIDE2IDE2IEwgMTYgMTggTCAyMyAxOCBMIDIzIDIwIEwgMTYgMjAgTCAxNiAyMyBMIDI1LjE1NjI1IDIzIEMgMjUuNjE3MTg4IDIzIDI2IDIyLjYwNTQ2OSAyNiAyMi4xMjUgTCAyNiAzLjg3NSBDIDI2IDMuMzk0NTMxIDI1LjYxNzE4OCAzIDI1LjE1NjI1IDMgWiBNIDEuOTY4NzUgNy45Mzc1IEwgMy44NzUgNy45Mzc1IEwgNSAxNC42ODc1IEMgNS4wNDY4NzUgMTQuOTcyNjU2IDUuMDcwMzEzIDE1LjM1OTM3NSA1LjA5Mzc1IDE1Ljg0Mzc1IEwgNS4xMjUgMTUuODQzNzUgQyA1LjE0MDYyNSAxNS40ODA0NjkgNS4xOTE0MDYgMTUuMDg1OTM4IDUuMjgxMjUgMTQuNjU2MjUgTCA2LjcxODc1IDcuOTM3NSBMIDguNTkzNzUgNy45Mzc1IEwgOS45MDYyNSAxNC43NSBDIDkuOTUzMTI1IDE1IDkuOTk2MDk0IDE1LjMzNTkzOCAxMC4wMzEyNSAxNS44MTI1IEMgMTAuMDQ2ODc1IDE1LjQ0MTQwNiAxMC4wOTM3NSAxNS4wNzAzMTMgMTAuMTU2MjUgMTQuNjg3NSBMIDExLjI1IDcuOTM3NSBMIDEzLjAzMTI1IDcuOTM3NSBMIDEwLjkzNzUgMTguMDYyNSBMIDkgMTguMDYyNSBMIDcuNjg3NSAxMS41NjI1IEMgNy42MTcxODggMTEuMjIyNjU2IDcuNTc4MTI1IDEwLjg1NTQ2OSA3LjU2MjUgMTAuNDM3NSBMIDcuNTMxMjUgMTAuNDM3NSBDIDcuNSAxMC44OTg0MzggNy40Njg3NSAxMS4yNjU2MjUgNy40MDYyNSAxMS41NjI1IEwgNi4wNjI1IDE4LjA2MjUgTCA0LjAzMTI1IDE4LjA2MjUgWiI+PC9wYXRoPjwvc3ZnPg=="
            }

            return "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAABmJLR0QA/wD/AP+gvaeTAAAA3UlEQVRIie2VPw/BQBiHHyI6aaSRGMQH8E2s0ojJZCT9cD4Bk0oEk4iR1YTFVEPfph3aU9frVE/y5ob73fP2+u+gCliAB/jAS8oHFkCzqLwHHIEgow6S0cJKyK+AC7SkRsBZ5vZo7sRLyJ2UeQe4SWau02Ari11FZiyZjU6DpyxuKTK2ZB5ZgYZisUocYcn4zgrUc0hUDGU8FfSk0iV+yFPT8jawE/ka9a3+GZv4DbsQ7sQYNWBJ/H30TcoBJiK/AwPTcoCVNJiVIYfwygOgU1aDP1+JDprcFP0XVYAPYEI0b/Y+EU4AAAAASUVORK5CYII=";
        },
        from(message) {
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
            this.chats.forEach(element => {
                if (element.id == chatId) {
                    this.activeChat = element
                }
            })

            axios.get('/load-messages/' + chatId)
                .then((response) => {
                    if (response.data.status) {
                        this.messages = response.data.messages
                        let that = this
                        this.chats.forEach(element => {
                            if (element.id == chatId) {
                                element.isNewMessage = false;
                            }
                        });
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });

        },
        sendMessage() {
            if (this.activeChatId > 0 && this.message != "" && this.editMessageId == 0) {
                let data = {
                    "chatId": this.activeChatId,
                    "message": this.message,
                    "files_": this.files

                }
                axios.post('/send-message', data)
                    .then((response) => {
                        if (response.data.status) {
                            response.data.message.isEdit = new Date()
                            this.messages.push(response.data.message)
                            this.message = ""
                            this.files = []
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });                
            }
            if (this.activeChatId > 0 && this.message != "" && this.editMessageId != 0) {
                let data = {
                    "message": this.message,
                    "files_": this.files

                }
                axios.put('/update-message/' + this.editMessageId, data)
                    .then((response) => {
                        if (response.data.status) {
                            response.data.message.isEdit = new Date()
                            let that = this
                            for (var index in this.messages) {
                                console.log(this.messages[index], that.editMessageId)
                                if (this.messages[index].id == that.editMessageId) {
                                    this.messages[index] = response.data.message
                                }
                            }
                            this.message = ""
                            this.files = []
                            this.editMessageId = 0;
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
        },
        listen() {
            console.log("listen")
            Echo.join('chat.' + this.userId)
                .listen('NewMessage', (e) => {
                    if (e.messsage.chat_id == this.activeChatId) {
                        this.messages.push(e.message);
                    } else {
                        this.chats.forEach(element => {
                            if (element.id == e.message.chat_id) {
                                element.isNewMessage = true;
                            }
                        });
                    }
                        
                })
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
        &.new-message::after {
            content: '';
            display: inline-block;
            margin-top: 9px;
            margin-left: 10px;
            width: 5px;
            height: 5px;
            -moz-border-radius: 7.5px;
            -webkit-border-radius: 7.5px;
            border-radius: 7.5px;
            background-color: #69b6d5;
        }

        &.online::before {
            content: '';
            display: inline-block;
            margin-top: 9px;
            margin-right: 10px;
            width: 5px;
            height: 5px;
            -moz-border-radius: 7.5px;
            -webkit-border-radius: 7.5px;
            border-radius: 7.5px;
            background-color: #69d592;
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
            span {
                cursor:pointer;
            }
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
    .admin-pannel {
        padding: 10px;;
    }
</style>