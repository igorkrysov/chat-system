<template>
    <div class="root">
        <div class="chats">
            <div class="chat">
                <div class="form-group">
                    <select class="custom-select" multiple size="1" v-model="newChat">
                        <option v-for="user in users" v-bind:key="user.id" :value="user.id" multiple>{{ user.name }}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" :value="name" placeholder="name" required>
                </div>
                <div class="form-group">
                    <button class="btn btn-success btn-create" @click="createChat()"  :disabled="newChat.length < 1">create chat</button>
                </div>
            </div>
            <div class="chat" v-for="chat in chats" v-bind:key="chat.id" @click.prevent="loadMessages">
                {{ chat.name }}
            </div>
        </div>
        <div class="message-data">
            <div class="messages">
            <div class="message" v-for="message in messages" v-bind:key="message.id"></div>
        </div>
        <div class="new-message">
            <div class="form-group">
                <textarea v-model="message"
                            @keydown.enter.exact.prevent
                            @keyup.enter.exact="sendMessage"
                            @keydown.enter.shift.exact="newMessageLine"
                            class="form-control"
                            ></textarea>
            </div>
            <div class="form-group">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" accept=".jpg, .jpeg, .png" name="img" ref="photo" v-on:change="uploadFile()">
                    <label class="custom-file-label" for="validatedCustomFile">Choose file...</label>
                    <div class="invalid-feedback">Example invalid custom file feedback</div>
                </div>
            </div>
            
            <button class="btn btn-primary">Send</button>
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
            message: "",
            newChat: [],
            name: ''
        }
    },
    mounted() {
        this.loadUsers();
        this.loadChats();
    },
    methods: {
        createChat() {
            if (this.newChat.lenght > 0) {

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
        loadMessages() {

        },
        sendMessage() {

        },
        newMessageLine() {
             this.message += "\n";
        },
        uploadFile() {

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
        min-height: 20px;
        .btn-create {
            width: 100%;
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
    .new-message {
        border: 1px solid #E9E9E9;
    }
</style>