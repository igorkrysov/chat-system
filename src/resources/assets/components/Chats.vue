<template>
    <div class="root">
        <div class="chats">
            <div class="chat" v-for="chat in chats" v-bind:key="chat.id" @click.prevent="loadMessages">
                {{ chat.name }}
            </div>
        </div>
        <div class="messages">
            <div class="message" v-for="message in messages" v-bind:key="message.id"></div>
        </div>
        <div class="new-message">
            <div class="">
                <label>
                    <input type="file" accept=".jpg, .jpeg, .png" name="img" ref="photo" v-on:change="uploadFile()"/>
                    <div class="personal-area-file-img" style="background-image: url('img/personal-area/clipboard.png')"></div>
                </label>
            </div>
            <textarea v-model="message"
                            @keydown.enter.exact.prevent
                            @keyup.enter.exact="sendMessage"
                            @keydown.enter.shift.exact="newMessageLine"
                            ></textarea>
            <button>Send</button>
        </div>
    </div>
    
</template>
<script>
export default {
    data() {
        return {
            chats: [],
            messages: [],
            message: ""
        }
    },
    methods: {
        loadChats() {
            
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
        flex-direction: column;
        width: 100%;
        height: 100%;
    }
    .chats {
        display: flex;
        flex-direction: row;
        width: 20%;
        border: 1px solid #E9E9E9;
        height: 100%;
    }
    .chat {
        display: flex;
        width: 100%;
        border: 1px solid #F2F2F2;
        min-height: 20px;
    }
    .messages {
        height: 80%;
        width: 80%;
        border: 1px solid #E9E9E9;
    }
    .new-message {
        border: 1px solid #E9E9E9;
        height: 20%;
    }
</style>