<template>
    <section class="space-y-6">
        <div class="space-y-2">
            <p class="text-sm uppercase tracking-[0.3em] text-slate-500">{{ t('legal.label') }}</p>
            <h1 class="text-3xl font-semibold text-white">{{ t('privacy.title') }}</h1>
            <p class="text-slate-400">{{ t('privacy.subtitle') }}</p>
        </div>

        <div class="space-y-6 rounded-3xl border border-white/10 bg-slate-900/70 p-6 text-slate-300">
            <div>
                <h2 class="text-lg font-semibold text-white">{{ t('privacy.section1Title') }}</h2>
                <p>{{ t('privacy.section1Body') }}</p>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-white">{{ t('privacy.section2Title') }}</h2>
                <p>{{ t('privacy.section2Body') }}</p>
                <p class="mt-3">{{ t('privacy.section2Note') }}</p>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-white">{{ t('privacy.section3Title') }}</h2>
                <p>{{ t('privacy.section3Body') }}</p>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-white">{{ t('privacy.section4Title') }}</h2>
                <p>{{ t('privacy.section4Body') }}</p>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-white">{{ t('privacy.section5Title') }}</h2>
                <p>{{ t('privacy.section5Body') }}</p>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-white">{{ t('privacy.section6Title') }}</h2>
                <p>{{ t('privacy.section6Body', { email: 'privacy@stratton.cologne' }) }}</p>
            </div>

            <p class="text-xs text-slate-500">
                {{ t('privacy.disclaimer') }}
            </p>
        </div>
    </section>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>
