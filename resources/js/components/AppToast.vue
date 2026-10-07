<script setup lang="ts">
import { computed } from 'vue';
import { flashToastState } from '@/lib/flashToast';

const color = computed(() => {
    switch (flashToastState.toast?.type) {
        case 'error':
            return 'error';
        case 'info':
            return 'info';
        case 'warning':
            return 'warning';
        default:
            return 'success';
    }
});
</script>

<template>
    <VSnackbar
        v-model="flashToastState.show"
        :color="color"
        location="top end"
        timer="bottom"
        timer-color="white"
    >
        {{ flashToastState.toast?.message }}

        <template v-if="flashToastState.toast?.type === 'info'" v-slot:prepend>
            <v-avatar
                color="blue"
                icon="mdi-information-outline"
            ></v-avatar>
        </template>

        <template
            v-else-if="flashToastState.toast?.type === 'warning'"
            v-slot:prepend
        >
            <v-avatar
                color="amber"
                icon="mdi-bell-ring-outline"
            ></v-avatar>
        </template>

        <template
            v-else-if="flashToastState.toast?.type === 'error'"
            v-slot:prepend
        >
            <v-avatar
                color="error"
                icon="mdi-close-octagon"
            ></v-avatar>
        </template>
        <template v-else v-slot:prepend>
            <v-avatar
                color="success"
                icon="mdi-check-decagram"
            ></v-avatar>
        </template>

        <template #actions>
            <VBtn
                size="sm"
                variant="text"
                icon="mdi-close"
                @click="flashToastState.show = false"
            />
        </template>
    </VSnackbar>
</template>
