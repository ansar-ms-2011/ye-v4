<script setup lang="ts">
import { usePage, Head, Form, router } from '@inertiajs/vue3';
import { onMounted, ref, shallowRef, toRef, computed } from 'vue';
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
        active: 1,
    };
}

const page = usePage();

const usersPage = computed(() => (page.props as any).users); // the paginator
const users = computed<User[]>(() => (page.props as any).users?.data ?? []);
const districts = computed(() => (page.props as any).districts ?? []);
const clubs = computed(() => (page.props as any).clubs ?? []);
const roles = computed(() => (page.props as any).roles ?? []);

const formModel = ref<UserFormModel>(createNewUser());
const dialog = shallowRef(false);
const isEditing = toRef(() => !!formModel.value.id);

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

onMounted(() => {
    //
});

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
        active: user.active ? 1 : 0,
    };

    dialog.value = true;
}

function reset() {
    dialog.value = false;
    formModel.value = createNewUser();
}

function closeDialog() {
    dialog.value = false;
}

function onPageChange(newPage: number) {
    router.get(
        index().url,
        {
            page: newPage,
            per_page: usersPage.value.per_page,
        },
        { preserveState: true, preserveScroll: true, only: ['users'] },
    );
}

function onPerPageChange(newPerPage: number) {
    router.get(
        index().url,
        {
            page: 1,
            per_page: newPerPage,
        },
        { preserveState: true, preserveScroll: true, only: ['users'] },
    );
}

function onFormSuccess() {
    reset();
}

const deleteDialog = shallowRef(false);
const deleteTarget = ref<User | null>(null);
const deleting = shallowRef(false);

function confirmRemove(user: User) {
    deleteTarget.value = user;
    deleteDialog.value = true;
}

function cancelRemove() {
    deleteDialog.value = false;
    deleteTarget.value = null;
}

function remove() {
    if (!deleteTarget.value) {
        return;
    }

    deleting.value = true;

    router.delete(destroy(deleteTarget.value.id).url, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            cancelRemove();
        },
        onFinish: () => {
            deleting.value = false;
        },
    });
}
</script>

<template>
    <Head title="Users" />
    <div class="app-page">
        <v-sheet border rounded>
            <v-data-table-server
                density="compact"
                :headers="headers"
                :items="users"
                :items-length="usersPage.total"
                :items-per-page="usersPage.per_page"
                :page="usersPage.current_page"
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

                        <v-btn
                            class="me-2"
                            prepend-icon="mdi-plus"
                            rounded="lg"
                            text="Add a user"
                            border
                            @click="add"
                        ></v-btn>
                    </v-toolbar>
                </template>

                <template v-slot:item.role_id="{ item }">
                    {{ item.roles?.[0]?.name }}
                </template>

                <template v-slot:item.club="{ item }">
                    {{ item.club?.club_name }}
                </template>

                <template v-slot:item.active="{ item }">
                    {{ item.active ? 'Yes' : 'No' }}
                </template>

                <template v-slot:item.actions="{ item }">
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
                            @click="confirmRemove(item)"
                        ></v-icon>
                    </div>
                </template>

                <template v-slot:no-data>
                    <v-btn
                        prepend-icon="mdi-backup-restore"
                        rounded="lg"
                        text="Reset data"
                        variant="text"
                        border
                        @click="reset"
                    ></v-btn>
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
                                    name="active"
                                    hide-details="auto"
                                    :error-messages="errors.active"
                                >
                                </v-checkbox>
                            </VCol>
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
                            variant="tonal"
                        >
                            Save User
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </Form>
        </v-dialog>
        <v-dialog v-model="deleteDialog" max-width="420" persistent>
            <v-card>
                <v-card-title class="d-flex align-center ga-2">
                    <v-icon color="error" icon="mdi-alert-circle" />
                    Confirm Delete
                </v-card-title>

                <v-card-text>
                    Are you sure you want to delete
                    <strong>{{ deleteTargetName }}</strong
                    >?
                    <br />
                    <span class="text-medium-emphasis">
                        This action cannot be undone.
                    </span>
                </v-card-text>

                <v-divider />

                <v-card-actions class="bg-surface-light">
                    <v-btn
                        text="Cancel"
                        variant="plain"
                        :disabled="deleting"
                        @click="cancelRemove"
                    ></v-btn>

                    <v-spacer></v-spacer>

                    <v-btn
                        color="error"
                        variant="tonal"
                        class="rounded-md"
                        :loading="deleting"
                        @click="remove"
                    >
                        Delete
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<style scoped>
:deep(.v-toolbar__content) {
    height: 56px !important;
}
</style>
