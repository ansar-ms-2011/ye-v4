<script setup lang="ts">
import { usePage, Head, Form, router, Deferred } from '@inertiajs/vue3';
import { ref, shallowRef, toRef, computed, watch } from 'vue';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import { index, store, update, destroy } from '@/routes/users';
import type { User, UserFormModel } from '@/types';

function createNewUser(): UserFormModel {
    return {
        id: null,
        full_name: '',
        email: '',
        district: null,
        rotary_club_id: null,
        role_id: null,
        password: '',
        password_confirmation: '',
        active: true,
    };
}

const page = usePage();

const usersPage = computed(() => (page.props as any).users); // the paginator
const users = computed<User[]>(() => (page.props as any).users?.data ?? []);
const districts = computed(() => (page.props as any).districts ?? []);
const clubs = computed(() => (page.props as any).clubs ?? []);
const roles = computed(() => (page.props as any).roles ?? []);

const deleteDialog = ref(false);
const deleteTarget = ref<any>(null);
const deleting = ref(false);
const formModel = ref<UserFormModel>(createNewUser());
const dialog = shallowRef(false);
const searchText = ref('');
const isEditing = toRef(() => !!formModel.value.id);

let debounceTimer: ReturnType<typeof setTimeout> | null = null;
const isSearching = ref(false);

const headers = [
    { title: 'Full name', key: 'full_name', align: 'start' as const },
    { title: 'Email Address', key: 'email', align: 'start' as const },
    { title: 'Role Name', key: 'role_id', align: 'start' as const },
    { title: 'District', key: 'district', align: 'center' as const },
    { title: 'Club Name', key: 'club', align: 'center' as const },
    { title: 'Active', key: 'active', align: 'center' as const },
    {
        title: 'Actions',
        key: 'actions',
        align: 'center' as const,
        sortable: false,
    },
];

// Initialize searchText from URL query params
if (typeof window !== 'undefined') {
    const urlParams = new URLSearchParams(window.location.search);
    const urlSearch = urlParams.get('searchText');

    if (urlSearch) {
        searchText.value = urlSearch;
    }
}

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
            perPage: usersPage.value.per_page,
            searchText: search || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['users'],
            onFinish: () => {
                isSearching.value = false;
            },
        },
    );
}

function add() {
    formModel.value = createNewUser();
    dialog.value = true;
}

function edit(user: User) {
    if (!user) {
        return;
    }

    formModel.value = {
        id: user.id,
        full_name: user.full_name ?? '',
        email: user.email ?? '',
        district: (user as any).district ?? null,
        rotary_club_id:
            user.rotary_club_id != null ? Number(user.rotary_club_id) : null,
        role_id: user.roles != null ? Number(user.roles?.[0]?.id) : null,
        password: '',
        password_confirmation: '',
        active: user.active,
    };

    dialog.value = true;
}

function reset() {
    dialog.value = false;
    formModel.value = createNewUser();
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
            only: ['users'],
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
            perPage: usersPage.value.perPage,
            searchText: searchText.value,
        },
        { preserveState: true, preserveScroll: true, only: ['users'] },
    );
}

function onPerPageChange(newPerPage: number) {
    router.get(
        index().url,
        {
            page: 1,
            perPage: newPerPage,
            searchText: searchText.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['users'],
        },
    );
}

function onFormSuccess() {
    reset();
}

function confirmRemove(item: User) {
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
        <v-sheet border rounded class="overflow-hidden">
            <Deferred data="users">
                <template #fallback>
                    <div class="d-flex align-center justify-center" style="min-height: 500px">
                        <span>Loading users' data...</span>
                    </div>
                </template>

                <v-data-table-server
                    density="compact"
                    :headers="headers"
                    hover
                    rounded
                    class="elevation-1"
                    :items="users"
                    :items-length="usersPage.total"
                    :items-per-page="usersPage.per_page"
                    :page="usersPage.current_page"
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

                                Users
                            </v-toolbar-title>

                            <VSpacer></VSpacer>
                            <VTextField
                                v-model="searchText"
                                prepend-inner-icon="mdi-magnify"
                                rounded="lg"
                                placeholder="Search"
                                class="me-2"
                                density="compact"
                            ></VTextField>

                            <v-btn
                                color="primary"
                                class="me-2"
                                prepend-icon="mdi-plus"
                                rounded="lg"
                                text="Add New User"
                                variant="outlined"
                                @click="add"
                            ></v-btn>
                        </v-toolbar>
                    </template>

                    <template v-slot:[`item.role_id`]="{ item }">
                        {{ item.roles?.[0]?.name }}
                    </template>

                    <template v-slot:[`item.club`]="{ item }">
                        {{ item.club?.club_name }}
                    </template>

                    <template v-slot:[`item.active`]="{ item }">
                        {{ item.active ? 'Yes' : 'No' }}
                    </template>

                    <template v-slot:[`item.actions`]="{ item }">
                        <div class="d-flex ga-2 justify-end">
                            <v-icon
                                color="success"
                                icon="mdi-pencil"
                                size="small"
                                @click="edit(item)"
                            ></v-icon>

                            <v-icon
                                color="red"
                                icon="mdi-delete"
                                size="small"
                                @click="confirmRemove(item as User)"
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
            </Deferred>
        </v-sheet>

        <v-dialog v-model="dialog" max-width="700">
            <Form
                v-bind="isEditing ? update.form(formModel.id!) : store.form()"
                :reset-on-success="['password']"
                v-slot="{ errors, processing }"
                @success="onFormSuccess"
                :transform="(data) => ({ ...data, active: !!formModel.active })"
            >
                <v-card
                    :subtitle="`${isEditing ? 'Update' : 'Create'} user account`"
                    :title="`${isEditing ? 'Edit' : 'Add'} a User`"
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
                                    name="full_name"
                                    v-model="formModel.full_name"
                                    label="Full Name"
                                    density="compact"
                                    variant="outlined"
                                    hide-details="auto"
                                    :error-messages="errors.full_name"
                                ></VTextField>
                            </v-col>

                            <v-col cols="12" md="6" class="py-1">
                                <VTextField
                                    name="email"
                                    label="Email Address"
                                    v-model="formModel.email"
                                    density="compact"
                                    variant="outlined"
                                    hide-details="auto"
                                    :error-messages="errors.email"
                                ></VTextField>
                            </v-col>

                            <v-col cols="12" md="6" class="py-1">
                                <VCombobox
                                    name="role_id"
                                    itemTitle="title"
                                    itemValue="value"
                                    variant="outlined"
                                    density="compact"
                                    hide-details="auto"
                                    v-model="formModel.role_id"
                                    :items="roles"
                                    label="Role"
                                    :error-messages="errors.role_id"
                                ></VCombobox>
                            </v-col>

                            <v-col cols="12" md="6" class="py-1">
                                <VCombobox
                                    name="district"
                                    itemTitle="title"
                                    itemValue="value"
                                    variant="outlined"
                                    density="compact"
                                    v-model="formModel.district"
                                    :items="districts"
                                    label="District"
                                    hide-details="auto"
                                    :error-messages="errors.district"
                                ></VCombobox>
                            </v-col>

                            <v-col cols="12" md="6" class="py-1">
                                <VCombobox
                                    name="rotary_club_id"
                                    itemTitle="title"
                                    itemValue="value"
                                    v-model="formModel.rotary_club_id"
                                    :items="clubs"
                                    label="Rotary Club ID"
                                    density="compact"
                                    variant="outlined"
                                    hide-details="auto"
                                    :error-messages="errors.rotary_club_id"
                                ></VCombobox>
                            </v-col>
                            <VCol cols="12" md="6" class="py-1">
                                <v-checkbox
                                    label="Active"
                                    v-model="formModel.active"
                                    hide-details="auto"
                                    :error-messages="errors.active"
                                >
                                </v-checkbox>
                                <input
                                    type="hidden"
                                    name="active"
                                    :value="!!formModel.active"
                                />
                            </VCol>
                        </v-row>
                        <VRow density="compact" class="ma-0">
                            <v-col cols="12" md="6" class="py-1">
                                <VTextField
                                    name="password"
                                    label="Password"
                                    density="compact"
                                    variant="outlined"
                                    hide-details="auto"
                                    :error-messages="errors.password"
                                ></VTextField>
                            </v-col>
                            <v-col cols="12" md="6" class="py-1">
                                <VTextField
                                    name="password_confirmation"
                                    label="Password Confirmation"
                                    density="compact"
                                    variant="outlined"
                                    hide-details="auto"
                                    :error-messages="errors.password"
                                ></VTextField>
                            </v-col>
                        </VRow>
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
