<script setup lang="ts">
import { Head, router, Deferred } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import { index, destroy, create, edit } from '@/routes/application';
import type { Application } from '@/types';
import { format, parseISO } from 'date-fns';

const props = defineProps<{
    applicationsPaginator?: any;
    filters?: {
        searchText?: string;
    };
}>();

const deleteDialog = ref(false);
const deleteTarget = ref<any>(null);
const deleting = ref(false);
const searchText = ref(props.filters?.searchText ?? '');
let debounceTimer: ReturnType<typeof setTimeout> | null = null;
const isSearching = ref(false);
const defaultSort = [{ key: 'application_no', order: 'desc' }] as any;

const headers = [
    {
        title: 'Actions',
        key: 'actions',
        align: 'center' as const,
        sortable: false,
        fixed: true,
        width: '50px',
    },
    {
        title: 'District',
        key: 'district_code',
        align: 'start' as const,
        sortable: false,
        nowrap: true,
        width: '10px',
    },
    {
        title: 'First Name',
        key: 'firstname',
        align: 'start' as const,
        nowrap: true,
    },
    {
        title: 'Surname',
        key: 'surname',
        align: 'start' as const,
        nowrap: true,
    },
    {
        title: 'App. No',
        key: 'application_no',
        align: 'start' as const,
        nowrap: true,
    },
    {
        title: 'App. Type',
        key: 'exchange_type',
        align: 'start' as const,
        nowrap: true,
    },
    {
        title: 'DOB',
        key: 'dob',
        align: 'start' as const,
        nowrap: true,
    },
    {
        title: 'Gender',
        key: 'gender',
        align: 'start' as const,
        nowrap: true,
    },
    {
        title: 'Status',
        key: 'application_status',
        align: 'start' as const,
        nowrap: true,
    },
    {
        title: 'Email',
        key: 'email_address',
        align: 'start' as const,
        nowrap: true,
    },
    {
        title: 'DYEO',
        key: 'dyeo_name',
        align: 'start' as const,
        sortable: false,
        nowrap: true,
    },
    {
        title: 'App. Date',
        key: 'date_of_app',
        align: 'start' as const,
        nowrap: true,
        lastFixed: true,
    },
];

// Debounced search watcher
watch(searchText, (newValue) => {
    if (debounceTimer) {
        clearTimeout(debounceTimer);
    }

    debounceTimer = setTimeout(() => {
        performSearch(newValue);
    }, 400);
});

function performSearch(search: string) {
    isSearching.value = true;
    router.get(
        index().url,
        {
            page: 1,
            perPage: props.applicationsPaginator.per_page,
            searchText: search || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['applicationsPaginator'],
            onFinish: () => {
                isSearching.value = false;
            },
        },
    );
}

function addNewApplication() {
    router.get(create().url);
}
function clearSearch() {
    searchText.value = '';
    isSearching.value = true;
    router.get(
        index().url,
        {},
        {
            preserveState: true,
            preserveScroll: true,
            only: ['applicationsPaginator'],
            onFinish: () => {
                isSearching.value = false;
            },
        },
    );
}

function onOptionsChange({ page, itemsPerPage, sortBy }: any) {
    router.get(
        index().url,
        {
            page: page,
            perPage: itemsPerPage,
            ...(sortBy && { sortBy: sortBy[0]?.key }),
            ...(sortBy && { sortOrder: sortBy[0]?.order }),
            ...(searchText.value && { searchText: searchText.value }),
        },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['applicationsPaginator'],
        },
    );
}

async function remove() {
    if (!deleteTarget.value) {
        return;
    }

    deleting.value = true;

    try {
        router.delete(destroy(deleteTarget.value?.id).url, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                cancelRemove();
            },
            onFinish: () => {
                deleting.value = false;
            },
        });
    } finally {
        deleting.value = false;
    }
}

function cancelRemove() {
    deleteDialog.value = false;
    deleteTarget.value = null;
}

const showContextMenu = ref(false);
const contextMenuTarget = ref(null);
const contextMenuTargetedApplication = ref<Application | null>(null);
const contextMenuItems = computed(() => {
    if (!contextMenuTargetedApplication.value) {
        return [];
    }

    const editName =
        'Edit Application No: ' +
        contextMenuTargetedApplication.value.application_no;

    if (contextMenuTargetedApplication.value.exchange_type === 'STEP') {
        return [
            {
                title: editName,
                prependIcon: 'mdi-square-edit-outline',
                code: 'edit',
                color: 'success-darker-3',
            },
            { type: 'divider' },
            {
                title: 'View Application in PDF',
                prependIcon: 'mdi-file-pdf-box',
                code: 'view_full_pdf',
                color: 'secondary',
            },
            {
                title: 'View Signing Page',
                prependIcon: 'mdi-file-pdf-box',
                code: 'signing_pdf_full',
                color: 'secondary',
            },
            { type: 'divider' },
            {
                title: 'Remove',
                prependIcon: 'mdi-trash-can',
                code: 'delete',
                color: 'error-darker-3',
            },
        ];
    } else {
        return [
            {
                title: editName,
                prependIcon: 'mdi-square-edit-outline',
                code: 'edit',
                color: 'success-darker-3',
            },
            { type: 'divider' },
            {
                title: 'View Application in PDF',
                prependIcon: 'mdi-file-pdf-box',
                code: 'view_full_pdf',
                color: 'secondary',
            },
            {
                title: 'View Signing Page 3',
                prependIcon: 'mdi-file-pdf-box',
                code: 'signing_pdf_3',
                color: 'secondary',
            },
            {
                title: 'View Signing Page 5-6',
                prependIcon: 'mdi-file-pdf-box',
                code: 'signing_pdf_5_6',
                color: 'secondary',
            },
            { type: 'divider' },
            {
                title: 'Remove',
                prependIcon: 'mdi-trash-can',
                code: 'delete',
                color: 'error-darker-3',
            },
        ];
    }
});

async function show(evt: any, application: Application) {
    document
        .querySelector('.icon-btn-applications-active')
        ?.classList.remove('icon-btn-applications-active');

    contextMenuTargetedApplication.value = application;

    if (showContextMenu.value) {
        showContextMenu.value = false;
        await new Promise((resolve) => setTimeout(resolve, 100));
    }

    contextMenuTarget.value = evt.target.closest('.v-icon-btn');
    showContextMenu.value = true;

    evt.target.parentElement?.parentElement?.classList.add(
        'icon-btn-applications-active',
    );
}

function handleMenuItemClick({ id, value, code }: any) {
    if (id === 'edit' && contextMenuTargetedApplication.value?.id) {
        router.visit(edit({ id: contextMenuTargetedApplication.value?.id }));
    }

    console.log('Clicked item value:', value); // value = item.code
    console.log('Clicked item id:', id);
    console.log('Clicked item code:', code);
}

function hide() {
    document
        .querySelector('.icon-btn-applications-active')
        ?.classList.remove('icon-btn-applications-active');
}
function getRowProps(row: any) {
    if (row?.item?.application_season_class) {
        return { class: row?.item?.application_season_class };
    } else {
        return {};
    }
}
</script>

<template>
    <Head title="Club Youth Exchange Officers" />
    <div class="app-page">
        <v-sheet border rounded :elevation="2">
            <Deferred data="applicationsPaginator">
                <template #fallback>
                    <div
                        class="d-flex align-center justify-center"
                        style="min-height: 500px"
                    >
                        <span>Loading youth exchange officers' data...</span>
                    </div>
                </template>
                <template #rescue>
                    <div
                        class="d-flex align-center justify-center"
                        style="min-height: 500px"
                    >
                        <span class="text-red"
                            >An error has been while fetching data.</span
                        >
                    </div>
                </template>
                <template #default="{ reloading }">
                    <v-data-table-server
                        hover
                        density="compact"
                        :headers="headers"
                        class="overflow-hidden"
                        :items="applicationsPaginator?.data"
                        :items-length="applicationsPaginator.total"
                        :items-per-page="applicationsPaginator.per_page"
                        :items-per-page-options="[15, 35, 65, 100]"
                        :page="applicationsPaginator.current_page"
                        :loading="isSearching"
                        :sort-by="defaultSort"
                        :row-props="getRowProps"
                        @update:options="onOptionsChange"
                    >
                        <template v-slot:top>
                            <v-toolbar flat>
                                <v-toolbar-title>
                                    <v-icon
                                        color="medium-emphasis"
                                        icon="mdi-file-document-multiple-outline"
                                        start
                                    ></v-icon>
                                    Applications
                                </v-toolbar-title>

                                <VSpacer></VSpacer>
                                <VTextField
                                    v-model="searchText"
                                    prepend-inner-icon="mdi-magnify"
                                    rounded="lg"
                                    placeholder="Search the applications"
                                    class="me-2 bg-white"
                                    density="compact"
                                ></VTextField>

                                <v-btn
                                    color="primary"
                                    class="me-2"
                                    prepend-icon="mdi-plus"
                                    rounded="lg"
                                    text="Add New Application"
                                    variant="outlined"
                                    @click="addNewApplication"
                                ></v-btn>
                            </v-toolbar>
                        </template>

                        <template v-slot:[`item.actions`]="{ item }">
                            <div class="ga-2">
                                <v-icon-btn
                                    icon="mdi-dots-vertical"
                                    size="small"
                                    hover-elevation="5"
                                    class="icon-btn-applications mx-2"
                                    variant="outlined"
                                    @click.stop="
                                        (evt: any) =>
                                            show(evt, item as Application)
                                    "
                                ></v-icon-btn>
                            </div>
                        </template>

                        <template
                            v-slot:[`item.district_code`]="{
                                item,
                            }: {
                                item: any;
                            }"
                        >
                            {{ item.dyeo?.district_code }}
                        </template>

                        <template
                            v-slot:[`item.application_no`]="{
                                item,
                            }: {
                                item: any;
                            }"
                        >
                            <span class="font-weight-bold text-primary">
                                {{ item.application_no }}
                            </span>
                        </template>

                        <template
                            v-slot:[`item.exchange_type`]="{
                                item,
                            }: {
                                item: any;
                            }"
                        >
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

                        <template
                            v-slot:[`item.gender`]="{ item }: { item: any }"
                        >
                            <VChip
                                size="small"
                                :color="
                                    item.gender === 'Female'
                                        ? 'error-lighten-3'
                                        : 'success-darker-3'
                                "
                            >
                                {{ item.gender }}
                            </VChip>
                        </template>

                        <template
                            v-slot:[`item.dyeo_name`]="{ item }: { item: any }"
                        >
                            {{ item.dyeo?.dyeo_name }}
                        </template>

                        <template
                            v-slot:[`item.date_of_app`]="{
                                item,
                            }: {
                                item: any;
                            }"
                        >
                            {{
                                format(parseISO(item.date_of_app), 'dd-MM-yyyy')
                            }}
                        </template>

                        <template v-slot:no-data>
                            <div class="d-flex justify-center pa-4">
                                <span v-if="reloading"
                                    >Deferred data is being loaded ...</span
                                >
                                <v-btn
                                    v-else
                                    prepend-icon="mdi-backup-restore"
                                    rounded="lg"
                                    text="Reset filters"
                                    variant="text"
                                    @click="clearSearch"
                                ></v-btn>
                            </div>
                        </template> </v-data-table-server
                ></template>
            </Deferred>

            <v-menu
                class="mx-2 hover-elevation-5"
                v-if="contextMenuTarget"
                v-model="showContextMenu"
                location="end"
                :offset="[5, -5]"
                :target="contextMenuTarget"
                scroll-strategy="close"
                @update:model-value="hide"
            >
                <v-list
                    :items="contextMenuItems"
                    class="py-0"
                    density="compact"
                    item-value="code"
                    item-props
                    @click:select="handleMenuItemClick"
                    slim
                >
                    <template v-slot:prepend="{ item }">
                        <v-icon
                            class="mr-n2"
                            :icon="item.prependIcon"
                            :color="item.color"
                        ></v-icon>
                    </template>
                </v-list>
            </v-menu>
        </v-sheet>

        <ConfirmDialog
            v-model="deleteDialog"
            title="Confirm Delete"
            message="Are you sure you want to delete?"
            confirm-text="Delete"
            :target-name="deleteTarget?.name"
            :loading="deleting"
            @confirm="remove"
            @cancel="cancelRemove"
        />
    </div>
</template>

<style scoped>
:deep(.v-toolbar__content) {
    height: 56px !important;
}

.icon-btn-applications:hover {
    background: rgb(var(--v-theme-secondary-darken-1)) !important;
    color: white !important;
    transition: background-color 0.3s ease-in-out;
    scale: 1.2;
}
.icon-btn-applications-active {
    background: rgb(var(--v-theme-surface-variant)) !important;
    border: 2px solid rgb(var(--v-theme-primary));
}

:deep(.previous_season td) {
    opacity: 0.7 !important;
    background-color: rgb(var(--v-theme-surface-variant)) !important;
    transition: opacity 0.3s ease-in-out;
}
</style>
