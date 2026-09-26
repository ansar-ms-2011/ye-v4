<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import { dashboard } from '@/routes';

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
            class="dashboard-placeholder dashboard-placeholder-card"
            border
            rounded="lg"
        >
            <PlaceholderPattern />
        </VSheet>

        <VSheet
            class="dashboard-placeholder dashboard-placeholder-panel"
            border
            rounded="lg"
        >
            <VSelect
                density="compact"
                variant="outlined"
                :items="[
                    'California',
                    'Colorado',
                    'Florida',
                    'Georgia',
                    'Texas',
                    'Wyoming',
                ]"
                multiple
            ></VSelect>
            <VTextField
                id="email"
                type="email"
                name="email"
                density="compact"
                variant="outlined"
                hide-details="auto"
                required
                autofocus
                autocomplete="email"
            />
            <!--            <PlaceholderPattern />-->
        </VSheet>
    </div>
</template>
