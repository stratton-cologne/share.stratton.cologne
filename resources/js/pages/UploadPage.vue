<template>
    <section class="grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
        <div class="rounded-3xl border border-white/10 bg-slate-900/60 p-8 shadow-xl">
            <h1 class="text-3xl font-semibold">{{ t('upload.title') }}</h1>
            <p class="mt-2 text-slate-400">{{ t('upload.subtitle') }}</p>

            <div class="mt-8 flex flex-col gap-4">
                <label
                    class="flex cursor-pointer flex-col items-center justify-center gap-3 rounded-2xl border border-dashed border-white/20 bg-slate-950/60 px-6 py-10 text-center transition hover:border-sky-400">
                    <input class="hidden" type="file" multiple @change="onFileChange" />
                    <span class="text-sm uppercase tracking-widest text-slate-400">{{ t('upload.selectFiles') }}</span>
                    <span class="text-lg font-medium">
                        {{ files.length ? t('upload.filesSelected', { count: files.length }) : t('upload.dragDrop') }}
                    </span>
                    <span class="text-xs text-slate-500">{{ t('upload.maxFile') }}</span>
                </label>

                <div v-if="files.length" class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-400">{{ t('upload.selectedFiles') }}</p>
                            <p class="text-lg font-medium">{{ t('upload.filesSelected', { count: files.length }) }}</p>
                        </div>
                        <p class="text-sm text-slate-300">{{ formattedSize }}</p>
                    </div>

                    <div class="mt-3 space-y-2">
                        <div v-for="fileItem in files" :key="fileItem.name"
                            class="flex items-center justify-between text-sm text-slate-300">
                            <span class="truncate">{{ fileItem.name }}</span>
                            <span class="text-slate-500">{{ formatBytes(fileItem.size) }}</span>
                        </div>
                    </div>

                    <div v-if="isUploading" class="mt-4">
                        <div class="flex justify-between text-xs text-slate-400">
                            <span>{{ t('upload.uploading') }}</span>
                            <span>{{ uploadProgress }}%</span>
                        </div>
                        <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-800">
                            <div class="h-full bg-gradient-to-r from-sky-500 to-purple-500"
                                :style="{ width: `${uploadProgress}%` }"></div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4 md:col-span-2">
                        <label class="text-xs uppercase tracking-widest text-slate-400">{{ t('upload.emailLabel')
                            }}</label>
                        <input v-model.trim="uploaderEmail" type="email" required
                            :placeholder="t('upload.emailPlaceholder', { email: 'info@stratton.cologne' })"
                            class="mt-2 w-full rounded-xl border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none" />
                        <p class="mt-2 text-xs text-slate-500">
                            {{ t('upload.emailHint') }}
                        </p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                        <label class="text-xs uppercase tracking-widest text-slate-400">{{ t('upload.expiresLabel')
                            }}</label>
                        <input v-model.number="expiresInDays" type="number" min="1" max="30" required
                            :placeholder="t('upload.expiresPlaceholder')"
                            class="mt-2 w-full rounded-xl border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none" />
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                        <label class="text-xs uppercase tracking-widest text-slate-400">{{
                            t('upload.maxDownloadsLabel') }}</label>
                        <input v-model.number="maxDownloads" type="number" min="1" max="1000"
                            :placeholder="t('upload.downloadsPlaceholder')"
                            class="mt-2 w-full rounded-xl border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none" />
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4 md:col-span-2">
                        <label class="flex items-start gap-3 text-sm text-slate-300">
                            <input v-model="agbAccepted" type="checkbox"
                                class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-900/60 text-sky-500 focus:ring-sky-500" />
                            <span>
                                {{ t('upload.agbAccept') }}
                                <button type="button" class="text-sky-400 hover:text-sky-300" @click="showAgb = true">
                                    {{ t('upload.agbButton') }}
                                </button>
                                .
                            </span>
                        </label>
                    </div>
                </div>

                <p v-if="error" class="text-sm text-rose-400">{{ error }}</p>

                <button
                    class="rounded-2xl bg-gradient-to-r from-sky-500 to-purple-500 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-500/20 transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="!files.length || isUploading || !uploaderEmail || !agbAccepted" @click="upload">
                    {{ t('upload.startUpload') }}
                </button>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="rounded-3xl border border-white/10 bg-slate-900/60 p-6">
                <h2 class="text-xl font-semibold">{{ t('upload.stepsTitle') }}</h2>
                <ul class="mt-4 space-y-3 text-sm text-slate-400">
                    <li class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-sky-500"></span>
                        {{ t('upload.steps.pick') }}
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-purple-500"></span>
                        {{ t('upload.steps.share') }}
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-emerald-500"></span>
                        {{ t('upload.steps.download') }}
                    </li>
                </ul>
            </div>

            <div class="rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900/80 to-slate-950/80 p-6">
                <h2 class="text-xl font-semibold">{{ t('upload.controlTitle') }}</h2>
                <p class="mt-2 text-sm text-slate-400">{{ t('upload.controlText') }}</p>
            </div>
        </aside>
    </section>

    <div v-if="showAgb" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4 py-8">
        <div class="w-full max-w-3xl rounded-3xl border border-white/10 bg-slate-950/90 p-6 text-slate-200 shadow-2xl">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-white">{{ t('upload.agbModalTitle') }}</h2>
                <button type="button" class="text-slate-400 hover:text-white" @click="showAgb = false">
                    {{ t('upload.close') }}
                </button>
            </div>
            <div class="mt-4 max-h-[60vh] space-y-4 overflow-y-auto text-sm text-slate-300">
                <p>{{ t('upload.agbModalIntro') }}</p>
                <p>{{ t('upload.agbModalIllegal') }}</p>
                <p>{{ t('upload.agbModalEnforcement') }}</p>
                <p>{{ t('upload.agbModalContact', { email: 'legal@stratton.cologne' }) }}</p>
            </div>
            <div class="mt-6 flex justify-end">
                <button
                    class="rounded-xl bg-gradient-to-r from-sky-500 to-purple-500 px-4 py-2 text-sm font-semibold text-white"
                    type="button" @click="showAgb = false">
                    {{ t('upload.understood') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { formatBytes } from '@/utils/format';
import { useI18n } from 'vue-i18n';

const router = useRouter();
const files = ref<File[]>([]);
const isUploading = ref(false);
const uploadProgress = ref(0);
const error = ref<string | null>(null);
const expiresInDays = ref<number | null>(7);
const maxDownloads = ref<number | null>(null);
const uploaderEmail = ref('');
const agbAccepted = ref(false);
const showAgb = ref(false);
const { t } = useI18n();

const formattedSize = computed(() => {
    const total = files.value.reduce((sum, item) => sum + item.size, 0);
    return total ? formatBytes(total) : '0 B';
});

const onFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const selected = target.files ? Array.from(target.files) : [];
    files.value = selected;
    uploadProgress.value = 0;
    error.value = null;
};

const upload = async () => {
    if (!files.value.length) return;

    isUploading.value = true;
    uploadProgress.value = 0;
    error.value = null;

    try {
        const formData = new FormData();
        files.value.forEach((fileItem) => {
            formData.append('files[]', fileItem);
        });
        formData.append('uploader_email', uploaderEmail.value);
        if (expiresInDays.value) formData.append('expires_in_days', String(expiresInDays.value));
        if (maxDownloads.value) formData.append('max_downloads', String(maxDownloads.value));

        const response = await axios.post('/api/files', formData, {
            onUploadProgress: (progressEvent) => {
                if (!progressEvent.total) return;
                uploadProgress.value = Math.round((progressEvent.loaded / progressEvent.total) * 100);
            },
        });

        sessionStorage.setItem('last_upload', JSON.stringify(response.data));
        window.dispatchEvent(new Event('stratton-upload-ready'));
        await router.push('/progress');
    } catch (uploadError: any) {
        error.value = uploadError?.response?.data?.message ?? 'Upload fehlgeschlagen. Bitte erneut versuchen.';
    } finally {
        isUploading.value = false;
    }
};
</script>
