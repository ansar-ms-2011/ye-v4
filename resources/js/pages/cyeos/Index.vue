<script setup lang="ts">
import { Head, Form, router, Deferred } from '@inertiajs/vue3';
import { ref, shallowRef, toRef, watch } from 'vue';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import { getNewCyeo } from '@/helpers';
import { index, store, update, destroy } from '@/routes/cyeos';
import type { Cyeo } from '@/types';

const props = defineProps<{
    cyeoPaginator: any;
    clubs: any;
    districts: any[];
    filters?: { searchText?: string };
}>();

const deleteDialog = ref(false);
const deleteTarget = ref<any>(null);
const deleting = ref(false);
const formModel = ref<Cyeo>(getNewCyeo());
const dialog = shallowRef(false);
const searchText = ref(props.filters?.searchText ?? '');
const isEditing = toRef(() => !!formModel.value.id);
let debounceTimer: ReturnType<typeof setTimeout> | null = null;
const isSearching = ref(false);

const headers = [
    {
        title: 'Actions',
        key: 'actions',
        align: 'center' as const,
        sortable: false,
    },
    {
        title: 'District',
        key: 'district',
        align: 'start' as const,
        nowrap: true,
    },
    {
        title: 'Club Name',
        key: 'club_name',
        align: 'start' as const,
        nowrap: true,
    },
    {
        title: 'CYEO Name',
        key: 'cyeo_name',
        align: 'start' as const,
        nowrap: true,
    },
    {
        title: 'Email',
        key: 'cyeo_email',
        align: 'start' as const,
        nowrap: true,
    },
    {
        title: 'Address',
        key: 'cyeo_address',
        align: 'start' as const,
        nowrap: true,
    },
    {
        title: 'City',
        key: 'cyeo_city',
        align: 'start' as const,
        nowrap: true,
    },
    {
        title: 'State',
        key: 'cyeo_state',
        align: 'start' as const,
        nowrap: true,
    },
    {
        title: 'Post Code',
        key: 'cyeo_postcode',
        align: 'start' as const,
        nowrap: true,
    },
    {
        title: 'Country',
        key: 'cyeo_country',
        align: 'start' as const,
        nowrap: true,
    },
    {
        title: 'Home Tel.',
        key: 'cyeo_htel',
        align: 'start' as const,
        nowrap: true,
    },
    {
        title: 'Work Tel.',
        key: 'cyeo_wtel',
        align: 'start' as const,
        nowrap: true,
    },
    {
        title: 'Mobile',
        key: 'cyeo_mobile',
        align: 'start' as const,
        nowrap: true,
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
            perPage: props.cyeoPaginator.per_page,
            searchText: search || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['cyeoPaginator'],
            onFinish: () => {
                isSearching.value = false;
            },
        },
    );
}

function add() {
    formModel.value = getNewCyeo();
    dialog.value = true;
}

function edit(cyeo: Cyeo) {
    if (!cyeo) {
        return;
    }

    formModel.value = {
        id: cyeo.id,
        cyeo_name: cyeo.cyeo_name ?? '',
        cyeo_sig: cyeo.cyeo_sig ?? '',
        cyeo_address: cyeo.cyeo_address ?? '',
        cyeo_city: cyeo.cyeo_city ?? '',
        cyeo_state: cyeo.cyeo_state ?? '',
        cyeo_postcode: cyeo.cyeo_postcode ?? '',
        cyeo_country: cyeo.cyeo_country ?? '',
        cyeo_htel: cyeo.cyeo_htel ?? '',
        cyeo_wtel: cyeo.cyeo_wtel ?? '',
        cyeo_mobile: cyeo.cyeo_mobile ?? '',
        cyeo_fax: cyeo.cyeo_fax ?? '',
        cyeo_email: cyeo.cyeo_email ?? '',
        application_no: cyeo.application_no ?? null,
        ribi_club_id: cyeo.ribi_club_id ?? null,
        district_id: cyeo.club?.district_id ?? null,
        user_id: cyeo.user_id ?? null,
        created_at: cyeo.created_at ?? null,
        updated_at: cyeo.updated_at ?? null,
    };

    dialog.value = true;
}

function onRowClick(_event: MouseEvent, { item }: { item: Cyeo }) {
    edit(item);
}

function reset() {
    dialog.value = false;
    formModel.value = getNewCyeo();
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
            only: ['cyeoPaginator'],
            onFinish: () => {
                isSearching.value = false;
            },
        },
    );
}

function closeDialog() {
    dialog.value = false;
}

function onPageChange(newPage: number) {
    router.get(
        index().url,
        {
            page: newPage,
            perPage: props.cyeoPaginator.perPage,
            ...(searchText.value && { searchText: searchText.value }),
        },
        { preserveState: true, preserveScroll: true, only: ['cyeoPaginator'] },
    );
}

function onPerPageChange(newPerPage: number) {
    router.get(
        index().url,
        {
            page: 1,
            perPage: newPerPage,
            ...(searchText.value && { searchText: searchText.value }),
        },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['cyeoPaginator'],
        },
    );
}

function onFormSuccess() {
    reset();
}

function confirmRemove(item: Cyeo) {
    deleteTarget.value = item;
    deleteDialog.value = true;
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
</script>

<template>
    <Head title="Club Youth Exchange Officers" />
    <div class="app-page">
        <v-sheet border rounded>
            <Deferred data="cyeoPaginator">
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
                        density="compact"
                        :headers="headers"
                        hover
                        class="elevation-1"
                        :items="cyeoPaginator?.data"
                        :items-length="cyeoPaginator.total"
                        :items-per-page="cyeoPaginator.per_page"
                        :items-per-page-options="[15, 35, 65, 100]"
                        :page="cyeoPaginator.current_page"
                        :loading="isSearching"
                        @update:page="onPageChange"
                        @update:items-per-page="onPerPageChange"
                        @dblclick:row="onRowClick"
                    >
                        <template v-slot:top>
                            <v-toolbar flat>
                                <v-toolbar-title>
                                    <v-icon
                                        color="medium-emphasis"
                                        icon="mdi-account-multiple"
                                        size="x-small"
                                        start
                                    ></v-icon>
                                    CYEOs
                                </v-toolbar-title>

                                <VSpacer></VSpacer>
                                <VTextField
                                    v-model="searchText"
                                    prepend-inner-icon="mdi-magnify"
                                    rounded="lg"
                                    placeholder="Search the Club Youth Exchange Officers"
                                    class="me-2"
                                    density="compact"
                                ></VTextField>

                                <v-btn
                                    color="primary"
                                    class="me-2"
                                    prepend-icon="mdi-plus"
                                    rounded="lg"
                                    text="Add New CYEO"
                                    variant="outlined"
                                    @click="add"
                                ></v-btn>
                            </v-toolbar>
                        </template>

                        <template v-slot:[`item.actions`]="{ item }">
                            <div class="d-flex ga-2 justify-end">
                                <v-icon
                                    color="success"
                                    icon="mdi-square-edit-outline"
                                    size="small"
                                    @click.stop="edit(item as Cyeo)"
                                ></v-icon>

                                <v-icon
                                    color="red"
                                    icon="mdi-delete"
                                    size="small"
                                    @click.stop="confirmRemove(item as Cyeo)"
                                ></v-icon>
                            </div>
                        </template>

                        <template
                            v-slot:[`item.district`]="{ item }: { item: any }"
                        >
                            {{ item.club?.district?.code }}
                        </template>

                        <template
                            v-slot:[`item.club_name`]="{ item }: { item: any }"
                        >
                            {{ item.club?.club_name }}
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
        </v-sheet>

        <v-dialog v-model="dialog" max-width="800">
            <Form
                v-bind="isEditing ? update.form(formModel.id!) : store.form()"
                v-slot="{ errors, processing }"
                @success="onFormSuccess"
            >
                <v-card
                    border
                    elevation="2"
                    :title="`${isEditing ? 'Update' : 'Create'}`"
                    :subtitle="`${isEditing ? 'Edit the existing' : 'Add a new'} club youth exchange officer `"
                >
                    <template v-slot:append>
                        <v-btn
                            icon="mdi-close"
                            variant="text"
                            density="comfortable"
                            size="small"
                            @click="closeDialog"
                        />
                    </template>
                    <template v-slot:text>
                        <v-row density="compact" class="ma-0">
                            <v-col cols="12" md="6" class="py-1">
                                <VTextField
                                    name="cyeo_name"
                                    v-model="formModel.cyeo_name"
                                    label="Name"
                                    density="compact"
                                    variant="outlined"
                                    hide-details="auto"
                                    :error-messages="errors.cyeo_name"
                                ></VTextField>
                            </v-col>
                            <v-col cols="12" md="6" class="py-1">
                                <VTextField
                                    name="cyeo_email"
                                    label="Email"
                                    type="email"
                                    v-model="formModel.cyeo_email"
                                    density="compact"
                                    variant="outlined"
                                    hide-details="auto"
                                    :error-messages="errors.cyeo_email"
                                ></VTextField>
                            </v-col>
                            <v-col cols="12" md="6" class="py-1">
                                <VTextField
                                    name="cyeo_address"
                                    label="Address"
                                    v-model="formModel.cyeo_address"
                                    density="compact"
                                    variant="outlined"
                                    hide-details="auto"
                                    :error-messages="errors.cyeo_address"
                                ></VTextField>
                            </v-col>
                            <v-col cols="12" md="6" class="py-1">
                                <VTextField
                                    name="cyeo_country"
                                    label="Country"
                                    v-model="formModel.cyeo_country"
                                    density="compact"
                                    variant="outlined"
                                    hide-details="auto"
                                    :error-messages="errors.cyeo_country"
                                ></VTextField>
                            </v-col>
                            <v-col cols="12" md="6" class="py-1">
                                <VTextField
                                    name="cyeo_city"
                                    label="City"
                                    v-model="formModel.cyeo_city"
                                    density="compact"
                                    variant="outlined"
                                    hide-details="auto"
                                    :error-messages="errors.cyeo_city"
                                ></VTextField>
                            </v-col>
                            <v-col cols="12" md="6" class="py-1">
                                <VTextField
                                    name="cyeo_state"
                                    label="City"
                                    v-model="formModel.cyeo_state"
                                    density="compact"
                                    variant="outlined"
                                    hide-details="auto"
                                    :error-messages="errors.cyeo_state"
                                ></VTextField>
                            </v-col>
                            <v-col cols="12" md="6" class="py-1">
                                <VTextField
                                    name="cyeo_postcode"
                                    label="Post Code"
                                    v-model="formModel.cyeo_postcode"
                                    density="compact"
                                    variant="outlined"
                                    hide-details="auto"
                                    :error-messages="errors.cyeo_postcode"
                                ></VTextField>
                            </v-col>
                            <v-col cols="12" md="6" class="py-1">
                                <VTextField
                                    name="cyeo_htel"
                                    label="Home Telephone"
                                    v-model="formModel.cyeo_htel"
                                    density="compact"
                                    variant="outlined"
                                    hide-details="auto"
                                    :error-messages="errors.cyeo_htel"
                                ></VTextField>
                            </v-col>
                            <v-col cols="12" md="6" class="py-1">
                                <VTextField
                                    name="cyeo_wtel"
                                    label="Work Telephone"
                                    v-model="formModel.cyeo_wtel"
                                    density="compact"
                                    variant="outlined"
                                    hide-details="auto"
                                    :error-messages="errors.cyeo_wtel"
                                ></VTextField>
                            </v-col>
                            <v-col cols="12" md="6" class="py-1">
                                <VTextField
                                    name="cyeo_mobile"
                                    label="Mobile Number"
                                    v-model="formModel.cyeo_mobile"
                                    density="compact"
                                    variant="outlined"
                                    hide-details="auto"
                                    :error-messages="errors.cyeo_mobile"
                                ></VTextField>
                            </v-col>
                            <v-col cols="12" md="6" class="py-1">
                                <VAutocomplete
                                    name="district_id"
                                    itemValue="id"
                                    itemTitle="code"
                                    variant="outlined"
                                    density="compact"
                                    v-model="formModel.district_id"
                                    :items="districts"
                                    label="District"
                                    hide-details="auto"
                                    :error-messages="errors.district_id"
                                ></VAutocomplete>
                            </v-col>
                            <v-col cols="12" md="6" class="py-1">
                                <VAutocomplete
                                    name="ribi_club_id"
                                    itemValue="id"
                                    itemTitle="club_name"
                                    variant="outlined"
                                    density="compact"
                                    v-model="formModel.ribi_club_id"
                                    :items="clubs"
                                    label="Ribi Club"
                                    hide-details="auto"
                                    :error-messages="errors.ribi_club_id"
                                ></VAutocomplete>
                            </v-col>
                        </v-row>
                    </template>

                    <v-divider></v-divider>

                    <v-card-actions class="bg-surface-light">
                        <v-btn
                            text="Cancel"
                            variant="plain"
                            @click="dialog = false"
                        ></v-btn>

                        <v-spacer></v-spacer>

                        <v-btn
                            :loading="processing"
                            :disabled="processing"
                            class="round-md"
                            text="Save"
                            type="submit"
                            color="primary"
                            variant="flat"
                        >
                            Save CYEO
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </Form>
        </v-dialog>
        <ConfirmDialog
            v-model="deleteDialog"
            title="Confirm Delete"
            message="Are you sure you want to delete?"
            :target-name="deleteTarget?.name"
            confirm-text="Delete"
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
</style>
