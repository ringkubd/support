require("./bootstrap");

// Import modules...
import { createApp, h } from "vue";
import {
    App as InertiaApp,
    plugin as InertiaPlugin,
} from "@inertiajs/inertia-vue3";
import { InertiaProgress } from "@inertiajs/progress";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

const el = document.getElementById("app");

// check if an element exists in array using a comparer function
// comparer : function(currentElement)
Array.prototype.inArray = function (comparer) {
    for (var i = 0; i < this.length; i++) {
        if (comparer(this[i])) return true;
    }
    return false;
};

// adds an element to the array if it does not already exist using a comparer
// function
Array.prototype.pushIfNotExist = function (element, comparer) {
    if (!this.inArray(comparer)) {
        this.push(element);
    }
};

createApp({
    render: () =>
        h(InertiaApp, {
            initialPage: JSON.parse(el.dataset.page),
            resolveComponent: (name) => require(`./Pages/${name}`).default,
        }),
})
    .mixin({
        props: ["user"],
        data() {
            return {
                userOnline: [],
                PresenceChannel: [],
            };
        },
        methods: {
            route,
            EventListen() {
                this.PresenceChannel = Echo.join("chat")
                    .here((users) => {})
                    .joining((user) => {
                        axios.put("/users/" + user.id + "/update", {});
                    })
                    .leaving((user) => {
                        //this.removeUser(user.id);
                        axios.put("/users/" + user.id + "/offline");
                    })
                    .listen("UserOnline", (e) => {
                        this.pushIfNotExist(e.user.id);
                    })
                    .listen("UserOfflineEvent", (e) => {
                        this.removeUser(e.user.id);
                    });
            },
            removeUser(user) {
                this.userOnline = this.userOnline.filter((u) => u !== user);
            },
            unique(value, index, self) {
                return self.indexOf(value) === index;
            },
            pushIfNotExist(element) {
                this.userOnline.pushIfNotExist(element, function (e) {
                    return element === e;
                });
            },
        },
        mounted() {},
        unmounted() {},
        created() {
            if (this.user != undefined) {
                this.EventListen();
            }
            let _this = this;
        },
    })
    .use(InertiaPlugin)
    .component("font-awesome-icon", FontAwesomeIcon)
    .mount(el);

InertiaProgress.init({ color: "#4B5563" });
