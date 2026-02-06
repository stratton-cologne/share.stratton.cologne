<template>
    <section class="rounded-3xl border border-white/10 bg-slate-900/60 p-8">
        <h1 class="text-3xl font-semibold">Upload-Status</h1>

        <div v-if="uploads.length" class="mt-6 space-y-6">
            <div class="rounded-2xl border border-white/10 bg-slate-950/50 p-5">
                <p class="text-sm text-slate-400">Dateien</p>
                <p class="text-xl font-medium">{{ uploads.length }} Datei(en)</p>
                <p class="mt-1 text-sm text-slate-400">Gesamtgröße: {{ totalSize }}</p>
            </div>

            <div class="rounded-2xl border border-emerald-400/40 bg-emerald-500/10 p-5">
                <p class="text-sm text-emerald-200">Upload abgeschlossen</p>
                <p class="mt-2 text-sm text-slate-300">Dein Share-Link ist bereit.</p>
            </div>

            <div class="flex flex-wrap gap-4">
                <RouterLink v-if="batch"
                    class="rounded-2xl bg-gradient-to-r from-sky-500 to-purple-500 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-500/20"
                    :to="`/share/${batch.token}`">
                    Share-Link ansehen
                </RouterLink>
                <button v-if="batch"
                    class="rounded-2xl border border-white/10 px-6 py-3 text-sm font-semibold text-slate-200"
                    @click="copyBatchLink">
                    Share-Link kopieren
                </button>
            </div>

            <div class="grid gap-3 md:grid-cols-2">
                <div v-for="item in uploads" :key="item.token"
                    class="rounded-2xl border border-white/10 bg-slate-950/50 p-4">
                    <p class="text-sm text-slate-400">Datei</p>
                    <p class="text-lg font-medium">{{ item.original_name }}</p>
                    <p class="mt-1 text-sm text-slate-400">Größe: {{ formatBytes(item.size) }}</p>
                </div>
            </div>

            <p v-if="copied" class="text-xs text-emerald-300">Link in Zwischenablage kopiert.</p>
        </div>

        <div v-else class="mt-6 rounded-2xl border border-white/10 bg-slate-950/40 p-6 text-slate-400">
            Kein aktueller Upload gefunden. Starte einen neuen Upload.
        </div>
    </section>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { RouterLink } from 'vue-router';
import { formatBytes } from '@/utils/format';

interface UploadPayload {
    token: string;
    share_url: string;
    download_url: string;
    original_name: string;
    size: number;
}

interface BatchPayload {
    token: string;
    share_url: string;
    download_url: string;
    expires_at: string | null;
    max_downloads: number | null;
}

const raw = sessionStorage.getItem('last_upload');
const uploads = computed<UploadPayload[]>(() => {
    if (!raw) return [];
    const parsed = JSON.parse(raw);
    if (parsed?.uploads && Array.isArray(parsed.uploads)) return parsed.uploads;
    return Array.isArray(parsed) ? parsed : [];
});
const batch = computed<BatchPayload | null>(() => {
    if (!raw) return null;
    const parsed = JSON.parse(raw);
    return parsed?.batch ?? null;
});
const totalSize = computed(() => {
    const total = uploads.value.reduce((sum, item) => sum + item.size, 0);
    return formatBytes(total);
});
const copied = ref(false);

const copyBatchLink = async () => {
    if (!batch.value) return;
    await navigator.clipboard.writeText(batch.value.share_url);
    copied.value = true;
    setTimeout(() => (copied.value = false), 2000);
};
</script>
