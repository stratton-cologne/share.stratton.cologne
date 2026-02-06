import "./bootstrap";
import axios from "axios";
import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import i18n from "./i18n";

const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute("content");
if (csrfToken) {
    axios.defaults.headers.common["X-CSRF-TOKEN"] = csrfToken;
}

const app = createApp(App);
app.use(router);
app.use(i18n);
app.mount("#app");

axios
    .get("/api/admin/auth/csrf")
    .then((response) => {
        const token = response.data?.token;
        if (token) {
            const meta = document.querySelector('meta[name="csrf-token"]');
            if (meta) {
                meta.setAttribute("content", token);
            }
            axios.defaults.headers.common["X-CSRF-TOKEN"] = token;
        }
    })
    .catch(() => {
        // ignore
    });
