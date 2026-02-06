import { createRouter, createWebHistory } from "vue-router";
import UploadPage from "@/pages/UploadPage.vue";
import ProgressPage from "@/pages/ProgressPage.vue";
import SharePage from "@/pages/SharePage.vue";
import DownloadPage from "@/pages/DownloadPage.vue";
import ImpressumPage from "@/pages/ImpressumPage.vue";
import DatenschutzPage from "@/pages/DatenschutzPage.vue";

const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: "/", name: "upload", component: UploadPage },
        {
            path: "/progress",
            name: "progress",
            component: ProgressPage,
            beforeEnter: () => {
                const hasUpload = Boolean(
                    sessionStorage.getItem("last_upload"),
                );
                return hasUpload ? true : { path: "/" };
            },
        },
        {
            path: "/share/:token",
            name: "share",
            component: SharePage,
            props: true,
        },
        {
            path: "/download/:token",
            name: "download",
            component: DownloadPage,
            props: true,
        },
        {
            path: "/impressum",
            name: "impressum",
            component: ImpressumPage,
        },
        {
            path: "/datenschutz",
            name: "datenschutz",
            component: DatenschutzPage,
        },
    ],
});

export default router;
