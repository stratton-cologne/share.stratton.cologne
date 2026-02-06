<template>
    <div class="min-h-screen bg-slate-950 text-slate-100">
        <header class="border-b border-white/10 bg-slate-950/80 backdrop-blur">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-5">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-sky-500 to-purple-500 text-xl font-semibold">
                        S
                    </div>
                    <div>
                        <p class="text-lg font-semibold">Stratton Share</p>
                        <p class="text-xs text-slate-400">share.stratton.cologne</p>
                    </div>
                </div>
                <nav class="flex items-center gap-4 text-sm text-slate-300">
                    <RouterLink class="hover:text-white" to="/">Upload</RouterLink>
                    <RouterLink v-if="hasUpload" class="hover:text-white" to="/progress">Progress</RouterLink>
                    <RouterLink class="hover:text-white" to="/impressum">Impressum</RouterLink>
                    <RouterLink class="hover:text-white" to="/datenschutz">Datenschutz</RouterLink>
                    <RouterLink class="hover:text-white" to="/admin">Admin</RouterLink>
                    <button
                        class="rounded-xl border border-white/10 px-3 py-1 text-xs font-semibold text-slate-200 hover:text-white"
                        type="button" :aria-pressed="isDark" @click="toggleTheme">
                        {{ isDark ? 'Hellmodus' : 'Dunkelmodus' }}
                    </button>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-6 py-10">
            <RouterView />
        </main>

        <footer class="border-t border-white/10 py-6">
            <div
                class="mx-auto flex max-w-6xl flex-col gap-3 px-6 text-xs text-slate-500 md:flex-row md:items-center md:justify-between">
                <span>© 2026 Stratton Cologne</span>
                <div class="flex items-center gap-4">
                    <RouterLink class="hover:text-white" to="/impressum">Impressum</RouterLink>
                    <RouterLink class="hover:text-white" to="/datenschutz">Datenschutz</RouterLink>
                    <button class="hover:text-white" type="button" @click="openCookieSettings">
                        Cookie-Einstellungen
                    </button>
                </div>
                <span>Sicheres Teilen großer Dateien</span>
            </div>
        </footer>

        <CookieBanner />
    </div>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue';
import { RouterLink, RouterView } from 'vue-router';
import CookieBanner from '@/components/CookieBanner.vue';

const hasUpload = ref(false);
const isDark = ref(true);
const themeKey = 'stratton-theme';

const applyTheme = (theme: 'dark' | 'light') => {
    document.documentElement.classList.toggle('theme-light', theme === 'light');
    document.documentElement.classList.toggle('theme-dark', theme === 'dark');
    isDark.value = theme === 'dark';
    localStorage.setItem(themeKey, theme);
};

const toggleTheme = () => {
    applyTheme(isDark.value ? 'light' : 'dark');
};

const syncUploadState = () => {
    hasUpload.value = Boolean(sessionStorage.getItem('last_upload'));
};

const openCookieSettings = () => {
    window.dispatchEvent(new Event('stratton-cookie-reset'));
};

onMounted(() => {
    syncUploadState();
    const savedTheme = localStorage.getItem(themeKey) as 'dark' | 'light' | null;
    const prefersDark = window.matchMedia?.('(prefers-color-scheme: dark)').matches ?? true;
    applyTheme(savedTheme ?? (prefersDark ? 'dark' : 'light'));
    window.addEventListener('stratton-upload-ready', syncUploadState);
    window.addEventListener('storage', syncUploadState);
});

onUnmounted(() => {
    window.removeEventListener('stratton-upload-ready', syncUploadState);
    window.removeEventListener('storage', syncUploadState);
});
</script>
