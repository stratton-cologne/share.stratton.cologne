<template>
    <section class="grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
        <div class="rounded-3xl border border-white/10 bg-slate-900/60 p-8">
            <h1 class="text-3xl font-semibold">Share-Link</h1>
            <p class="mt-2 text-slate-400">Teile diesen Link, damit Empfänger die Datei herunterladen können.</p>

            <div v-if="loading" class="mt-8 text-slate-400">Lade Uploadinfos ...</div>
            <div v-else-if="error" class="mt-8 rounded-2xl border border-rose-500/40 bg-rose-500/10 p-5 text-rose-200">
                {{ error }}
            </div>

            <div v-else class="mt-8 space-y-6">
                <div class="rounded-2xl border border-white/10 bg-slate-950/50 p-5">
                    <p class="text-sm text-slate-400">Dateien</p>
                    <p class="text-xl font-medium">{{ files.length }} Datei(en)</p>
                    <p class="mt-1 text-sm text-slate-400">Gesamtgröße: {{ totalSize }}</p>
                </div>

                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                    <p class="text-xs uppercase tracking-widest text-slate-400">Share-Link</p>
                    <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-center">
                        <input
                            class="w-full flex-1 rounded-xl border border-white/10 bg-slate-900/60 px-3 py-2 text-sm text-slate-200"
                            :value="batch?.share_url" readonly />
                        <button class="rounded-xl bg-white/10 px-4 py-2 text-sm font-semibold text-slate-200"
                            @click="copyLink">
                            Kopieren
                        </button>
                    </div>
                    <p v-if="copied" class="mt-2 text-xs text-emerald-300">Link kopiert.</p>
                </div>

                <div class="flex flex-wrap gap-4">
                    <button
                        class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-sky-500 to-purple-500 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-500/20"
                        :disabled="batchRemaining !== null && batchRemaining <= 0"
                        :class="batchRemaining !== null && batchRemaining <= 0 ? 'opacity-60 cursor-not-allowed' : ''"
                        @click="downloadZip">
                        Alle als ZIP herunterladen
                    </button>
                    <p v-if="batchRemaining !== null" class="text-sm text-slate-400">
                        Verbleibende Downloads: {{ batchRemaining }}
                    </p>
                </div>

                <div class="grid gap-3 md:grid-cols-2">
                    <div v-for="item in files" :key="item.token"
                        class="rounded-2xl border border-white/10 bg-slate-950/50 p-4">
                        <p class="text-sm text-slate-400">Datei</p>
                        <p class="text-lg font-medium">{{ item.original_name }}</p>
                        <p class="mt-1 text-sm text-slate-400">Größe: {{ formatBytes(item.size) }}</p>
                        <p v-if="item.max_downloads !== null" class="mt-1 text-sm text-slate-400">
                            Verbleibende Downloads: {{ Math.max(0, item.max_downloads - item.download_count) }}
                        </p>
                        <div class="mt-3 flex flex-wrap gap-3">
                            <button
                                class="rounded-xl border border-white/10 px-4 py-2 text-xs font-semibold text-slate-200"
                                :disabled="item.max_downloads !== null && item.download_count >= item.max_downloads"
                                :class="item.max_downloads !== null && item.download_count >= item.max_downloads ? 'opacity-60 cursor-not-allowed' : ''"
                                @click="downloadFile(item.token)">
                                Datei herunterladen
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="rounded-3xl border border-white/10 bg-slate-900/60 p-6">
                <h2 class="text-xl font-semibold">Sicher teilen</h2>
                <p class="mt-2 text-sm text-slate-400">
                    Dieser Link ist privat. Du kannst Download-Limits oder Ablaufzeiten definieren.
                </p>
                <dl class="mt-4 space-y-2 text-sm text-slate-300">
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Ablauf</dt>
                        <dd>{{ batch?.expires_at ?? 'Kein Ablauf' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900/80 to-slate-950/80 p-6">
                <h2 class="text-xl font-semibold">Weiterleiten</h2>
                <p class="mt-2 text-sm text-slate-400">Empfänger sehen eine klare Download-Ansicht.</p>
            </div>
        </aside>
    </section>
</template>

<script setup lang="ts">
import { computed, ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import { formatBytes } from '@/utils/format';

interface FileInfo {
    token: string;
    share_url: string;
    download_url: string;
    original_name: string;
    size: number;
    download_count: number;
    max_downloads: number | null;
}

interface BatchInfo {
    token: string;
    share_url: string;
    download_url: string;
    expires_at: string | null;
    max_downloads: number | null;
}

const route = useRoute();
const token = String(route.params.token ?? '');
const batch = ref<BatchInfo | null>(null);
const files = ref<FileInfo[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);
const copied = ref(false);

const totalSize = computed(() => {
    const total = files.value.reduce((sum, item) => sum + item.size, 0);
    return formatBytes(total);
});

const batchRemaining = computed(() => {
    const limited = files.value.filter((file) => file.max_downloads !== null);
    if (!limited.length) return null;
    const remainingValues = limited.map((file) => Math.max(0, file.max_downloads! - file.download_count));
    return Math.min(...remainingValues);
});

const fetchInfo = async () => {
    loading.value = true;
    error.value = null;
    try {
        const response = await axios.get(`/api/batches/${token}`);
        batch.value = response.data?.batch ?? null;
        files.value = response.data?.files ?? [];
    } catch (fetchError: any) {
        error.value = fetchError?.response?.data?.message ?? 'Upload konnte nicht geladen werden.';
    } finally {
        loading.value = false;
    }
};

const copyLink = async () => {
    if (!batch.value) return;
    await navigator.clipboard.writeText(batch.value.share_url);
    copied.value = true;
    setTimeout(() => (copied.value = false), 2000);
};

const downloadZip = () => {
    if (!token) return;
    if (batchRemaining.value !== null && batchRemaining.value <= 0) {
        error.value = 'Dieses Download-Limit wurde erreicht.';
        return;
    }
    window.location.href = `/api/batches/${token}/download`;
};

const downloadFile = (fileToken: string) => {
    const file = files.value.find((item) => item.token === fileToken);
    if (file && file.max_downloads !== null && file.download_count >= file.max_downloads) {
        error.value = 'Dieses Download-Limit wurde erreicht.';
        return;
    }
    window.location.href = `/api/files/${fileToken}/download`;
};

onMounted(fetchInfo);
</script>
