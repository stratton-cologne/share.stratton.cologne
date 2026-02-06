<template>
    <section class="space-y-6">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Admin</p>
                <h1 class="text-3xl font-semibold text-white">Uploads verwalten</h1>
            </div>
            <div class="flex flex-wrap gap-2">
                <RouterLink class="rounded-xl border border-white/10 px-4 py-2 text-sm font-semibold text-slate-200"
                    to="/admin/users">
                    User
                </RouterLink>
                <RouterLink class="rounded-xl border border-white/10 px-4 py-2 text-sm font-semibold text-slate-200"
                    to="/admin/profile">
                    Profil & MFA
                </RouterLink>
                <button class="rounded-xl border border-white/10 px-4 py-2 text-sm font-semibold text-slate-200"
                    @click="loadBatches">
                    Aktualisieren
                </button>
                <button class="rounded-xl border border-white/10 px-4 py-2 text-sm font-semibold text-slate-200"
                    @click="logout">
                    Abmelden
                </button>
            </div>
        </div>

        <div v-if="error" class="rounded-2xl border border-rose-500/40 bg-rose-500/10 p-5 text-rose-200">
            {{ error }}
        </div>

        <div class="rounded-3xl border border-white/10 bg-slate-900/60 p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold">Batches</h2>
                <p class="text-sm text-slate-400">{{ batches.length }} Einträge</p>
            </div>

            <div class="mt-6 overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="text-xs uppercase tracking-widest text-slate-500">
                        <tr>
                            <th class="py-3">Batch</th>
                            <th class="py-3">Dateien</th>
                            <th class="py-3">Uploader</th>
                            <th class="py-3">Ablauf</th>
                            <th class="py-3">Aktionen</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-200">
                        <tr v-if="loading">
                            <td colspan="5" class="py-6 text-center text-slate-400">Lade Daten ...</td>
                        </tr>
                        <tr v-else-if="!batches.length">
                            <td colspan="5" class="py-6 text-center text-slate-400">Keine Uploads gefunden.</td>
                        </tr>
                        <tr v-for="batch in batches" :key="batch.id" class="border-t border-white/10">
                            <td class="py-4">
                                <div class="font-medium">{{ batch.token }}</div>
                                <a class="text-xs text-sky-400 hover:text-sky-300" :href="`/share/${batch.token}`"
                                    target="_blank" rel="noopener">
                                    Share-Link öffnen
                                </a>
                            </td>
                            <td class="py-4">{{ batch.files_count }}</td>
                            <td class="py-4">{{ batch.uploader_email }}</td>
                            <td class="py-4">{{ formatDate(batch.expires_at) }}</td>
                            <td class="py-4">
                                <div class="flex flex-wrap gap-2">
                                    <div class="flex items-center gap-2">
                                        <input v-model.number="extendDays[batch.id]" type="number" min="1" max="30"
                                            class="w-20 rounded-xl border border-white/10 bg-slate-900/60 px-3 py-2 text-xs text-slate-200" />
                                        <button
                                            class="rounded-xl border border-white/10 px-3 py-2 text-xs font-semibold text-slate-200"
                                            @click="extendBatch(batch.id)">
                                            Verlängern
                                        </button>
                                    </div>
                                    <button
                                        class="rounded-xl bg-rose-500/20 px-3 py-2 text-xs font-semibold text-rose-200"
                                        @click="deleteBatch(batch.id)">
                                        Löschen
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex items-center justify-between text-xs text-slate-500">
                <button class="rounded-lg border border-white/10 px-3 py-2" :disabled="!pagination.prev_page_url"
                    @click="goToPage(pagination.prev_page_url)">
                    Zurück
                </button>
                <span>Seite {{ pagination.current_page }} / {{ pagination.last_page }}</span>
                <button class="rounded-lg border border-white/10 px-3 py-2" :disabled="!pagination.next_page_url"
                    @click="goToPage(pagination.next_page_url)">
                    Weiter
                </button>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import axios from 'axios';

interface BatchItem {
    id: number;
    token: string;
    uploader_email: string;
    files_count: number;
    expires_at: string | null;
}

interface PaginationResponse {
    data: BatchItem[];
    current_page: number;
    last_page: number;
    next_page_url: string | null;
    prev_page_url: string | null;
}

const batches = ref<BatchItem[]>([]);
const loading = ref(false);
const error = ref<string | null>(null);
const pagination = ref<PaginationResponse>({
    data: [],
    current_page: 1,
    last_page: 1,
    next_page_url: null,
    prev_page_url: null,
});
const extendDays = ref<Record<number, number>>({});
const router = useRouter();

const formatDate = (value: string | null) => {
    if (!value) return 'Kein Ablauf';
    const date = new Date(value);
    return date.toLocaleString('de-DE');
};

const loadBatches = async (url = '/api/admin/batches') => {
    loading.value = true;
    error.value = null;

    try {
        const response = await axios.get<PaginationResponse>(url);
        batches.value = response.data.data;
        pagination.value = response.data;
        response.data.data.forEach((batch) => {
            if (!extendDays.value[batch.id]) {
                extendDays.value[batch.id] = 7;
            }
        });
    } catch (err: any) {
        error.value = err?.response?.data?.message ?? 'Daten konnten nicht geladen werden.';
    } finally {
        loading.value = false;
    }
};

const goToPage = (url: string | null) => {
    if (!url) return;
    const path = url.replace(window.location.origin, '');
    loadBatches(path);
};

const extendBatch = async (batchId: number) => {
    const days = extendDays.value[batchId] ?? 7;
    try {
        await axios.post(`/api/admin/batches/${batchId}/extend`, { extend_days: days });
        await loadBatches(pagination.value.current_page ? `/api/admin/batches?page=${pagination.value.current_page}` : undefined);
    } catch (err: any) {
        error.value = err?.response?.data?.message ?? 'Verlängern fehlgeschlagen.';
    }
};

const deleteBatch = async (batchId: number) => {
    if (!confirm('Batch und alle Dateien wirklich löschen?')) return;
    try {
        await axios.delete(`/api/admin/batches/${batchId}`);
        await loadBatches(pagination.value.current_page ? `/api/admin/batches?page=${pagination.value.current_page}` : undefined);
    } catch (err: any) {
        error.value = err?.response?.data?.message ?? 'Löschen fehlgeschlagen.';
    }
};

const logout = async () => {
    await axios.post('/api/admin/auth/logout');
    await router.push('/admin/login');
};

onMounted(() => loadBatches());
</script>
