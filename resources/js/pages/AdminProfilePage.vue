<template>
    <section class="space-y-6">
        <div>
            <h1 class="text-3xl font-semibold text-white">Profil & MFA</h1>
            <p class="mt-2 text-sm text-slate-400">Aktiviere TOTP oder E-Mail MFA.</p>
        </div>

        <div v-if="error" class="rounded-2xl border border-rose-500/40 bg-rose-500/10 p-4 text-rose-200">{{ error }}
        </div>

        <div class="rounded-3xl border border-white/10 bg-slate-900/60 p-6 space-y-6">
            <div>
                <h2 class="text-xl font-semibold">TOTP (Authenticator App)</h2>
                <p class="text-sm text-slate-400">Scanne den QR-Code oder nutze den Secret Key.</p>
                <div class="mt-4 flex flex-col gap-4 md:flex-row md:items-center">
                    <div v-if="qrDataUrl" class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                        <img :src="qrDataUrl" alt="TOTP QR" class="h-32 w-32" />
                    </div>
                    <div class="flex-1">
                        <button class="rounded-xl border border-white/10 px-4 py-2 text-sm font-semibold text-slate-200"
                            @click="startTotp">
                            QR-Code generieren
                        </button>
                        <p v-if="totpSecret" class="mt-2 text-xs text-slate-400">Secret: {{ totpSecret }}</p>
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap gap-2">
                    <input v-model.trim="totpCode" placeholder="Code"
                        class="rounded-xl border border-white/10 bg-slate-900/60 px-3 py-2 text-sm" />
                    <button class="rounded-xl bg-emerald-500/20 px-4 py-2 text-sm text-emerald-200"
                        @click="verifyTotp">Aktivieren</button>
                    <button v-if="profile?.mfa_totp_enabled" class="rounded-xl border border-white/10 px-4 py-2 text-sm"
                        @click="disableTotp">Deaktivieren</button>
                </div>
            </div>

            <div>
                <h2 class="text-xl font-semibold">E-Mail MFA</h2>
                <div class="mt-2 flex flex-wrap gap-2">
                    <button class="rounded-xl border border-white/10 px-4 py-2 text-sm" @click="sendEmailCode">Code
                        senden</button>
                    <input v-model.trim="emailCode" placeholder="Code"
                        class="rounded-xl border border-white/10 bg-slate-900/60 px-3 py-2 text-sm" />
                    <button class="rounded-xl bg-emerald-500/20 px-4 py-2 text-sm text-emerald-200"
                        @click="verifyEmail">Aktivieren</button>
                    <button v-if="profile?.mfa_email_enabled"
                        class="rounded-xl border border-white/10 px-4 py-2 text-sm"
                        @click="disableEmail">Deaktivieren</button>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';
import axios from 'axios';
import QRCode from 'qrcode';

const profile = ref<any>(null);
const error = ref<string | null>(null);
const totpSecret = ref('');
const totpCode = ref('');
const emailCode = ref('');
const qrDataUrl = ref('');

const loadProfile = async () => {
    const response = await axios.get('/api/admin/auth/me');
    profile.value = response.data.user;
};

const startTotp = async () => {
    error.value = null;
    try {
        const response = await axios.post('/api/admin/auth/mfa/totp/setup');
        totpSecret.value = response.data.secret;
        qrDataUrl.value = await QRCode.toDataURL(response.data.otpauth_url);
    } catch (err: any) {
        error.value = err?.response?.data?.message ?? 'TOTP Setup fehlgeschlagen.';
    }
};

const verifyTotp = async () => {
    error.value = null;
    try {
        await axios.post('/api/admin/auth/mfa/totp/verify', { code: totpCode.value });
        await loadProfile();
    } catch (err: any) {
        error.value = err?.response?.data?.message ?? 'TOTP Code ungültig.';
    }
};

const disableTotp = async () => {
    await axios.post('/api/admin/auth/mfa/totp/disable');
    await loadProfile();
};

const sendEmailCode = async () => {
    error.value = null;
    try {
        await axios.post('/api/admin/auth/mfa/email/send');
    } catch (err: any) {
        error.value = err?.response?.data?.message ?? 'Code senden fehlgeschlagen.';
    }
};

const verifyEmail = async () => {
    error.value = null;
    try {
        await axios.post('/api/admin/auth/mfa/email/verify', { code: emailCode.value });
        await loadProfile();
    } catch (err: any) {
        error.value = err?.response?.data?.message ?? 'Code ungültig.';
    }
};

const disableEmail = async () => {
    await axios.post('/api/admin/auth/mfa/email/disable');
    await loadProfile();
};

onMounted(() => loadProfile());
</script>
