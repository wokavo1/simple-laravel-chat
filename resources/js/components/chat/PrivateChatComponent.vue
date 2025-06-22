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

        <div v-if="showDetailChat" class="d-flex flex-column">

            <button @click="onBackToChatListClick" class="btn btn-dark m-1">
                Back to chat list
            </button>

            <div v-if="detailChat" class="row">
                <div class="col-4 d-flex flex-column">
                    <div class="fancy-border m-1 w-100">
                        <div>
                            Chat users:
                        </div>
                        <div class="d-flex flex-column">

                            <div v-for="user in detailChat.users" class="d-flex flex-row">
                                <div>
                                    {{ user.name }}
                                </div>
                                <button @click="onChatUserDelete(user)" class="btn btn-dark m-1">
                                    Delete
                                </button>
                            </div>

                            <button v-if="!showAddChatUserForm" class="btn btn-dark m-1"
                                @click="showAddChatUserForm = !showAddChatUserForm">
                                Add user
                            </button>

                            <div v-if="showAddChatUserForm">
                                <div class="d-flex flex-row align-items-center">
                                    <p>User name</p>
                                    <input type="text" v-model="addChatUserName">
                                </div>
                                <div class="d-flex flex-row align-items-center">
                                    <button @click="onChatUserAdd" class="btn btn-dark m-1">
                                        Add
                                    </button>
                                    <button @click="showAddChatUserForm = !showAddChatUserForm"
                                        class="btn btn-dark m-1">
                                        Cancel
                                    </button>
                                </div>
                                <div v-if="addChatUserErrors" class="errors">
                                    {{ addChatUserErrors }}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-8 d-flex flex-row">
                    <div class="fancy-border m-1 w-100">
                        <div>
                            Chat messages:
                        </div>
                        <div class="d-flex flex-column">
                            <div v-for="message in detailChatMessages">
                                {{ message }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="d-flex flex-column">
                Loading chat details, please wait...
            </div>
        </div>

        <div v-else>
            <div v-if="chats.length > 0">
                <h4>Chats:</h4>
                <div class="d-flex flex-column">
                    <div v-for="chat in chats" class="d-flex flex-row">
                        <div>{{ chat.title }}</div>
                        <button @click="onChatDetailClick(chat)" class="btn btn-dark m-1">Detail</button>
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

            showDetailChat: false,
            detailChat: false,
            detailChatMessages: false,
            detailChatPage: 1,

            showAddChatUserForm: false,
            addChatUserName: "",
            addChatUserErrors: false,
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

            this.showDetailChat = true;
            this.detailChat = chat;
            this.detailChatPage = 1;

            let chatMessages = await this.getChatMessagesRequest(chat.id, this.detailChatPage);
            this.detailChatMessages = chatMessages.messages;
            console.log("onChatDetailClick :: chatMessages = ", chatMessages);
        },

        getChatMessagesRequest: async function (chat_id, page) {
            try {
                let response = await axios.post(
                    "/chat/private/messages",
                    {
                        page: page,
                        chat_id: chat_id,
                    }
                );
                console.log("getChatMessagesRequest :: response = ", response);
                return response.data;
            } catch (e) {
                console.error(e);
                return false;
            }
        },

        onChatUserAdd: async function () {
            let name = this.addChatUserName;
            let chat_id = this.detailChat.id;

            let result = this.chatUserAddRequest(name, chat_id);
            console.log("onChatUserAdd :: result = ", result);
            if (result && result.chat && result.chat.users) {
                this.detailChat.users = result.chat.users;
            }
        },

        chatUserAddRequest: async function (name, chat_id) {
            try {
                let response = await axios.post(
                    "/chat/private/addUser",
                    {
                        name: name,
                        chat_id: chat_id,
                    }
                );
                console.log("chatUserAddRequest :: response = ", response);
                return response.data;
            } catch (e) {
                console.error(e);
                return false;
            }
        },

        onChatUserDelete: async function (user) {
            console.log("onChatUserDelete :: user = ", user);
            let user_id = user.id;
            let chat_id = this.detailChat.id;
            
            let result = this.chatUserDeleteRequest(user_id, chat_id);
            console.log("onChatUserDelete :: result = ", result);
            if (result && result.chat && result.chat.users) {
                this.detailChat.users = result.chat.users;
            }
        },

        chatUserDeleteRequest: async function (user_id, chat_id) {
            try {
                let response = await axios.post(
                    "/chat/private/deleteUser",
                    {
                        user_id: user_id,
                        chat_id: chat_id,
                    }
                );
                console.log("chatUserDeleteRequest :: response = ", response);
                return response.data;
            } catch (e) {
                console.error(e);
                return false;
            }
        },

        onBackToChatListClick: function () {
            this.showDetailChat = false;
            this.detailChat = false;
            this.detailChatMessages = false;
            this.detailChatPage = 1;
        },
    },
};
</script>

<style>
.fancy-border {
    border: 1px solid black;
    border-radius: 10px;
    padding: 10px;
}
</style>