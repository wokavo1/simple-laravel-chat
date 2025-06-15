<template>
    <div v-if="initialLoading">
        <div>Loading chat list, please wait...</div>
    </div>

    <div v-else>
        <button v-if="!showChatCreateForm" class="btn btn-dark m-1" @click="showChatCreateForm = !showChatCreateForm">
            Create chat
        </button>
        <div v-if="showChatCreateForm" class="d-flex flex-column">
            <div class="d-flex flex-row align-items-center">
                <p>Chat title</p>
                <input type="text" v-model="chatCreateTitle">
            </div>
            <div class="d-flex flex-row align-items-center">
                <button @click="onChatCreate" class="btn btn-dark m-1">
                    Create
                </button>
                <button @click="showChatCreateForm = !showChatCreateForm" class="btn btn-dark m-1">
                    Cancel
                </button>
            </div>
            <div v-if="chatCreateErrors" class="errors">
                {{ chatCreateErrors }}
            </div>
        </div>

        <div v-if="showChatDetail"
            class="d-flex flex-column">

            <button @click="onBackToChatListClick"
                class="btn btn-dark m-1">
                Back to chat list
            </button>

            <div v-if="chatDetail" 
                class="d-flex flex-column">
                TODO: display Chat Detail
            </div>
            <div v-else
                class="d-flex flex-column">
                Loading chat details, please wait...
            </div>
        </div>

        <div v-else>
            <div v-if="chats.length > 0">
                <h4>Chats:</h4>
                <div class="d-flex flex-column">
                    <div v-for="chat in chats" class="d-flex flex-row">
                        <div>{{ chat.title }}</div>
                        <button @click="onChatDetailClick(chat)">Detail</button>
                    </div>
                </div>
            </div>
            <div v-else>
                <h4>
                    Chat list is empty. To start chatting create a chat.
                </h4>
            </div>
        </div>
    </div>

</template>

<script>
import axios from 'axios';

export default {
    name: 'PrivateChatComponent',
    data: function () {
        return {
            initialLoading: true,

            showChatCreateForm: false,
            chatCreateTitle: "",
            chatCreateErrors: false,

            chats: [],

            showChatDetail: false,
            chatDetail: false,
        };
    },
    mounted: async function () {
        await this.fetchChatData();
        this.initialLoading = false;
    },
    methods: {
        fetchChatData: async function () {
            let chatData = await this.getChatDataRequest();
            console.log("mounted :: chatData = ", chatData);
            if (chatData) {
                this.chats = chatData.chats;
            }
        },

        getChatDataRequest: async function () {
            try {
                let response = await axios.get("/chat/private/data");
                console.log("getChatDataRequest :: response = ", response);
                return response.data;
            } catch (e) {
                console.error(e);
                return false;
            }
        },

        onChatCreate: async function () {
            let chatTitle = this.chatCreateTitle;

            let createResult = await this.createChatRequest(chatTitle);
            console.log("onChatCreate :: createResult = ", createResult);

            this.showChatCreateForm = false;

            await this.fetchChatData();
        },

        createChatRequest: async function (chatTitle) {
            try {
                let response = await axios.post(
                    "/chat/private/create",
                    {
                        title: chatTitle,
                    }
                );
                console.log("createChatRequest :: response = ", response);
                return response.data;
            } catch (e) {
                console.error(e);
                return false;
            }
        },

        onChatDetailClick: async function (chat) {
            console.log("onChatDetailClick :: chat = ", chat);

            this.showChatDetail = true;
            this.chatDetail = true;
        },

        onBackToChatListClick: function () {
            this.showChatDetail = false;
            this.chatDetail = false;
        },
    },
};
</script>