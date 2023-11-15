<template>
    <div class="container">
        <div class="row">
            <div class="col-4 online-users">
                <h3>Now you can private chat with users!</h3>
                <ul>
                    <li v-for="friend in users" v-if="user.id" :key="friend.id" @click="setFriendId(friend.id)">
                        {{ friend.name }}
                    </li>
                </ul>
            </div>

            <div class="col-8">
                <div class="chat card">
                    <div class="row">
                        <div class="col-4">

                        </div>

                        <div class="col-4">
                            <small v-if="friendId">Chat with {{ getFriendName(friendId) }}</small>
                        </div>

                        <div class="col-4">

                        </div>
                    </div>
                    <div class="scrollable card-body" ref="hasScrolledToBottom">
                        <template v-for="message in messages" :key="message.id">
                            <div v-if="message.user">
                                <div class="message message-receive" v-if="user.id !== message.user.id">
                                    <strong class="primary-font">
                                        {{ message.user.name }} :
                                    </strong>
                                    {{ message.message }}
                                    <div><small>{{ formatTime(message.created_at) }} | {{ formatDate(message.created_at) }}</small></div>
                                </div>
                                <div class="message message-send" v-else>
                                    <strong class="primary-font">
                                        {{ message.user.name }} :
                                    </strong>
                                    {{ message.message }}
                                    <div><small>{{ formatTime(message.created_at) }} | {{ formatDate(message.created_at) }}</small></div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="chat-form input-group">
                        <input id="btn-input" type="text" name="message" class="form-control input-sm message-" placeholder="Type your message here..." v-model="newMessage" @keyup.enter="addMessage">

                        <span class="input-group-btn">
              <button class="btn btn-primary" id="btn-chat" @click="addMessage">
                Send
              </button>
            </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted, onUpdated } from 'vue';
import axios from 'axios';

const formatTime = (timestamp) => {
    return new Date(timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

const formatDate = (timestamp) => {
    return new Date(timestamp).toLocaleDateString();
};

export default {
    props: ['user'],

    setup(props) {
        let messages = ref([]);
        let newMessage = ref('');
        let hasScrolledToBottom = ref('');
        let users = ref([]);
        let friendId = ref('');

        onMounted(() => {
            fetchUsers();
        });

        onUpdated(() => {
            scrollBottom();
        });

        Echo.private('privatechat.' + props.user.id)
            .listen('PrivateSendMessage', (e) => {
                messages.value.push({
                    message: e.message.message,
                    user: e.user
                });
            })

        const setFriendId = (id) => {
            friendId.value = id;
            console.log('FriendId:', friendId.value); // Log the friendId value
            fetchMessages(id);
        };

        const fetchMessages = async (id) => {
            friendId.value = id;
            if (friendId.value !== null) {
                try {
                    const response = await axios.get('/private-messages/' + friendId.value);
                    messages.value = response.data;
                } catch (error) {
                    console.error('Error fetching messages:', error);
                }
            }
        };

        const fetchUsers = async () => {
            try {
                const response = await axios.get('/users');
                users.value = response.data;
            } catch (error) {
                console.error('Error fetching users:', error);
            }
        };

        const addMessage = async () => {
            if (!props.user || !props.user.id) {
                console.error('User or user id is undefined');
                return;
            }

            let user_message = {
                user_id: props.user.id,
                receiver_id: friendId.value,
                message: newMessage.value,
                created_at: new Date().toISOString(),
            };

            console.log('FriendId:', friendId.value);
            console.log('Message:', newMessage.value);

            messages.value.push(user_message);

            try {
                const response = await axios.post('/privateMessages', user_message);
                console.log(response.data);
            } catch (error) {
                console.error('Error posting message:', error);
            }

            newMessage.value = '';
        };

        const scrollBottom = () => {
            if (messages.value.length > 1) {
                let el = hasScrolledToBottom.value;
                el.scrollTop = el.scrollHeight;
            }
        };

        const getFriendName = (friendId) => {
            const friend = users.value.find((user) => user.id === friendId);
            return friend ? friend.name : '';
        };


        return {
            messages,
            newMessage,
            addMessage,
            fetchMessages,
            fetchUsers,
            hasScrolledToBottom,
            formatTime,
            formatDate,
            users,
            friendId,
            setFriendId,
            getFriendName,
        };
    },
};
</script>