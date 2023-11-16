
require('./bootstrap');

import { createApp } from 'vue';
import chat from './components/Chat.vue';
import PrivateChat from './components/PrivateChat.vue';

const app = createApp({});// If you have any global options or properties, you can define them here

// Register the 'chat' component globally
app.component('chat', chat);
app.component('PrivateChat', PrivateChat)

// Mount the app to the specified element with ID 'app' in your HTML
app.mount("#app");
