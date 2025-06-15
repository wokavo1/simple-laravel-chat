<template>
    <div id="messageList" class="d-flex flex-column block message-list mb-1">
        <div v-for="tmp_message in messages" class="message-item">
            {{ tmp_message.text }}
        </div>
    </div>
    <div class="d-flex flex-column block message-input">
        <input v-model="message" placeholder="Type your message here..." />
        <button @click="onMessageSend" :disabled="spamLock == true">Send</button>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'BroadcastChatComponent',
    data: function () {
        return {
            message: "",
            messages: [],
            messagesLimit: 10,
            spamLock: false,
        };
    },
    mounted: function () {
        window.Echo.channel("chat.broadcast")
            .listen(".BroadcastEvent", this.onMessageRecieve);
    },
    methods: {
        onMessageRecieve: function (event) {
            console.log("onMessageRecieve :: event = ", event);

            let message = event.message;
            if (message) {
                console.log("onMessageRecieve :: this.messages = ", this.messages);
                let messageObj = {
                    text: message,
                };
                this.messages.push(messageObj);
                let diff = this.messages.length - this.messagesLimit;
                if (diff > 0) {
                    let messagesNew = new Array(this.messagesLimit);
                    for (let i = 0; i < this.messagesLimit; i++) {
                        messagesNew[i] = this.messages[i + diff];
                    }
                    this.messages = messagesNew;
                }
            }
        },

        onMessageSend: async function () {
            console.log("onMessageSend :: this.message = ", this.message);

            this.spamLock = true;
            try {
                await axios.post("/chat/broadcast", {
                    message: this.message,
                });
            } catch (e) {
                console.error(e);
            }
            this.spamLock = false;
        },
    },
};
</script>

<style>
.block {
    border-radius: 10px;
    padding: 10px;
}

.message-list {
    background-color: wheat;
}

.message-item {
    background-color: white;
    border-radius: 5px;
    padding: 5px;
    margin-bottom: 5px;
}

.message-input {
    background-color: lightblue;
}
</style>