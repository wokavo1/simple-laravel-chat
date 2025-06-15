import { createApp } from 'vue';
import TestComponent from './components/TestComponent.vue';
import BroadcastChatComponent from './components/chat/BroadcastChatComponent.vue';
import PrivateChatComponent from './components/chat/PrivateChatComponent.vue';

const app = createApp({});

app.component('test-component', TestComponent);
app.component('broadcast-chat', BroadcastChatComponent);
app.component('private-chat', PrivateChatComponent);

app.mount("#app");