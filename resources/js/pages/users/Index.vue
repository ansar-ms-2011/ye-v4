<script setup lang="ts">
import { usePage, Head, Form, router } from '@inertiajs/vue3';
import { onMounted, ref, shallowRef, toRef, computed } from 'vue';
import { index, store, update, destroy } from '@/routes/users';
import type { User } from '@/types';

function createNewUser() {
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

const usersPage = computed(() => page.props.users); // the paginator
const users = computed(() => page.props.users?.data ?? []);
const districts = computed(() => page.props.districts ?? []);
const clubs = computed(() => page.props.clubs ?? []);
const roles = computed(() => page.props.roles ?? []);

const formModel = ref(createNewUser());
const dialog = shallowRef(false);
const isEditing = toRef(() => !!formModel.value.id);

const headers = [
    { title: 'Full name', key: 'full_name', align: 'start' },
    { title: 'Email Address', key: 'email', align: 'start' },
    { title: 'Role Name', key: 'role_id', align: 'start' },
    { title: 'District', key: 'district', align: 'center' },
    { title: 'Club Name', key: 'club', align: 'center' },
    { title: 'Active', key: 'active', align: 'center' },
    { title: 'Actions', key: 'actions', align: 'center', sortable: false },
];

onMounted(() => {
    //
});

function add() {
    formModel.value = createNewUser();
    dialog.value = true;
}

function edit(id: number) {
    const user = users.value.find((user: User) => user.id === id);

    formModel.value = {
        id: user.id,
        full_name: user.full_name,
        email: user.email,
        district: user.district,
        rotary_club_id:
            user.rotary_club_id != null ? Number(user.rotary_club_id) : null,
        role_id: user.roles != null ? Number(user.roles?.[0].id) : null,
        active: user.active ? 1 : 0,
    };

    dialog.value = true;
}

function remove(id: number) {
    const index = users.value.findIndex((user: User) => user.id === id);
    users.value.splice(index, 1);
}

function reset() {
    dialog.value = false;
    formModel.value = createNewUser();
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
                            @click="edit(item.id)"
                        ></v-icon>

                        <v-icon
                            color="red"
                            icon="mdi-delete"
                            size="small"
                            @click="remove(item.id)"
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
                v-bind="isEditing ? update.form(formModel.id) : store.form()"
                :reset-on-success="['password']"
                v-slot="{ errors, processing }"
                @success="onFormSuccess"
            >
                <v-card
                    :subtitle="`${isEditing ? 'Update' : 'Create'} your favorite book`"
                    :title="`${isEditing ? 'Edit' : 'Add'} a User`"
                >
                    <template v-slot:text>
                        <v-row>
                            <v-col cols="12">
                                <VTextField
                                    name="full_name"
                                    v-model="formModel.full_name"
                                    label="Full Name"
                                    density="compact"
                                    variant="outlined"
                                ></VTextField>
                            </v-col>

                            <v-col cols="12" md="6">
                                <VTextField
                                    name="email"
                                    label="Email Address"
                                    v-model="formModel.email"
                                    density="compact"
                                    variant="outlined"
                                ></VTextField>
                            </v-col>

                            <v-col cols="12" md="6">
                                <VCombobox
                                    name="role_id"
                                    itemTitle="title"
                                    itemValue="value"
                                    variant="outlined"
                                    density="compact"
                                    v-model="formModel.role_id"
                                    :items="roles"
                                    label="Role"
                                ></VCombobox>
                            </v-col>

                            <v-col cols="12" md="6">
                                <VCombobox
                                    name="district"
                                    itemTitle="title"
                                    itemValue="value"
                                    variant="outlined"
                                    density="compact"
                                    v-model="formModel.district"
                                    :items="districts"
                                    label="District"
                                ></VCombobox>
                            </v-col>

                            <v-col cols="12" md="6">
                                <VCombobox
                                    name="rotary_club_id"
                                    itemTitle="title"
                                    itemValue="value"
                                    v-model="formModel.rotary_club_id"
                                    :items="clubs"
                                    label="Rotary Club ID"
                                    density="compact"
                                    variant="outlined"
                                ></VCombobox>
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
    </div>
</template>

<style scoped>
:deep(.v-toolbar__content) {
    height: 56px !important;
}
</style>
