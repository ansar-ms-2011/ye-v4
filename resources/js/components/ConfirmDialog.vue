<template>
    <v-dialog
        :model-value="modelValue"
        :max-width="maxWidth"
        @update:model-value="$emit('update:modelValue', $event)"
    >
        <v-card>
            <v-card-title class="d-flex align-center ga-2">
                <v-icon :color="iconColor" :icon="icon" />
                {{ title }}
            </v-card-title>

            <v-card-text>
                <slot>
                    {{ message }}
                    <strong v-if="targetName">{{ targetName }}</strong>
                    <span v-if="targetName">?</span>
                </slot>
                <br v-if="warningText" />
                <span v-if="warningText" class="text-medium-emphasis">
                    {{ warningText }}
                </span>
            </v-card-text>

            <v-divider />

            <v-card-actions class="bg-surface-light">
                <v-btn
                    :text="cancelText"
                    variant="plain"
                    :disabled="loading"
                    @click="onCancel"
                />

                <v-spacer />

                <v-btn
                    :color="confirmColor"
                    variant="tonal"
                    class="rounded-md"
                    :loading="loading"
                    @click="onConfirm"
                >
                    {{ confirmText }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup>
const props = defineProps({
    modelValue: { type: Boolean, default: false },
    title: { type: String, default: 'Confirm' },
    message: { type: String, default: 'Are you sure?' },
    targetName: { type: String, default: '' },
    warningText: { type: String, default: 'This action cannot be undone.' },
    confirmText: { type: String, default: 'Confirm' },
    cancelText: { type: String, default: 'Cancel' },
    confirmColor: { type: String, default: 'error' },
    icon: { type: String, default: 'mdi-alert-circle' },
    iconColor: { type: String, default: 'error' },
    maxWidth: { type: [String, Number], default: 420 },
    loading: { type: Boolean, default: false },
});

const emit = defineEmits(['update:modelValue', 'confirm', 'cancel']);

function onConfirm() {
    emit('confirm');
}

function onCancel() {
    emit('cancel');
    emit('update:modelValue', false);
}
</script>
