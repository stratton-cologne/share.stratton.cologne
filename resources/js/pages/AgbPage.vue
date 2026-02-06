<template>
    <section class="space-y-6">
        <div class="space-y-2">
            <p class="text-sm uppercase tracking-[0.3em] text-slate-500">{{ t('legal.label') }}</p>
            <h1 class="text-3xl font-semibold text-white">{{ t('terms.title') }}</h1>
            <p class="text-slate-400">{{ t('terms.subtitle') }}</p>
        </div>

        <div class="space-y-6 rounded-3xl border border-white/10 bg-slate-900/70 p-6 text-slate-300">
            <div>
                <h2 class="text-lg font-semibold text-white">{{ t('terms.section1Title') }}</h2>
                <p>{{ t('terms.section1Body') }}</p>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-white">{{ t('terms.section2Title') }}</h2>
                <p>{{ t('terms.section2Body') }}</p>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-white">{{ t('terms.section3Title') }}</h2>
                <p>{{ t('terms.section3Body') }}</p>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-white">{{ t('terms.section4Title') }}</h2>
                <p>{{ t('terms.section4Body') }}</p>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-white">{{ t('terms.section5Title') }}</h2>
                <p>{{ t('terms.section5Body') }}</p>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-white">{{ t('terms.section6Title') }}</h2>
                <p>{{ t('terms.section6Body', { email: 'info@stratton.cologne' }) }}</p>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>
