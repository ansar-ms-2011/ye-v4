<script setup lang="ts">
import { usePage, Head, Form, router } from '@inertiajs/vue3';
import { ref, shallowRef, toRef, computed, watch } from 'vue';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import { index, store, update, destroy } from '@/routes/clubs';
import type { Club, ClubFormModel } from '@/types';

const props = defineProps<{
    clubs: any;
    districts: any[];
    filters?: { searchText?: string };
}>();
function createNewClub(): ClubFormModel {
    return {
        id: null,
        club_name: '',
        club_president: '',
        club_president_email: null,
        club_president_mobile: null,
        club_president_sig: null,
        club_other_name: null,
        club_other_sig: null,
        district_code: null,
        district_id: null,
    };
}

const page = usePage();

const clubsPage = computed(() => (page.props as any).clubs); // the paginator
// const clubs = computed<Club[]>(() => (page.props as any).clubs?.data ?? []);

const deleteDialog = ref(false);
const deleteTarget = ref<any>(null);
const deleting = ref(false);
const formModel = ref<ClubFormModel>(createNewClub());
const dialog = shallowRef(false);
const searchText = ref(props.filters?.searchText ?? '');
const isEditing = toRef(() => !!formModel.value.id);

let debounceTimer: ReturnType<typeof setTimeout> | null = null;
const isSearching = ref(false);

const headers = [
    { title: 'District', key: 'district_code', align: 'start' as const },
    { title: 'Club Name', key: 'club_name', align: 'start' as const },
    {
        title: 'President Email',
        key: 'club_president_email',
        align: 'start' as const,
    },
    {
        title: 'President Name',
        key: 'club_president',
        align: 'start' as const,
    },
    {
        title: 'President Mobile',
        key: 'club_president_mobile',
        align: 'start' as const,
    },
    { title: 'Other Name', key: 'club_other_name', align: 'start' as const },
    {
        title: 'Actions',
        key: 'actions',
        align: 'center' as const,
        sortable: false,
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
            perPage: clubsPage.value.per_page,
            searchText: search || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['clubs'],
            onFinish: () => {
                isSearching.value = false;
            },
        },
    );
}

function add() {
    formModel.value = createNewClub();
    dialog.value = true;
}

function edit(club: Club) {
    if (!club) {
        return;
    }

    formModel.value = {
        id: club.id,
        club_name: club.club_name ?? '',
        club_president: club.club_president ?? '',
        club_president_email: club.club_president_email ?? '',
        club_president_mobile: club.club_president_mobile ?? '',
        club_president_sig: club.club_president_sig ?? '',
        club_other_name: club.club_other_name ?? '',
        club_other_sig: club.club_other_sig ?? '',
        district_code: club.district_code ?? null,
        district_id: club.district_id ?? null,
    };

    dialog.value = true;
}

function reset() {
    dialog.value = false;
    formModel.value = createNewClub();
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
            only: ['clubs'],
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
            perPage: clubsPage.value.perPage,
            ...(searchText.value && { searchText: searchText.value }),
        },
        { preserveState: true, preserveScroll: true, only: ['clubs'] },
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
            only: ['clubs'],
        },
    );
}

function onFormSuccess() {
    reset();
}

function confirmRemove(item: Club) {
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
    <Head title="Users" />
    <div class="app-page">
        <v-sheet border rounded>
            <v-data-table-server
                density="compact"
                :headers="headers"
                hover
                class="elevation-1"
                :items="clubs?.data"
                :items-length="clubsPage.total"
                :items-per-page="clubsPage.per_page"
                :page="clubsPage.current_page"
                :loading="isSearching"
                :items-per-page-options="[15, 35, 65, 100]"
                @update:page="onPageChange"
                @update:items-per-page="onPerPageChange"
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
                            Rotary Clubs
                        </v-toolbar-title>

                        <VSpacer></VSpacer>
                        <VTextField
                            v-model="searchText"
                            prepend-inner-icon="mdi-magnify"
                            rounded="lg"
                            placeholder="Search the Rotary Clubs"
                            class="me-2"
                            density="compact"
                        ></VTextField>

                        <v-btn
                            color="primary"
                            class="me-2"
                            prepend-icon="mdi-plus"
                            rounded="lg"
                            text="Add New Club"
                            variant="outlined"
                            @click="add"
                        ></v-btn>
                    </v-toolbar>
                </template>

                <template v-slot:[`item.actions`]="{ item }">
                    <div class="d-flex ga-2 justify-end">
                        <v-icon
                            color="success"
                            icon="mdi-pencil"
                            size="small"
                            @click="edit(item as Club)"
                        ></v-icon>

                        <v-icon
                            color="red"
                            icon="mdi-delete"
                            size="small"
                            @click="confirmRemove(item as Club)"
                        ></v-icon>
                    </div>
                </template>

                <template v-slot:no-data>
                    <div class="d-flex justify-center pa-4">
                        <v-btn
                            prepend-icon="mdi-backup-restore"
                            rounded="lg"
                            text="Reset filters"
                            variant="text"
                            @click="clearSearch"
                        ></v-btn>
                    </div>
                </template>
            </v-data-table-server>
        </v-sheet>

        <v-dialog v-model="dialog" max-width="700">
            <Form
                v-bind="isEditing ? update.form(formModel.id!) : store.form()"
                :reset-on-success="['password']"
                v-slot="{ errors, processing }"
                @success="onFormSuccess"
            >
                <v-card
                    :title="`${isEditing ? 'Update' : 'Create'}`"
                    :subtitle="`${isEditing ? 'Edit the existing' : 'Add a new'} Club `"
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
                            <v-col cols="12" md="12" class="py-1">
                                <VTextField
                                    name="club_name"
                                    v-model="formModel.club_name"
                                    label="Club Name"
                                    density="compact"
                                    variant="outlined"
                                    hide-details="auto"
                                    :error-messages="errors.club_name"
                                ></VTextField>
                            </v-col>
                            <v-col cols="12" md="6" class="py-1">
                                <VTextField
                                    name="club_president"
                                    label="Club President Name"
                                    v-model="formModel.club_president"
                                    density="compact"
                                    variant="outlined"
                                    hide-details="auto"
                                    :error-messages="errors.club_president"
                                ></VTextField>
                            </v-col>
                            <v-col cols="12" md="6" class="py-1">
                                <VTextField
                                    name="club_president_email"
                                    label="Club President Email"
                                    v-model="formModel.club_president_email"
                                    density="compact"
                                    variant="outlined"
                                    hide-details="auto"
                                    :error-messages="
                                        errors.club_president_email
                                    "
                                ></VTextField>
                            </v-col>
                            <v-col cols="12" md="6" class="py-1">
                                <VTextField
                                    name="club_president_mobile"
                                    label="Club President Mobile"
                                    v-model="formModel.club_president_mobile"
                                    density="compact"
                                    variant="outlined"
                                    hide-details="auto"
                                    :error-messages="
                                        errors.club_president_mobile
                                    "
                                ></VTextField>
                            </v-col>
                            <v-col cols="12" md="6" class="py-1">
                                <VTextField
                                    name="club_other_name"
                                    label="Club Other Name"
                                    v-model="formModel.club_other_name"
                                    density="compact"
                                    variant="outlined"
                                    hide-details="auto"
                                    :error-messages="errors.club_other_name"
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
                            Save User
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
