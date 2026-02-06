<template>
    <section class="space-y-6">
        <div class="space-y-2">
            <p class="text-sm uppercase tracking-[0.3em] text-slate-500">{{ t('legal.label') }}</p>
            <h1 class="text-3xl font-semibold text-white">{{ t('imprint.title') }}</h1>
            <p class="text-slate-400">{{ t('imprint.subtitle') }}</p>
        </div>

        <div class="grid gap-6 rounded-3xl border border-white/10 bg-slate-900/70 p-6">
            <div>
                <h2 class="text-lg font-semibold text-white">{{ t('imprint.providerTitle') }}</h2>
                <p class="text-slate-300">{{ t('imprint.providerName') }}</p>
                <p class="text-slate-300">{{ t('imprint.providerStreet') }}</p>
                <p class="text-slate-300">{{ t('imprint.providerCity') }}</p>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-white">{{ t('imprint.contactTitle') }}</h2>
                <p class="text-slate-300">{{ t('imprint.contactPhone') }}</p>
                <p class="text-slate-300">{{ t('imprint.contactEmail', { email: 'info@stratton.cologne' }) }}</p>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-white">{{ t('imprint.representativeTitle') }}</h2>
                <p class="text-slate-300">{{ t('imprint.representativeName') }}</p>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>
