<script setup lang="ts">
defineProps({
    user: {
        type: Object,
        required: true,
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
});
</script>

<template>
    <VRow density="compact" class="ma-0">
        <VCol class="py-1" cols="12" md="6">
            <label for="full_name">Full Name</label>
            <VTextField
                id="full_name"
                :modelValue="user.full_name"
                name="full_name"
                density="compact"
                variant="outlined"
                hide-details="auto"
                required
                autocomplete="full_name"
                :error-messages="errors.full_name"
            />
        </VCol>
        <VCol class="py-1" cols="12" md="6">
            <label for="email">Email address</label>
            <VTextField
                id="cyeo_email"
                :modelValue="user.email"
                type="email"
                name="email"
                density="compact"
                variant="outlined"
                hide-details="auto"
                required
                autocomplete="email"
                :error-messages="errors.email"
            />
        </VCol>
    </VRow>
</template>

<style scoped></style>
