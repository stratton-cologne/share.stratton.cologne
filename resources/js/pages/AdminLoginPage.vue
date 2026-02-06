<template>
    <section class="mx-auto max-w-md space-y-6 rounded-3xl border border-white/10 bg-slate-900/60 p-8">
        <div>
            <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Admin</p>
            <h1 class="text-3xl font-semibold text-white">Login</h1>
        </div>

        <div v-if="error" class="rounded-2xl border border-rose-500/40 bg-rose-500/10 p-4 text-rose-200">
            {{ error }}
        </div>

        <div v-if="!mfaRequired" class="space-y-4">
            <div>
                <label class="text-xs uppercase tracking-widest text-slate-400">E-Mail</label>
                <input v-model.trim="email" type="email"
                    class="mt-2 w-full rounded-xl border border-white/10 bg-slate-900/60 px-3 py-2 text-sm" />
            </div>
            <div>
                <label class="text-xs uppercase tracking-widest text-slate-400">Passwort</label>
                <input v-model.trim="password" type="password"
                    class="mt-2 w-full rounded-xl border border-white/10 bg-slate-900/60 px-3 py-2 text-sm" />
            </div>
            <button
                class="w-full rounded-2xl bg-gradient-to-r from-sky-500 to-purple-500 px-6 py-3 text-sm font-semibold text-white"
                @click="login">
                Einloggen
            </button>
        </div>

        <div v-else class="space-y-4">
            <p class="text-sm text-slate-300">Bitte MFA bestätigen.</p>

            <div v-if="methods.totp" class="space-y-3">
                <label class="text-xs uppercase tracking-widest text-slate-400">TOTP Code</label>
                <input v-model.trim="totpCode" type="text"
                    class="w-full rounded-xl border border-white/10 bg-slate-900/60 px-3 py-2 text-sm" />
                <button class="w-full rounded-2xl border border-white/10 px-6 py-3 text-sm font-semibold text-slate-200"
                    @click="verifyTotp">
                    TOTP verifizieren
                </button>
            </div>

            <div v-if="methods.email" class="space-y-3">
                <label class="text-xs uppercase tracking-widest text-slate-400">E-Mail Code</label>
                <div class="flex gap-2">
                    <input v-model.trim="emailCode" type="text"
                        class="flex-1 rounded-xl border border-white/10 bg-slate-900/60 px-3 py-2 text-sm" />
                    <button class="rounded-xl border border-white/10 px-3 py-2 text-xs" @click="sendEmailCode">Code
                        senden</button>
                </div>
                <button class="w-full rounded-2xl border border-white/10 px-6 py-3 text-sm font-semibold text-slate-200"
                    @click="verifyEmailCode">
                    E-Mail Code verifizieren
                </button>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const email = ref('');
const password = ref('');
const error = ref<string | null>(null);
const mfaRequired = ref(false);
const methods = ref({ totp: false, email: false });
const totpCode = ref('');
const emailCode = ref('');

const login = async () => {
    error.value = null;
    try {
        await refreshCsrf();
        const response = await axios.post('/api/admin/auth/login', { email: email.value, password: password.value });
        if (response.data?.mfa_required) {
            mfaRequired.value = true;
            methods.value = response.data.methods;
            if (methods.value.email && !methods.value.totp) {
                await sendEmailCode();
            }
            return;
        }
        await refreshCsrf();
        await router.push('/admin');
    } catch (err: any) {
        error.value = err?.response?.data?.message ?? 'Login fehlgeschlagen.';
    }
};

const verifyTotp = async () => {
    error.value = null;
    try {
        await refreshCsrf();
        await axios.post('/api/admin/auth/mfa/login/totp', { code: totpCode.value });
        await refreshCsrf();
        await router.push('/admin');
    } catch (err: any) {
        error.value = err?.response?.data?.message ?? 'TOTP fehlgeschlagen.';
    }
};

const sendEmailCode = async () => {
    error.value = null;
    try {
        await refreshCsrf();
        await axios.post('/api/admin/auth/mfa/login/email/send');
    } catch (err: any) {
        error.value = err?.response?.data?.message ?? 'Code senden fehlgeschlagen.';
    }
};

const verifyEmailCode = async () => {
    error.value = null;
    try {
        await refreshCsrf();
        await axios.post('/api/admin/auth/mfa/login/email/verify', { code: emailCode.value });
        await refreshCsrf();
        await router.push('/admin');
    } catch (err: any) {
        error.value = err?.response?.data?.message ?? 'E-Mail MFA fehlgeschlagen.';
    }
};

const refreshCsrf = async () => {
    const response = await axios.get('/api/admin/auth/csrf');
    const token = response.data?.token;
    if (token) {
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) {
            meta.setAttribute('content', token);
        }
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    }
};

onMounted(() => {
    refreshCsrf();
});
</script>
