<template>
    <v-dialog
        :model-value="modelValue"
        :max-width="maxWidth"
        @update:model-value="$emit('update:modelValue', $event)"
    >
        <v-card>
            <v-card-title class="d-flex align-center ga-2 border-b">
                <v-icon :color="iconColor" :icon="icon" size="small" />
                <span class="text-h5">{{ title }}</span>
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

<script setup lang="ts">
interface Props {
    modelValue?: boolean;
    title?: string;
    message?: string;
    targetName?: string;
    warningText?: string;
    confirmText?: string;
    cancelText?: string;
    confirmColor?: string;
    icon?: string;
    iconColor?: string;
    maxWidth?: string | number;
    loading?: boolean;
}


withDefaults(defineProps<Props>(), {
    modelValue: false,
    title: 'Confirm',
    message: 'Are you sure?',
    targetName: '',
    warningText: 'This action cannot be undone.',
    confirmText: 'Confirm',
    cancelText: 'Cancel',
    confirmColor: 'error',
    icon: 'mdi-alert-circle',
    iconColor: 'error',
    maxWidth: 420,
    loading: false,
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean): void;
    (e: 'confirm'): void;
    (e: 'cancel'): void;
}>();

function onConfirm(): void {
    emit('confirm');
}

function onCancel(): void {
    emit('cancel');
    emit('update:modelValue', false);
}
</script>
