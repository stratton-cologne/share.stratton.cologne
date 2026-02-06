import "./bootstrap";
import axios from "axios";
import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";

const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute("content");
if (csrfToken) {
    axios.defaults.headers.common["X-CSRF-TOKEN"] = csrfToken;
}

const app = createApp(App);
app.use(router);
app.mount("#app");
