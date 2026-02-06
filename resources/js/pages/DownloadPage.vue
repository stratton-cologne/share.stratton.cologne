<template>
    <section class="rounded-3xl border border-white/10 bg-slate-900/60 p-8">
        <h1 class="text-3xl font-semibold">Download</h1>
        <p class="mt-2 text-slate-400">Weiterleitung zur Download-Übersicht ...</p>
    </section>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();
const token = String(route.params.token ?? '');

onMounted(() => {
    if (!token) return;
    router.replace(`/share/${token}`);
});
</script>
