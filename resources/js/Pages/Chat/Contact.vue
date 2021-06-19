<template>
    <ui class="contacts">
        <li v-for="contact in contacts">
            <div
                :class="activeUserHighlight(contact.id)"
                @click="makeUserActive(contact.id)"
            >
                <div class="img_cont">
                    <img
                        src="https://picsum.photos/200/300"
                        class="rounded-circle user_img"
                    />
                    <span class="online_icon"></span>
                </div>
                <div class="user_info">
                    <span>{{ contact.name }}</span>
                    <p>{{ onlineStatus(contact.id) }}</p>
                </div>
            </div>
        </li>
    </ui>
</template>

<script>
export default {
    name: "Contact",
    props: ["contacts", "getActive", "onlineUsers"],
    data() {
        return {
            activeUser: localStorage.getItem("selected_user"),
        };
    },
    mounted() {},
    methods: {
        activeUserHighlight(userid) {
            return userid == this.activeUser
                ? "d-flex bd-highlight active"
                : "d-flex";
        },
        onlineStatus(userid) {
            return this.onlineUsers.includes(userid) ? "Online" : "Offline";
        },
        makeUserActive(userId) {
            this.activeUser = userId;
            this.getActive(userId);
            localStorage.setItem("selected_user", userId);
        },
    },
};
</script>

<style scoped>
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
</style>
