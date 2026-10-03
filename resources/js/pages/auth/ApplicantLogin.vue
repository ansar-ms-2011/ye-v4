<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import TextLink from '@/components/TextLink.vue';
import { login } from '@/routes';
import { store } from '@/routes/applicant';

defineOptions({
    layout: {
        title: 'Log in to your account',
        description: 'Enter your application number and dob.',
    },
});

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head title="Log in" />

    <VAlert v-if="status" type="success" variant="tonal" class="mb-4">
        {{ status }}
    </VAlert>

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
    >
        <div class="starter-field mb-4">
            <label for="email">Application Number</label>
            <VTextField
                id="application_no"
                type="application_no"
                name="application_no"
                density="compact"
                variant="outlined"
                hide-details="auto"
                required
                autofocus
                placeholder="Enter your application number i.e 45698"
                autocomplete="application_no"
                :error-messages="errors.application_no"
            />
        </div>

        <div class="starter-field mb-2">
            <label for="email">Applicant's DOB</label>
            <VDateInput
                id="applicant_dob"
                name="applicant_dob"
                density="compact"
                prepend-icon=""
                variant="outlined"
                inputFormat="dd-mm-yyyy"
                placeholder="Enter your application's DOB (dd-mm-yyyy)"
                persistent-placeholder
                hide-details="auto"
                required
                autocomplete="applicant_dob"
                :error-messages="errors.applicant_dob"
            />
        </div>

        <VCheckbox
            name="remember"
            label="Remember me"
            value="on"
            density="compact"
            hide-details
        />

        <VBtn
            type="submit"
            class="mt-4 rounded-lg"
            color="primary"
            block
            size="large"
            :loading="processing"
            :disabled="processing"
            data-test="login-button"
        >
            Log in
        </VBtn>

        <div class="text-center text-body-2 mt-6">
            Don't have an account?
            <TextLink :href="login()" :tabindex="5">Official Log In</TextLink>
        </div>
    </Form>
</template>
