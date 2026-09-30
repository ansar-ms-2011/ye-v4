<script setup lang="ts">
import { Head, Form, router, Deferred } from '@inertiajs/vue3';
import { ref, shallowRef, toRef, watch } from 'vue';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import { getNewDyeo } from '@/helpers';
import { index, store, update, destroy } from '@/routes/dyeos';
import type { Dyeo } from '@/types';

const props = defineProps<{
    dyeoPaginator?: any;
    districts: any[];
    filters?: {
        searchText?: string;
    };
}>();

const deleteDialog = ref(false);
const deleteTarget = ref<any>(null);
const deleting = ref(false);
const formModel = ref<Dyeo>(getNewDyeo());
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
        fixed: true,
    },
    {
        title: 'District',
        key: 'district_code',
        align: 'start' as const,
        nowrap: true,
    },
    {
        title: 'DEYO Name',
        key: 'dyeo_name',
        align: 'start' as const,
        nowrap: true,
    },
    {
        title: 'Email',
        key: 'dyeo_email',
        align: 'start' as const,
        nowrap: true,
    },
    {
        title: 'Address',
        key: 'dyeo_address',
        align: 'start' as const,
        nowrap: true,
    },
    {
        title: 'City',
        key: 'dyeo_city',
        align: 'start' as const,
        nowrap: true,
    },
    {
        title: 'State',
        key: 'dyeo_state',
        align: 'start' as const,
        nowrap: true,
    },
    {
        title: 'Post Code',
        key: 'dyeo_postcode',
        align: 'start' as const,
        nowrap: true,
    },
    {
        title: 'Country',
        key: 'dyeo_country',
        align: 'start' as const,
        nowrap: true,
    },
    {
        title: 'Home Tel.',
        key: 'dyeo_htel',
        align: 'start' as const,
        nowrap: true,
    },
    {
        title: 'Work Tel.',
        key: 'dyeo_wtel',
        align: 'start' as const,
        nowrap: true,
    },
    {
        title: 'Mobile',
        key: 'dyeo_mobile',
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
            perPage: props.dyeoPaginator.per_page,
            searchText: search || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['dyeoPaginator'],
            onFinish: () => {
                isSearching.value = false;
            },
        },
    );
}

function add() {
    formModel.value = getNewDyeo();
    dialog.value = true;
}

function edit(dyeo: Dyeo) {
    if (!dyeo) {
        return;
    }

    formModel.value = {
        id: dyeo.id,
        district_code: dyeo.district_code ?? '',
        dyeo_name: dyeo.dyeo_name ?? '',
        dyeo_address: dyeo.dyeo_address ?? '',
        dyeo_contact_no: dyeo.dyeo_contact_no ?? '',
        dyeo_city: dyeo.dyeo_city ?? '',
        dyeo_state: dyeo.dyeo_state ?? '',
        dyeo_postcode: dyeo.dyeo_postcode ?? '',
        dyeo_country: dyeo.dyeo_country ?? '',
        dyeo_htel: dyeo.dyeo_htel ?? '',
        dyeo_wtel: dyeo.dyeo_wtel ?? '',
        dyeo_mobile: dyeo.dyeo_mobile ?? '',
        dyeo_fax: dyeo.dyeo_fax ?? '',
        dyeo_email: dyeo.dyeo_email ?? '',
        user_id: dyeo.user_id ?? null,
        created_at: dyeo.created_at ?? null,
        updated_at: dyeo.updated_at ?? null,
    };

    dialog.value = true;
}

function onRowClick(_event: MouseEvent, { item }: { item: Dyeo }) {
    edit(item);
}

function reset() {
    dialog.value = false;
    formModel.value = getNewDyeo();
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
            only: ['dyeoPaginator'],
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
            perPage: props.dyeoPaginator.perPage,
            ...(searchText.value && { searchText: searchText.value }),
        },
        { preserveState: true, preserveScroll: true, only: ['dyeoPaginator'] },
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

function confirmRemove(item: Dyeo) {
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
        <v-sheet border rounded :elevation="2">
            <Deferred data="dyeoPaginator">
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
                        class="overflow-hidden"
                        :items="dyeoPaginator?.data"
                        :items-length="dyeoPaginator.total"
                        :items-per-page="dyeoPaginator.per_page"
                        :items-per-page-options="[15, 35, 65, 100]"
                        :page="dyeoPaginator.current_page"
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
                                    DYEOs
                                </v-toolbar-title>

                                <VSpacer></VSpacer>
                                <VTextField
                                    v-model="searchText"
                                    prepend-inner-icon="mdi-magnify"
                                    rounded="lg"
                                    placeholder="Search the District Youth Exchange Officers"
                                    class="me-2 bg-white"
                                    density="compact"
                                ></VTextField>

                                <v-btn
                                    color="primary"
                                    class="me-2"
                                    prepend-icon="mdi-plus"
                                    rounded="lg"
                                    text="Add New DYEO"
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
                                    @click.stop="edit(item as Dyeo)"
                                ></v-icon>

                                <v-icon
                                    color="red"
                                    icon="mdi-delete"
                                    size="small"
                                    @click.stop="confirmRemove(item as Dyeo)"
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
                    :subtitle="`${isEditing ? 'Edit the existing' : 'Add a new'} district youth exchange officer `"
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
                                    name="dyeo_name"
                                    v-model="formModel.dyeo_name"
                                    label="Name"
                                    density="compact"
                                    variant="outlined"
                                    hide-details="auto"
                                    :error-messages="errors.dyeo_name"
                                ></VTextField>
                            </v-col>
                            <v-col cols="12" md="6" class="py-1">
                                <VTextField
                                    name="dyeo_email"
                                    label="Email"
                                    type="email"
                                    v-model="formModel.dyeo_email"
                                    density="compact"
                                    variant="outlined"
                                    hide-details="auto"
                                    :error-messages="errors.dyeo_email"
                                ></VTextField>
                            </v-col>
                            <v-col cols="12" md="6" class="py-1">
                                <VTextField
                                    name="dyeo_address"
                                    label="Address"
                                    v-model="formModel.dyeo_address"
                                    density="compact"
                                    variant="outlined"
                                    hide-details="auto"
                                    :error-messages="errors.dyeo_address"
                                ></VTextField>
                            </v-col>
                            <v-col cols="12" md="6" class="py-1">
                                <VTextField
                                    name="dyeo_country"
                                    label="Country"
                                    v-model="formModel.dyeo_country"
                                    density="compact"
                                    variant="outlined"
                                    hide-details="auto"
                                    :error-messages="errors.dyeo_country"
                                ></VTextField>
                            </v-col>
                            <v-col cols="12" md="6" class="py-1">
                                <VTextField
                                    name="dyeo_city"
                                    label="City"
                                    v-model="formModel.dyeo_city"
                                    density="compact"
                                    variant="outlined"
                                    hide-details="auto"
                                    :error-messages="errors.dyeo_city"
                                ></VTextField>
                            </v-col>
                            <v-col cols="12" md="6" class="py-1">
                                <VTextField
                                    name="dyeo_state"
                                    label="City"
                                    v-model="formModel.dyeo_state"
                                    density="compact"
                                    variant="outlined"
                                    hide-details="auto"
                                    :error-messages="errors.dyeo_state"
                                ></VTextField>
                            </v-col>
                            <v-col cols="12" md="6" class="py-1">
                                <VTextField
                                    name="dyeo_postcode"
                                    label="Post Code"
                                    v-model="formModel.dyeo_postcode"
                                    density="compact"
                                    variant="outlined"
                                    hide-details="auto"
                                    :error-messages="errors.dyeo_postcode"
                                ></VTextField>
                            </v-col>
                            <v-col cols="12" md="6" class="py-1">
                                <VTextField
                                    name="dyeo_htel"
                                    label="Home Telephone"
                                    v-model="formModel.dyeo_htel"
                                    density="compact"
                                    variant="outlined"
                                    hide-details="auto"
                                    :error-messages="errors.dyeo_htel"
                                ></VTextField>
                            </v-col>
                            <v-col cols="12" md="6" class="py-1">
                                <VTextField
                                    name="dyeo_wtel"
                                    label="Work Telephone"
                                    v-model="formModel.dyeo_wtel"
                                    density="compact"
                                    variant="outlined"
                                    hide-details="auto"
                                    :error-messages="errors.dyeo_wtel"
                                ></VTextField>
                            </v-col>
                            <v-col cols="12" md="6" class="py-1">
                                <VTextField
                                    name="dyeo_mobile"
                                    label="Mobile Number"
                                    v-model="formModel.dyeo_mobile"
                                    density="compact"
                                    variant="outlined"
                                    hide-details="auto"
                                    :error-messages="errors.dyeo_mobile"
                                ></VTextField>
                            </v-col>
                            <v-col cols="12" md="6" class="py-1">
                                <VAutocomplete
                                    name="district_code"
                                    itemValue="code"
                                    itemTitle="code"
                                    variant="outlined"
                                    density="compact"
                                    v-model="formModel.district_code"
                                    :items="districts"
                                    label="District"
                                    hide-details="auto"
                                    :error-messages="errors.district_code"
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
                            Save DYEO
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
