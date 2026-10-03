<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { dashboard } from '@/routes';

interface Stats {
    total_applications: number;
    camps_count: number;
    step_count: number;
    fee_paid_count: number;
    total_clubs: number;
    total_dyeos: number;
    total_cyeos: number;
    total_users: number;
}

interface ApplicationSummary {
    id: number;
    application_no: string | null;
    full_name: string;
    email_address: string;
    exchange_type: string | null;
    application_status: string | null;
    date_of_app: string | null;
    club_name: string;
    district_code: string;
}

interface EmailSummary {
    id: number;
    recipient: string;
    subject: string;
    sent_at: string;
}

interface ApplicantApplication {
    id: number;
    application_no: string | null;
    full_name: string;
    firstname: string;
    surname: string;
    email_address: string;
    exchange_type: string | null;
    application_status: string | null;
    application_status_note: string | null;
    application_fee_paid: boolean;
    date_of_app: string | null;
    club_name: string;
    dyeo_name: string;
    district_code: string;
    media_count: number;
    languages_count: number;
}

interface Props {
    role: string;
    stats: Stats;
    statusBreakdown: Record<string, number>;
    exchangeTypeBreakdown: Record<string, number>;
    recentApplications: ApplicationSummary[];
    recentEmails: EmailSummary[];
    applicantApplication: ApplicantApplication | null;
    userContext: {
        name?: string;
        email?: string;
        district?: string;
        club_name?: string;
    };
}

const props = defineProps<Props>();

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

const isApplicant = computed(() => props.role === 'applicant');
const isAdmin = computed(() => props.role === 'admin');
const isDyeo = computed(() => props.role === 'dyeo');
const isCyeo = computed(() => props.role === 'cyeo');

const statusColors: Record<string, string> = {
    Submitted: 'primary',
    Pending: 'warning',
    Approved: 'success',
    Rejected: 'error',
    'Under Review': 'info',
    Unassigned: 'grey',
};

function getStatusColor(status?: string | null): string {
    if (!status) {
        return 'grey';
    }

    return statusColors[status] || 'primary';
}

const recentApplicationsHeaders = [
    { title: 'App No', key: 'application_no', align: 'start' as const },
    { title: 'Applicant', key: 'full_name', align: 'start' as const },
    { title: 'Exchange Type', key: 'exchange_type', align: 'start' as const },
    { title: 'District', key: 'district_code', align: 'center' as const },
    { title: 'Club', key: 'club_name', align: 'start' as const },
    { title: 'Status', key: 'application_status', align: 'center' as const },
    {
        title: 'Actions',
        key: 'actions',
        align: 'center' as const,
        sortable: false,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <div class="app-page dashboard-container">
        <!-- Hero Welcome Banner -->
        <VCard
            elevation="1"
            rounded="lg"
            class="mb-4 welcome-banner"
            color="primary"
        >
            <VCardText class="pa-4 pa-sm-6">
                <div
                    class="d-flex flex-column flex-sm-row justify-space-between align-sm-center ga-3"
                >
                    <div>
                        <div
                            class="text-caption text-uppercase font-weight-bold opacity-80 mb-1"
                        >
                            Rotary Youth Exchange &bull; RIBI
                        </div>
                        <h1
                            class="text-h5 text-sm-h4 font-weight-bold text-white mb-1"
                        >
                            Welcome back, {{ userContext.name || 'User' }}
                        </h1>
                        <p class="text-body-2 text-white opacity-90 mb-0">
                            <span class="text-capitalize">{{ role }}</span>
                            portal
                            <template v-if="userContext.district">
                                &bull; District
                                {{ userContext.district }}</template
                            >
                            <template v-if="userContext.club_name">
                                &bull; {{ userContext.club_name }}</template
                            >
                        </p>
                    </div>

                    <div class="d-flex ga-2 flex-wrap align-center">
                        <VChip
                            color="white"
                            variant="flat"
                            class="text-primary font-weight-bold"
                        >
                            <VIcon
                                start
                                icon="mdi-shield-account-outline"
                                size="18"
                            />
                            {{ role.toUpperCase() }}
                        </VChip>
                        <template v-if="!isApplicant">
                            <VBtn
                                color="white"
                                variant="outlined"
                                prepend-icon="mdi-plus"
                                href="/application/create"
                                size="small"
                                class="text-white"
                            >
                                New Application
                            </VBtn>
                        </template>
                    </div>
                </div>
            </VCardText>
        </VCard>

        <!-- APPLICANT VIEW -->
        <template v-if="isApplicant">
            <VRow class="mb-4">
                <VCol cols="12" md="8">
                    <VCard elevation="1" rounded="lg" class="h-100">
                        <VCardItem class="border-b">
                            <template #prepend>
                                <VIcon
                                    icon="mdi-account-details-outline"
                                    color="primary"
                                    class="mr-2"
                                />
                            </template>
                            <VCardTitle>My Application Status</VCardTitle>
                        </VCardItem>
                        <VCardText class="pa-4 pa-sm-6">
                            <template v-if="applicantApplication">
                                <div
                                    class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center pb-4 border-b ga-3"
                                >
                                    <div>
                                        <div class="text-h6 font-weight-bold">
                                            {{ applicantApplication.full_name }}
                                        </div>
                                        <div
                                            class="text-caption text-medium-emphasis"
                                        >
                                            Application #{{
                                                applicantApplication.application_no ||
                                                applicantApplication.id
                                            }}
                                            &bull; Applied:
                                            {{
                                                applicantApplication.date_of_app ||
                                                'N/A'
                                            }}
                                        </div>
                                    </div>
                                    <VChip
                                        :color="
                                            getStatusColor(
                                                applicantApplication.application_status,
                                            )
                                        "
                                        size="large"
                                        class="font-weight-bold"
                                    >
                                        {{
                                            applicantApplication.application_status ||
                                            'Submitted'
                                        }}
                                    </VChip>
                                </div>

                                <VRow class="mt-4">
                                    <VCol cols="12" sm="6">
                                        <div
                                            class="text-caption text-medium-emphasis"
                                        >
                                            Exchange Type
                                        </div>
                                        <div
                                            class="text-body-1 font-weight-medium"
                                        >
                                            {{
                                                applicantApplication.exchange_type ||
                                                'Short Term Exchange Program (STEP)'
                                            }}
                                        </div>
                                    </VCol>
                                    <VCol cols="12" sm="6">
                                        <div
                                            class="text-caption text-medium-emphasis"
                                        >
                                            Fee Status
                                        </div>
                                        <div>
                                            <VChip
                                                size="small"
                                                :color="
                                                    applicantApplication.application_fee_paid
                                                        ? 'success'
                                                        : 'warning'
                                                "
                                                variant="flat"
                                            >
                                                {{
                                                    applicantApplication.application_fee_paid
                                                        ? 'Fee Paid'
                                                        : 'Fee Pending'
                                                }}
                                            </VChip>
                                        </div>
                                    </VCol>
                                    <VCol cols="12" sm="6">
                                        <div
                                            class="text-caption text-medium-emphasis"
                                        >
                                            Assigned Rotary Club
                                        </div>
                                        <div
                                            class="text-body-1 font-weight-medium"
                                        >
                                            {{ applicantApplication.club_name }}
                                        </div>
                                    </VCol>
                                    <VCol cols="12" sm="6">
                                        <div
                                            class="text-caption text-medium-emphasis"
                                        >
                                            District Coordinator (DYEO)
                                        </div>
                                        <div
                                            class="text-body-1 font-weight-medium"
                                        >
                                            {{ applicantApplication.dyeo_name }}
                                            (District
                                            {{
                                                applicantApplication.district_code
                                            }})
                                        </div>
                                    </VCol>
                                    <VCol
                                        cols="12"
                                        v-if="
                                            applicantApplication.application_status_note
                                        "
                                    >
                                        <div
                                            class="text-caption text-medium-emphasis"
                                        >
                                            Coordinator Notes
                                        </div>
                                        <div
                                            class="pa-3 bg-grey-lighten-4 rounded text-body-2"
                                        >
                                            {{
                                                applicantApplication.application_status_note
                                            }}
                                        </div>
                                    </VCol>
                                </VRow>

                                <div class="d-flex ga-2 mt-6 flex-wrap">
                                    <VBtn
                                        color="primary"
                                        prepend-icon="mdi-pencil-outline"
                                        :href="`/applications/${applicantApplication.id}/edit`"
                                    >
                                        Edit Application
                                    </VBtn>
                                    <VBtn
                                        variant="outlined"
                                        color="primary"
                                        prepend-icon="mdi-file-document-outline"
                                        :href="`/applications-pdf-view/${applicantApplication.id}`"
                                        target="_blank"
                                    >
                                        View PDF Form
                                    </VBtn>
                                    <VBtn
                                        variant="outlined"
                                        color="info"
                                        prepend-icon="mdi-email-outline"
                                        :href="`/email-guide/${applicantApplication.id}`"
                                    >
                                        Email Guide
                                    </VBtn>
                                </div>
                            </template>
                            <template v-else>
                                <div class="text-center py-8">
                                    <VIcon
                                        icon="mdi-clipboard-text-search-outline"
                                        size="48"
                                        color="grey"
                                        class="mb-3"
                                    />
                                    <div class="text-h6">
                                        No Application Found
                                    </div>
                                    <p
                                        class="text-caption text-medium-emphasis mb-4"
                                    >
                                        You have not submitted an application
                                        yet or it has not been linked to your
                                        account.
                                    </p>
                                    <VBtn
                                        color="primary"
                                        href="/application/create"
                                        prepend-icon="mdi-plus"
                                    >
                                        Start New Application
                                    </VBtn>
                                </div>
                            </template>
                        </VCardText>
                    </VCard>
                </VCol>

                <VCol cols="12" md="4">
                    <VCard elevation="1" rounded="lg" class="mb-4">
                        <VCardItem class="border-b">
                            <template #prepend>
                                <VIcon
                                    icon="mdi-help-circle-outline"
                                    color="primary"
                                    class="mr-2"
                                />
                            </template>
                            <VCardTitle>Quick Checklist</VCardTitle>
                        </VCardItem>
                        <VCardText class="pa-4">
                            <VList density="compact">
                                <VListItem>
                                    <template #prepend>
                                        <VIcon
                                            :icon="
                                                applicantApplication?.full_name
                                                    ? 'mdi-check-circle'
                                                    : 'mdi-checkbox-blank-circle-outline'
                                            "
                                            :color="
                                                applicantApplication?.full_name
                                                    ? 'success'
                                                    : 'grey'
                                            "
                                        />
                                    </template>
                                    <VListItemTitle
                                        >Personal Information</VListItemTitle
                                    >
                                    <VListItemSubtitle
                                        >Name, contact,
                                        address</VListItemSubtitle
                                    >
                                </VListItem>
                                <VListItem>
                                    <template #prepend>
                                        <VIcon
                                            :icon="
                                                (applicantApplication?.media_count ??
                                                    0) > 0
                                                    ? 'mdi-check-circle'
                                                    : 'mdi-alert-circle'
                                            "
                                            :color="
                                                (applicantApplication?.media_count ??
                                                    0) > 0
                                                    ? 'success'
                                                    : 'warning'
                                            "
                                        />
                                    </template>
                                    <VListItemTitle
                                        >Photo & Documents</VListItemTitle
                                    >
                                    <VListItemSubtitle
                                        >{{
                                            applicantApplication?.media_count ??
                                            0
                                        }}
                                        files uploaded</VListItemSubtitle
                                    >
                                </VListItem>
                                <VListItem>
                                    <template #prepend>
                                        <VIcon
                                            :icon="
                                                applicantApplication?.application_fee_paid
                                                    ? 'mdi-check-circle'
                                                    : 'mdi-alert-circle'
                                            "
                                            :color="
                                                applicantApplication?.application_fee_paid
                                                    ? 'success'
                                                    : 'warning'
                                            "
                                        />
                                    </template>
                                    <VListItemTitle
                                        >Application Fee</VListItemTitle
                                    >
                                    <VListItemSubtitle>{{
                                        applicantApplication?.application_fee_paid
                                            ? 'Confirmed'
                                            : 'Awaiting payment confirmation'
                                    }}</VListItemSubtitle>
                                </VListItem>
                            </VList>
                        </VCardText>
                    </VCard>

                    <VCard elevation="1" rounded="lg">
                        <VCardItem class="border-b">
                            <template #prepend>
                                <VIcon
                                    icon="mdi-information-outline"
                                    color="primary"
                                    class="mr-2"
                                />
                            </template>
                            <VCardTitle>Need Support?</VCardTitle>
                        </VCardItem>
                        <VCardText class="pa-4 text-body-2">
                            <p class="mb-2">
                                If you have any questions regarding your
                                exchange program or documents, please contact
                                your District Youth Exchange Officer (DYEO).
                            </p>
                            <div class="mt-3">
                                <strong>District:</strong>
                                {{
                                    applicantApplication?.district_code ||
                                    userContext.district ||
                                    'N/A'
                                }}
                            </div>
                        </VCardText>
                    </VCard>
                </VCol>
            </VRow>
        </template>

        <!-- ADMIN / DYEO / CYEO VIEW -->
        <template v-else>
            <!-- Key Metric KPI Cards -->
            <VRow class="mb-4">
                <VCol cols="12" sm="6" md="3">
                    <VCard
                        border
                        elevation="2"
                        rounded="lg"
                        class="h-100 kpi-card"
                        color="primary-lighten-5"
                    >
                        <VCardText
                            class="d-flex align-center justify-space-between pa-4"
                        >
                            <div>
                                <div
                                    class="text-caption text-medium-emphasis font-weight-medium"
                                >
                                    Total Applications
                                </div>
                                <div
                                    class="text-h4 font-weight-bold mt-1 text-primary"
                                >
                                    {{ stats.total_applications }}
                                </div>
                                <div
                                    class="text-caption text-medium-emphasis mt-1"
                                >
                                    All exchange types
                                </div>
                            </div>
                            <VAvatar
                                color="primary-lighten-4"
                                size="52"
                                rounded="lg"
                            >
                                <VIcon
                                    icon="mdi-folder-open-outline"
                                    color="primary"
                                    size="28"
                                />
                            </VAvatar>
                        </VCardText>
                    </VCard>
                </VCol>

                <VCol cols="12" sm="6" md="3">
                    <VCard
                        border
                        elevation="2"
                        rounded="lg"
                        class="h-100 kpi-card"
                        color="success-lighten-5"
                    >
                        <VCardText
                            class="d-flex align-center justify-space-between pa-4"
                        >
                            <div>
                                <div
                                    class="text-caption text-medium-emphasis font-weight-medium"
                                >
                                    Camps & Tours
                                </div>
                                <div
                                    class="text-h4 font-weight-bold mt-1 text-info"
                                >
                                    {{ stats.camps_count }}
                                </div>
                                <div
                                    class="text-caption text-medium-emphasis mt-1"
                                >
                                    Short tour exchanges
                                </div>
                            </div>
                            <VAvatar
                                color="info-lighten-4"
                                size="52"
                                rounded="lg"
                            >
                                <VIcon icon="mdi-tent" color="info" size="28" />
                            </VAvatar>
                        </VCardText>
                    </VCard>
                </VCol>

                <VCol cols="12" sm="6" md="3">
                    <VCard
                        border
                        elevation="2"
                        rounded="lg"
                        class="h-100 kpi-card"
                        color="warning-lighten-5"
                    >
                        <VCardText
                            class="d-flex align-center justify-space-between pa-4"
                        >
                            <div>
                                <div
                                    class="text-caption text-medium-emphasis font-weight-medium"
                                >
                                    STEP Programs
                                </div>
                                <div
                                    class="text-h4 font-weight-bold mt-1 text-teal"
                                >
                                    {{ stats.step_count }}
                                </div>
                                <div
                                    class="text-caption text-medium-emphasis mt-1"
                                >
                                    Short-term exchange
                                </div>
                            </div>
                            <VAvatar
                                color="teal-lighten-4"
                                size="52"
                                rounded="lg"
                            >
                                <VIcon
                                    icon="mdi-airplane-takeoff"
                                    color="teal"
                                    size="28"
                                />
                            </VAvatar>
                        </VCardText>
                    </VCard>
                </VCol>

                <VCol cols="12" sm="6" md="3">
                    <VCard
                        border
                        elevation="2"
                        rounded="lg"
                        class="h-100 kpi-card"
                        color="error-lighten-5"
                    >
                        <VCardText
                            class="d-flex align-center justify-space-between pa-4"
                        >
                            <div>
                                <div
                                    class="text-caption text-medium-emphasis font-weight-medium"
                                >
                                    Fees Paid
                                </div>
                                <div
                                    class="text-h4 font-weight-bold mt-1 text-success"
                                >
                                    {{ stats.fee_paid_count }}
                                </div>
                                <div
                                    class="text-caption text-medium-emphasis mt-1"
                                >
                                    Payment verified
                                </div>
                            </div>
                            <VAvatar
                                color="success-lighten-4"
                                size="52"
                                rounded="lg"
                            >
                                <VIcon
                                    icon="mdi-check-decagram"
                                    color="success"
                                    size="28"
                                />
                            </VAvatar>
                        </VCardText>
                    </VCard>
                </VCol>
            </VRow>

            <!-- Secondary Analytics & Status Row -->
            <VRow class="mb-4">
                <!-- Status Breakdown -->
                <VCol cols="12" md="4">
                    <VCard border elevation="2" rounded="lg" class="h-100">
                        <VCardItem class="border-b">
                            <template #prepend>
                                <VIcon
                                    icon="mdi-chart-pie"
                                    color="primary"
                                    class="mr-2"
                                />
                            </template>
                            <VCardTitle>Status Overview</VCardTitle>
                        </VCardItem>
                        <VCardText class="pa-4">
                            <template
                                v-if="Object.keys(statusBreakdown).length > 0"
                            >
                                <div
                                    v-for="(count, status) in statusBreakdown"
                                    :key="status"
                                    class="mb-3"
                                >
                                    <div
                                        class="d-flex justify-space-between text-body-2 font-weight-medium mb-1"
                                    >
                                        <span class="d-flex align-center ga-1">
                                            <VIcon
                                                icon="mdi-circle-small"
                                                :color="
                                                    getStatusColor(
                                                        String(status),
                                                    )
                                                "
                                            />
                                            {{ status }}
                                        </span>
                                        <span class="text-medium-emphasis"
                                            >{{ count }} ({{
                                                Math.round(
                                                    (Number(count) /
                                                        (stats.total_applications ||
                                                            1)) *
                                                        100,
                                                )
                                            }}%)</span
                                        >
                                    </div>
                                    <VProgressLinear
                                        :model-value="
                                            (Number(count) /
                                                (stats.total_applications ||
                                                    1)) *
                                            100
                                        "
                                        :color="getStatusColor(String(status))"
                                        rounded
                                        height="6"
                                    />
                                </div>
                            </template>
                            <div
                                v-else
                                class="text-center py-6 text-medium-emphasis text-body-2"
                            >
                                No applications recorded yet
                            </div>
                        </VCardText>
                    </VCard>
                </VCol>

                <!-- Program Exchange Types -->
                <VCol cols="12" md="4">
                    <VCard border elevation="2" rounded="lg" class="h-100">
                        <VCardItem class="border-b">
                            <template #prepend>
                                <VIcon
                                    icon="mdi-swap-horizontal-bold"
                                    color="primary"
                                    class="mr-2"
                                />
                            </template>
                            <VCardTitle>Exchange Types</VCardTitle>
                        </VCardItem>
                        <VCardText class="pa-4">
                            <div class="d-flex flex-column ga-3">
                                <div
                                    class="pa-3 rounded bg-grey-lighten-4 d-flex justify-space-between align-center"
                                >
                                    <div>
                                        <div
                                            class="font-weight-bold text-body-1"
                                        >
                                            Camps & Tours
                                        </div>
                                        <div
                                            class="text-caption text-medium-emphasis"
                                        >
                                            Short summer camps & tours
                                        </div>
                                    </div>
                                    <VChip
                                        color="info"
                                        variant="flat"
                                        class="font-weight-bold"
                                    >
                                        {{ stats.camps_count }}
                                    </VChip>
                                </div>

                                <div
                                    class="pa-3 rounded bg-grey-lighten-4 d-flex justify-space-between align-center"
                                >
                                    <div>
                                        <div
                                            class="font-weight-bold text-body-1"
                                        >
                                            STEP Exchange
                                        </div>
                                        <div
                                            class="text-caption text-medium-emphasis"
                                        >
                                            Short Term Exchange Program
                                        </div>
                                    </div>
                                    <VChip
                                        color="teal"
                                        variant="flat"
                                        class="font-weight-bold"
                                    >
                                        {{ stats.step_count }}
                                    </VChip>
                                </div>

                                <div
                                    class="pa-3 rounded bg-grey-lighten-4 d-flex justify-space-between align-center"
                                >
                                    <div>
                                        <div
                                            class="font-weight-bold text-body-1"
                                        >
                                            Registered Clubs
                                        </div>
                                        <div
                                            class="text-caption text-medium-emphasis"
                                        >
                                            Active Rotary Clubs
                                        </div>
                                    </div>
                                    <VChip
                                        color="primary"
                                        variant="flat"
                                        class="font-weight-bold"
                                    >
                                        {{ stats.total_clubs }}
                                    </VChip>
                                </div>
                            </div>
                        </VCardText>
                    </VCard>
                </VCol>

                <!-- Quick Actions & Domain Shortcuts -->
                <VCol cols="12" md="4">
                    <VCard border elevation="2" rounded="lg" class="h-100">
                        <VCardItem class="border-b">
                            <template #prepend>
                                <VIcon
                                    icon="mdi-lightning-bolt-outline"
                                    color="primary"
                                    class="mr-2"
                                />
                            </template>
                            <VCardTitle>Quick Actions</VCardTitle>
                        </VCardItem>
                        <VCardText class="pa-4">
                            <div class="d-flex flex-column ga-2">
                                <VBtn
                                    block
                                    variant="tonal"
                                    color="primary"
                                    prepend-icon="mdi-plus"
                                    href="/application/create"
                                    class="justify-start text-none"
                                >
                                    Create New Application
                                </VBtn>

                                <template v-if="isAdmin || isDyeo">
                                    <VBtn
                                        block
                                        variant="tonal"
                                        color="secondary"
                                        prepend-icon="mdi-cards-outline"
                                        href="/clubs"
                                        class="justify-start text-none"
                                    >
                                        Manage Rotary Clubs
                                    </VBtn>
                                </template>

                                <template v-if="isAdmin">
                                    <VBtn
                                        block
                                        variant="tonal"
                                        color="info"
                                        prepend-icon="mdi-account-group"
                                        href="/dyeos"
                                        class="justify-start text-none"
                                    >
                                        District DYEO Officers
                                    </VBtn>
                                </template>

                                <template v-if="isAdmin">
                                    <VBtn
                                        block
                                        variant="tonal"
                                        color="indigo"
                                        prepend-icon="mdi-account-multiple"
                                        href="/users"
                                        class="justify-start text-none"
                                    >
                                        System Users & Permissions
                                    </VBtn>
                                </template>

                                <VBtn
                                    block
                                    variant="tonal"
                                    color="grey-darken-1"
                                    prepend-icon="mdi-cog-outline"
                                    href="/settings/profile"
                                    class="justify-start text-none"
                                >
                                    Account & Settings
                                </VBtn>
                            </div>
                        </VCardText>
                    </VCard>
                </VCol>
            </VRow>

            <!-- Recent Applications Table Widget -->
            <VRow>
                <VCol cols="12">
                    <VCard border elevation="2" rounded="lg">
                        <VCardItem class="border-b">
                            <template #prepend>
                                <VIcon
                                    icon="mdi-clipboard-list-outline"
                                    color="primary"
                                    class="mr-2"
                                />
                            </template>
                            <VCardTitle>Recent Applications</VCardTitle>
                            <template #append>
                                <VBtn
                                    variant="text"
                                    color="primary"
                                    size="small"
                                    href="/applications"
                                >
                                    View All Applications
                                    <VIcon end icon="mdi-chevron-right" />
                                </VBtn>
                            </template>
                        </VCardItem>

                        <VDataTable
                            :headers="recentApplicationsHeaders"
                            :items="recentApplications"
                            :items-per-page="8"
                            density="compact"
                            hover
                            no-data-text="No recent applications available."
                        >
                            <template #item.application_no="{ item }">
                                <span class="font-weight-bold text-primary">
                                    {{ item.application_no || `#${item.id}` }}
                                </span>
                            </template>

                            <template #item.full_name="{ item }">
                                <div class="d-flex flex-column">
                                    <span class="font-weight-medium">{{
                                        item.full_name
                                    }}</span>
                                    <span
                                        class="text-caption text-medium-emphasis"
                                        >{{ item.email_address }}</span
                                    >
                                </div>
                            </template>

                            <template #item.exchange_type="{ item }">
                                <VChip
                                    size="small"
                                    :color="
                                        item.exchange_type === 'CAMPS & TOURS'
                                            ? 'info'
                                            : 'teal'
                                    "
                                    variant="outlined"
                                >
                                    {{ item.exchange_type || 'STEP' }}
                                </VChip>
                            </template>

                            <template #item.application_status="{ item }">
                                <VChip
                                    size="small"
                                    :color="
                                        getStatusColor(item.application_status)
                                    "
                                    variant="flat"
                                >
                                    {{ item.application_status || 'Submitted' }}
                                </VChip>
                            </template>

                            <template #item.actions="{ item }">
                                <div class="d-flex justify-center ga-1">
                                    <VTooltip
                                        text="Edit Application"
                                        location="top"
                                    >
                                        <template
                                            #activator="{ props: tooltipProps }"
                                        >
                                            <VBtn
                                                v-bind="tooltipProps"
                                                icon="mdi-pencil-outline"
                                                size="small"
                                                variant="text"
                                                color="primary"
                                                :href="`/applications/${item.id}/edit`"
                                            />
                                        </template>
                                    </VTooltip>

                                    <VTooltip text="View PDF" location="top">
                                        <template
                                            #activator="{ props: tooltipProps }"
                                        >
                                            <VBtn
                                                v-bind="tooltipProps"
                                                icon="mdi-file-pdf-box"
                                                size="small"
                                                variant="text"
                                                color="error"
                                                :href="`/applications-pdf-view/${item.id}`"
                                                target="_blank"
                                            />
                                        </template>
                                    </VTooltip>
                                </div>
                            </template>
                        </VDataTable>
                    </VCard>
                </VCol>
            </VRow>
        </template>
    </div>
</template>

<style scoped>
.dashboard-container {
    padding: 0;
}




.kpi-card {
    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease;
}

.kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
}
</style>
