import { createRouter, createWebHistory } from "vue-router";
import UploadPage from "@/pages/UploadPage.vue";
import ProgressPage from "@/pages/ProgressPage.vue";
import SharePage from "@/pages/SharePage.vue";
import DownloadPage from "@/pages/DownloadPage.vue";
import ImpressumPage from "@/pages/ImpressumPage.vue";
import DatenschutzPage from "@/pages/DatenschutzPage.vue";
import AdminPage from "@/pages/AdminPage.vue";
import AdminLoginPage from "@/pages/AdminLoginPage.vue";
import AdminUsersPage from "@/pages/AdminUsersPage.vue";
import AdminProfilePage from "@/pages/AdminProfilePage.vue";
import axios from "axios";

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
        {
            path: "/admin",
            name: "admin",
            component: AdminPage,
        },
        {
            path: "/admin/login",
            name: "admin-login",
            component: AdminLoginPage,
        },
        {
            path: "/admin/users",
            name: "admin-users",
            component: AdminUsersPage,
        },
        {
            path: "/admin/profile",
            name: "admin-profile",
            component: AdminProfilePage,
        },
    ],
});

const adminRoutes = new Set(["/admin", "/admin/users", "/admin/profile"]);

router.beforeEach(async (to) => {
    if (to.path === "/admin/login") return true;
    if (!adminRoutes.has(to.path)) return true;

    try {
        const response = await axios.get("/api/admin/auth/me");
        if (response.data?.authenticated && response.data?.user?.is_admin) {
            return true;
        }
    } catch {
        // ignore
    }

    return { path: "/admin/login" };
});

export default router;
