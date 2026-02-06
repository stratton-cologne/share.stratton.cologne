<template>
    <section class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-semibold text-white">User Verwaltung</h1>
            <button class="rounded-xl border border-white/10 px-4 py-2 text-sm font-semibold text-slate-200"
                @click="loadUsers">
                Aktualisieren
            </button>
        </div>

        <div v-if="error" class="rounded-2xl border border-rose-500/40 bg-rose-500/10 p-4 text-rose-200">{{ error }}
        </div>

        <div class="rounded-3xl border border-white/10 bg-slate-900/60 p-6 space-y-6">
            <div class="space-y-4">
                <h2 class="text-xl font-semibold">Neuen User anlegen</h2>
                <div class="grid gap-4 md:grid-cols-2">
                    <input v-model.trim="form.name" placeholder="Name"
                        class="rounded-xl border border-white/10 bg-slate-900/60 px-3 py-2 text-sm" />
                    <input v-model.trim="form.email" placeholder="E-Mail"
                        class="rounded-xl border border-white/10 bg-slate-900/60 px-3 py-2 text-sm" />
                    <input v-model.trim="form.password" type="password" placeholder="Passwort"
                        class="rounded-xl border border-white/10 bg-slate-900/60 px-3 py-2 text-sm" />
                    <label class="flex items-center gap-2 text-sm text-slate-300">
                        <input type="checkbox" v-model="form.is_admin" /> Admin
                    </label>
                </div>
                <button
                    class="rounded-xl bg-gradient-to-r from-sky-500 to-purple-500 px-4 py-2 text-sm font-semibold text-white"
                    @click="createUser">
                    User erstellen
                </button>
            </div>

            <div>
                <h2 class="text-xl font-semibold">Bestehende User</h2>
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="text-xs uppercase tracking-widest text-slate-500">
                            <tr>
                                <th class="py-3">Name</th>
                                <th class="py-3">E-Mail</th>
                                <th class="py-3">Admin</th>
                                <th class="py-3">Aktionen</th>
                            </tr>
                        </thead>
                        <tbody class="text-slate-200">
                            <tr v-for="user in users" :key="user.id" class="border-t border-white/10">
                                <td class="py-3">
                                    <input v-model.trim="user.name"
                                        class="rounded-lg border border-white/10 bg-slate-900/60 px-2 py-1 text-sm" />
                                </td>
                                <td class="py-3">
                                    <input v-model.trim="user.email"
                                        class="rounded-lg border border-white/10 bg-slate-900/60 px-2 py-1 text-sm" />
                                </td>
                                <td class="py-3">
                                    <input type="checkbox" v-model="user.is_admin" />
                                </td>
                                <td class="py-3">
                                    <div class="flex gap-2">
                                        <button class="rounded-xl border border-white/10 px-3 py-1 text-xs"
                                            @click="updateUser(user)">
                                            Speichern
                                        </button>
                                        <button class="rounded-xl bg-rose-500/20 px-3 py-1 text-xs text-rose-200"
                                            @click="deleteUser(user.id)">
                                            Löschen
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';
import axios from 'axios';

interface UserItem {
    id: number;
    name: string;
    email: string;
    is_admin: boolean;
}

const users = ref<UserItem[]>([]);
const error = ref<string | null>(null);
const form = ref({ name: '', email: '', password: '', is_admin: false });

const loadUsers = async () => {
    error.value = null;
    try {
        const response = await axios.get('/api/admin/users');
        users.value = response.data.data ?? response.data;
    } catch (err: any) {
        error.value = err?.response?.data?.message ?? 'User konnten nicht geladen werden.';
    }
};

const createUser = async () => {
    error.value = null;
    try {
        await refreshCsrf();
        await axios.post('/api/admin/users', form.value);
        form.value = { name: '', email: '', password: '', is_admin: false };
        await loadUsers();
    } catch (err: any) {
        error.value = err?.response?.data?.message ?? 'User anlegen fehlgeschlagen.';
    }
};

const updateUser = async (user: UserItem) => {
    error.value = null;
    try {
        await refreshCsrf();
        await axios.put(`/api/admin/users/${user.id}`, user);
        await loadUsers();
    } catch (err: any) {
        error.value = err?.response?.data?.message ?? 'User speichern fehlgeschlagen.';
    }
};

const deleteUser = async (id: number) => {
    if (!confirm('User wirklich löschen?')) return;
    error.value = null;
    try {
        await refreshCsrf();
        await axios.delete(`/api/admin/users/${id}`);
        await loadUsers();
    } catch (err: any) {
        error.value = err?.response?.data?.message ?? 'User löschen fehlgeschlagen.';
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
    loadUsers();
});
</script>
