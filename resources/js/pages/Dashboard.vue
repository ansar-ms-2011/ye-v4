<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
//import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import { dashboard } from '@/routes';
const pageProps = usePage().props.auth;
console.log(pageProps);
defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <div class="app-page dashboard-grid">
        <VSheet
            v-for="index in 3"
            :key="index"
            color="primary-light"
            class="dashboard-placeholder dashboard-placeholder-card"
            border
            rounded="lg"
        >
        </VSheet>

        <VSheet
            class="dashboard-placeholder dashboard-placeholder-panel"
            border
            rounded="lg"
        >
        </VSheet>
    </div>
</template>
