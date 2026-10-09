<template>
    <div id="appForm">
        <v-row class="mb-3">
            <v-col cols="6" sm="6" md="6">
                <v-btn
                    color="secondary-darken-5"
                    style="float: left"
                    @click="goBack"
                    prepend-icon="mdi-arrow-left"
                >
                    Back
                </v-btn>
            </v-col>
            <v-col v-if="isAdminOrDyeo" cols="6" sm="6" md="6" />
        </v-row>

        <v-card>
            <v-card-title class="text-h6 primary-lighten-5">
                <span>Emails Sent History</span>
            </v-card-title>
            <v-card-text>
                <VDataTable density="compact" :headers="headers" :items="rows">
                    <template #[`item.email_type`]="{ item }">
                        <VChip
                            size="small"
                            :color="
                                item.email_type?.email_type_label ===
                                'Payment-STEP'
                                    ? 'info'
                                    : 'teal'
                            "
                            variant="outlined"
                        >
                            {{ item.email_type?.email_type_label || 'STEP' }}
                        </VChip>
                    </template>
                    <template #[`item.created_at`]="{ item }">
                        {{ format(parseISO(item.created_at), 'dd-MM-yyyy') }}
                    </template>
                    <template #no-data>
                        <div
                            class="d-flex justify-center align-center"
                            style="min-width: 100%; min-height: 100px"
                        >
                            <div class="d-flex justify-center align-center">
                                <v-icon icon="mdi-alert-circle-outline"></v-icon>
                                <span class="ml-2">There is no email history</span>
                            </div>
                        </div>
                    </template>
                </VDataTable>
            </v-card-text>
        </v-card>
    </div>
</template>

<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { format, parseISO } from 'date-fns';
import { computed, onMounted, ref } from 'vue';
import type { PageProps } from '@/types';

interface EmailType {
    email_type_label: string;
}

interface EmailSentRow {
    id: number;
    email_address: string;
    message_title: string;
    email_type: EmailType;
    created_at: string;
}

interface Props {
    fileName?: string;
    appId: string | number | null;
    emailSentList?: EmailSentRow[];
}

const props = withDefaults(defineProps<Props>(), {
    fileName: '',
    emailSentList: () => [],
    appId: '',
});

const page = usePage<PageProps>();

const role = page.props.auth?.role ?? '';
const rows = ref<EmailSentRow[]>([]);

const headerClass = 'bg-secondary-lighten-3 text-black header-hover-primary';

const headers = [
    {
        title: 'ID',
        key: 'id',
        align: 'start' as const,
        sortable: false,
        width: '75px',
        headerProps: { class: headerClass },
    },
    {
        title: 'Email Address',
        key: 'email_address',
        width: '150px',
        sortable: false,
        headerProps: { class: headerClass },
    },
    {
        title: 'Message Title',
        key: 'message_title',
        headerProps: { class: headerClass },
        minWidth: '450px',
        maxWidth: '550px',
        sortable: false,
    },
    {
        title: 'Email Type',
        key: 'email_type',
        width: '150px',
        sortable: false,
        headerProps: { class: headerClass },
    },
    {
        title: 'Sent On',
        key: 'created_at',
        width: '250px',
        sortable: false,
        headerProps: { class: headerClass },
    },
];

const isAdminOrDyeo = computed(() => role === 'dyeo' || role === 'admin');

function goBack() {
    window.history.back();
}

onMounted(() => {
    rows.value = props.emailSentList ?? [];
});
</script>

<style scoped>
:deep(.header-hover-primary) {
    transition: color 0.2s ease;
}

:deep(.header-hover-primary:hover) {
    color: rgb(var(--v-theme-primary)) !important;
}
</style>
