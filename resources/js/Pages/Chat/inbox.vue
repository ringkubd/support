<template>
    <app-layout>
        <template #header>
            <h2>Inbox</h2>
        </template>
        <div class="container-fluid h-100">
            <div class="row justify-content-center h-100">
                <div class="col-md-4 col-xl-3 chat">
                    <div class="card mb-sm-3 mb-md-0 contacts_card">
                        <div class="card-header">
                            <div class="input-group">
                                <input
                                    type="text"
                                    placeholder="Search..."
                                    name=""
                                    class="form-control search"
                                />
                                <div class="input-group-prepend">
                                    <span class="input-group-text search_btn">
                                        <font-awesome-icon
                                            :icon="['fas', 'search']"
                                        />
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body contacts_body">
                            <Contact
                                :onlineUsers="userOnline"
                                :contacts="contacts"
                                :getActive="makeUserActive"
                                :activeUserInfo="activeUserInfo"
                            ></Contact>
                        </div>
                        <div class="card-footer"></div>
                    </div>
                </div>
                <div class="col-md-8 col-xl-6 chat">
                    <div class="card">
                        <div class="card-header msg_head">
                            <div class="d-flex bd-highlight">
                                <div class="img_cont">
                                    <img
                                        src="https://picsum.photos/200/300"
                                        class="rounded-circle user_img"
                                    />
                                    <span class="online_icon"></span>
                                </div>
                                <div class="user_info">
                                    <span>Chat with</span>
                                    <p v-if="conversation">
                                        {{ conversation.messages.length }}
                                    </p>
                                </div>
                                <div class="video_cam">
                                    <span><i class="fas fa-video"></i></span>
                                    <span><i class="fas fa-phone"></i></span>
                                </div>
                            </div>
                            <span id="action_menu_btn"
                                ><i class="fas fa-ellipsis-v"></i
                            ></span>
                            <div class="action_menu">
                                <ul>
                                    <li>
                                        <i class="fas fa-user-circle"></i> View
                                        profile
                                    </li>
                                    <li>
                                        <i class="fas fa-users"></i> Add to
                                        close friends
                                    </li>
                                    <li>
                                        <i class="fas fa-plus"></i> Add to group
                                    </li>
                                    <li><i class="fas fa-ban"></i> Block</li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body msg_card_body">
                            <div
                                v-for="message in chatMessages"
                                :key="message.id"
                                :class="leftRight(message.to_user)"
                            >
                                <div class="img_cont_msg">
                                    <img
                                        src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg"
                                        class="rounded-circle user_img_msg"
                                    />
                                </div>
                                <div class="msg_cotainer">
                                    {{ message.body }}
                                    <span class="msg_time">{{
                                        message.created_at
                                    }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="input-group">
                                <div class="input-group-append">
                                    <span class="input-group-text attach_btn"
                                        ><font-awesome-icon
                                            :icon="['fas', 'paperclip']"
                                    /></span>
                                </div>
                                <textarea
                                    @keydown="isTyping"
                                    @keyup="notTyping"
                                    v-model="message"
                                    name=""
                                    class="form-control type_msg"
                                    placeholder="Type your message..."
                                    @keydown.enter="sendMessage"
                                ></textarea>
                                <div class="input-group-append">
                                    <span
                                        @click="sendMessage"
                                        class="input-group-text send_btn"
                                        ><font-awesome-icon
                                            :icon="['fas', 'location-arrow']"
                                    /></span>
                                </div>
                                <span
                                    v-if="typing"
                                    class="help-block"
                                    style="font-style: italic"
                                >
                                    @{{ user.name }} is typing...
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Contact from "@/Pages/Chat/Contact";
import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faSearch,
    faPaperclip,
    faLocationArrow,
} from "@fortawesome/free-solid-svg-icons";
library.add(faSearch, faPaperclip, faLocationArrow);

export default {
    name: "inbox",
    components: { Contact, AppLayout },
    props: ["friends"],
    data() {
        return {
            contacts: this.friends,
            activeUser: localStorage.getItem("selected_user"),
            conversation: false,
            activeUserProfile: false,
            message: null,
            typing: false,
            selectedConversation: localStorage.getItem("conversation_id"),
            chats: [],
        };
    },
    mounted() {
        this.getUserMessages();
        this.activeUserInfo();
    },
    methods: {
        listen() {},
        makeUserActive(userid) {
            this.activeUser = userid;
            this.getUserMessages();
            //this.receiveMessage();
        },
        getUserMessages() {
            if (this.activeUser != 0) {
                axios
                    .get("/get_conversation/" + this.activeUser)
                    .then((response) => {
                        this.conversation = response.data;
                        this.chats = response.data.messages;
                        //this.receiveMessage();
                    });
            }
        },
        activeUserInfo() {
            this.activeUserProfile = this.friends.filter((u) => {
                return u.id == this.activeUser ? u : false;
            });
        },
        leftRight(userid) {
            return userid == this.activeUser
                ? "d-flex justify-content-start mb-4"
                : "d-flex justify-content-end mb-4";
        },
        isTyping() {
            let channel = Echo.private("messages." + this.conversation.id);
            setTimeout(function () {
                channel.whisper("typing", {
                    user: this.user,
                    typing: true,
                });
            }, 300);
        },
        sendMessage() {
            let newMessage = {
                conversation_id: this.conversation.id,
                body: this.message,
                to_user: this.activeUser,
                from_user: this.user.id,
            };
            this.conversation.messages.push(newMessage);
            axios.post("/inbox", newMessage).then(function (res) {});
            this.message = "";
        },
        receiveMessage() {
            let conversationId =
                this.conversation.id != undefined
                    ? this.conversation.id
                    : this.selectedConversation;
            Echo.private("messages." + conversationId).listen(
                "MessageReceiveEvent",
                function (e) {
                    //this.conversation.messages = e.messages;
                    this.chats = [];
                    this.chats.push(e.messages);
                    console.log(e);
                }
            );
        },
    },
    created() {
        this.receiveMessage();
        console.log(this.selectedConversation);
        if (this.selectedConversation) {
            Echo.private("messages." + this.selectedConversation)
                .listen("MessageReceiveEvent", function (e) {
                    this.chats.push(e.messages);
                    console.log(e);
                })
                .listenForWhisper("typing", (e) => {
                    this.user = e.user;
                    this.typing = e.typing;
                    console.log(e);
                    setTimeout(function () {
                        _this.typing = false;
                    }, 900);
                });
        }
    },
    computed: {
        chatMessages() {
            return this.chats;
            if (
                JSON.stringify(this.conversation.messages) !=
                JSON.stringify(this.chats)
            ) {
                console.log(this.chats, 1);
                return this.chats;
            } else {
                console.log(this.chats, 2);
                return this.conversation.messages;
            }
        },
    },
    watch: {
        chatMessages() {
            if (
                JSON.stringify(this.conversation.messages) !=
                JSON.stringify(this.chats)
            ) {
                return this.chats;
            } else {
                return this.conversation.messages;
            }
        },
    },
};
</script>

<style scoped>
body,
html {
    height: 100%;
    margin: 0;
    background: #7f7fd5;
    background: -webkit-linear-gradient(to right, #91eae4, #86a8e7, #7f7fd5);
    background: linear-gradient(to right, #91eae4, #86a8e7, #7f7fd5);
}

.chat {
    margin-top: auto;
    margin-bottom: auto;
}
.card {
    height: 500px;
    border-radius: 15px !important;
    background-color: rgba(0, 0, 0, 0.4) !important;
}
.contacts_body {
    padding: 0.75rem 0 !important;
    overflow-y: auto;
    white-space: nowrap;
}
.msg_card_body {
    overflow-y: auto;
}
.card-header {
    border-radius: 15px 15px 0 0 !important;
    border-bottom: 0 !important;
}
.card-footer {
    border-radius: 0 0 15px 15px !important;
    border-top: 0 !important;
}
.container {
    align-content: center;
}
.search {
    border-radius: 15px 0 0 15px !important;
    background-color: rgba(0, 0, 0, 0.3) !important;
    border: 0 !important;
    color: white !important;
}
.search:focus {
    box-shadow: none !important;
    outline: 0px !important;
}
.type_msg {
    background-color: rgba(0, 0, 0, 0.3) !important;
    border: 0 !important;
    color: white !important;
    height: 60px !important;
    overflow-y: auto;
}
.type_msg:focus {
    box-shadow: none !important;
    outline: 0px !important;
}
.attach_btn {
    border-radius: 15px 0 0 15px !important;
    background-color: rgba(0, 0, 0, 0.3) !important;
    border: 0 !important;
    color: white !important;
    cursor: pointer;
}
.send_btn {
    border-radius: 0 15px 15px 0 !important;
    background-color: rgba(0, 0, 0, 0.3) !important;
    border: 0 !important;
    color: white !important;
    cursor: pointer;
}
.search_btn {
    border-radius: 0 15px 15px 0 !important;
    background-color: rgba(0, 0, 0, 0.3) !important;
    border: 0 !important;
    color: white !important;
    cursor: pointer;
}
.contacts {
    list-style: none;
    padding: 0;
}
.contacts li {
    width: 100% !important;
    padding: 5px 10px;
    margin-bottom: 15px !important;
}
.active {
    background-color: rgba(0, 0, 0, 0.3);
}
.user_img {
    height: 70px;
    width: 70px;
    border: 1.5px solid #f5f6fa;
}
.user_img_msg {
    height: 40px;
    width: 40px;
    border: 1.5px solid #f5f6fa;
}
.img_cont {
    position: relative;
    height: 70px;
    width: 70px;
}
.img_cont_msg {
    height: 40px;
    width: 40px;
}
.online_icon {
    position: absolute;
    height: 15px;
    width: 15px;
    background-color: #4cd137;
    border-radius: 50%;
    bottom: 0.2em;
    right: 0.4em;
    border: 1.5px solid white;
}
.offline {
    background-color: #c23616 !important;
}
.user_info {
    margin-top: auto;
    margin-bottom: auto;
    margin-left: 15px;
}
.user_info span {
    font-size: 20px;
    color: white;
}
.user_info p {
    font-size: 10px;
    color: rgba(255, 255, 255, 0.6);
}
.video_cam {
    margin-left: 50px;
    margin-top: 5px;
}
.video_cam span {
    color: white;
    font-size: 20px;
    cursor: pointer;
    margin-right: 20px;
}
.msg_cotainer {
    margin-top: auto;
    margin-bottom: auto;
    margin-left: 10px;
    border-radius: 25px;
    background-color: #82ccdd;
    padding: 10px;
    position: relative;
}
.msg_cotainer_send {
    margin-top: auto;
    margin-bottom: auto;
    margin-right: 10px;
    border-radius: 25px;
    background-color: #78e08f;
    padding: 10px;
    position: relative;
}
.msg_time {
    position: absolute;
    left: 0;
    bottom: -15px;
    color: rgba(255, 255, 255, 0.5);
    font-size: 10px;
}
.msg_time_send {
    position: absolute;
    right: 0;
    bottom: -15px;
    color: rgba(255, 255, 255, 0.5);
    font-size: 10px;
}
.msg_head {
    position: relative;
}
#action_menu_btn {
    position: absolute;
    right: 10px;
    top: 10px;
    color: white;
    cursor: pointer;
    font-size: 20px;
}
.action_menu {
    z-index: 1;
    position: absolute;
    padding: 15px 0;
    background-color: rgba(0, 0, 0, 0.5);
    color: white;
    border-radius: 15px;
    top: 30px;
    right: 15px;
    display: none;
}
.action_menu ul {
    list-style: none;
    padding: 0;
    margin: 0;
}
.action_menu ul li {
    width: 100%;
    padding: 10px 15px;
    margin-bottom: 5px;
}
.action_menu ul li i {
    padding-right: 10px;
}
.action_menu ul li:hover {
    cursor: pointer;
    background-color: rgba(0, 0, 0, 0.2);
}
@media (max-width: 576px) {
    .contacts_card {
        margin-bottom: 15px !important;
    }
}
</style>
