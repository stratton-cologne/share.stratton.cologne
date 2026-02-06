<template>
    <transition name="fade">
        <div v-if="isVisible"
            class="fixed inset-x-0 bottom-0 z-50 border-t border-white/10 bg-slate-950/95 px-6 py-4 backdrop-blur">
            <div class="mx-auto flex max-w-6xl flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="space-y-2 text-sm text-slate-300">
                    <p class="text-base font-semibold text-white">Cookies & Datenschutz</p>
                    <p>
                        Wir verwenden essenzielle Cookies, um die Funktion dieser Website zu ermöglichen.
                        Du kannst diese Auswahl jederzeit in den Browsereinstellungen löschen.
                    </p>
                    <RouterLink class="text-sky-400 hover:text-sky-300" to="/datenschutz">
                        Mehr erfahren (inkl. E-Mail-Benachrichtigungen)
                    </RouterLink>
                </div>
                <div class="flex flex-wrap gap-3">
                    <button
                        class="rounded-xl border border-white/10 px-4 py-2 text-sm font-semibold text-slate-200 hover:border-white/20"
                        @click="acceptEssential">
                        Nur essenzielle Cookies
                    </button>
                    <button
                        class="rounded-xl bg-sky-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-sky-400"
                        @click="acceptAll">
                        Alle akzeptieren
                    </button>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue';
import { RouterLink } from 'vue-router';

const isVisible = ref(false);
const storageKey = 'stratton-share-cookie-consent';

type ConsentValue = 'essential' | 'all';

const setConsent = (value: ConsentValue) => {
    localStorage.setItem(storageKey, value);
    isVisible.value = false;
};

const acceptEssential = () => setConsent('essential');
const acceptAll = () => setConsent('all');

const resetConsent = () => {
    localStorage.removeItem(storageKey);
    isVisible.value = true;
};

onMounted(() => {
    const existing = localStorage.getItem(storageKey);
    if (existing !== 'essential' && existing !== 'all') {
        isVisible.value = true;
    }

    window.addEventListener('stratton-cookie-reset', resetConsent);
});

onUnmounted(() => {
    window.removeEventListener('stratton-cookie-reset', resetConsent);
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
