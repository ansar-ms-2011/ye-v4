<script setup lang="ts">
import { usePage, Head, router } from '@inertiajs/vue3';
import { onMounted, ref, shallowRef, toRef, computed } from 'vue';
import type { User } from '@/types';
import { index, store, update, destroy } from '@/routes/users';

function createNewUser() {
    return {
        id: null,
        full_name: '',
        email: '',
        district: null,
        rotary_club_id: null,
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

const formModel = ref(createNewUser());
const dialog = shallowRef(false);
const isEditing = toRef(() => !!formModel.value.id);

const headers = [
    { title: 'Full name', key: 'full_name', align: 'start' },
    { title: 'Email Address', key: 'email', align: 'start' },
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
        district: user.district, // value like "1230"
        rotary_club_id: user.rotary_club_id ?? null, // value like 5
        active: user.active ? 1 : 0,
    };

    dialog.value = true;
}

function remove(id: number) {
    const index = users.value.findIndex((user: User) => user.id === id);
    users.value.splice(index, 1);
}

function save() {
    if (isEditing.value) {
        const index = users.value.findIndex(
            (user) => user.id === formModel.value.id,
        );
        users.value[index] = formModel.value;
    } else {
        formModel.value.id = users.value.length + 1;
        users.value.push(formModel.value);
    }

    dialog.value = false;
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
                :items-per-page-options="[10, 25, 50, 100]"
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
            <v-card
                :subtitle="`${isEditing ? 'Update' : 'Create'} your favorite book`"
                :title="`${isEditing ? 'Edit' : 'Add'} a User`"
            >
                <template v-slot:text>
                    <v-row>
                        <v-col cols="12">
                            <VTextField
                                v-model="formModel.full_name"
                                label="Full Name"
                                density="compact"
                                variant="outlined"
                            ></VTextField>
                        </v-col>

                        <v-col cols="12" md="6">
                            <VTextField
                                label="Email Address"
                                v-model="formModel.email"
                                density="compact"
                                variant="outlined"
                            ></VTextField>
                        </v-col>

                        <v-col cols="12" md="6">
                            <VAutocomplete
                                itemTitle="title"
                                itemValue="value"
                                variant="outlined"
                                density="compact"
                                v-model="formModel.district"
                                :items="districts"
                                label="District"
                            ></VAutocomplete>
                        </v-col>

                        <v-col cols="12" md="6">
                            <VAutocomplete
                                itemTitle="title"
                                itemValue="value"
                                v-model="formModel.rotary_club_id"
                                :items="clubs"
                                label="Rotary Club ID"
                                density="compact"
                                variant="outlined"
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

                    <v-btn text="Save" @click="save"></v-btn>
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
