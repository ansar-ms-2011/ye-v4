<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { nextTick, onMounted, ref, watch } from 'vue';

const props = defineProps({
    ApplicationObj: {
        type: Object,
        required: true
    },
});

defineEmits(['close', 'actionUpdated']);

const dyeos = usePage().props.dyeos as any[];
const clubs = usePage().props.clubs as any[];
const role = usePage().props.auth?.user?.role?.name;
const showActionsModal = ref(false);
const selectedClubs = ref<any[]>([]);
const saving = ref(false);

const form = ref({
    dyeo_id: null,
    rotary_club_id: null,
    exchange_type: null,
    application_status: null,
    application_status_note: '',
});

watch(()=>props.ApplicationObj, async () => {
    await selectClubs();
    await nextTick();
    form.value.dyeo_id = props.ApplicationObj.dyeo_id;
    form.value.rotary_club_id = props.ApplicationObj.rotary_club_id;
    form.value.exchange_type = props.ApplicationObj.exchange_type;
    form.value.application_status = props.ApplicationObj.application_status;
});

function formatText(o: { district_code: string; text: string }) {
    return o.district_code + ' - ' + o.text;
}
async function selectClubs() {
    await nextTick();

    const district_code = dyeos?.find(
        (d: { id: any }) => d.id === form.value.dyeo_id,
    )?.district_code;

    selectedClubs.value = district_code ? clubs[district_code] || [] : [];
}

async function submitActions() {
    //
}

onMounted(async () => {
    await selectClubs();
});
</script>

<template>
    <VBtn
        class="ms-2"
        color="secondary"
        icon="mdi-dots-vertical"
        density="comfortable"
        @click="showActionsModal = true"
    />
    <VDialog v-model="showActionsModal" max-width="700px" id="actionDialog">
        <VCard>
            <VCardTitle class="text-h5 bg-primary-lighten-5 mb-3">
                Application Actions
            </VCardTitle>
            <VCardText>
                <VRow v-if="['admin', 'dyeo', 'cyeo'].includes(role)">
                    <VCol cols="12" sm="6" md="6">
                        <VAutocomplete
                            required
                            label="Choose DYEO"
                            v-model="form.dyeo_id"
                            variant="outlined"
                            density="compact"
                            :items="dyeos"
                            itemValue="id"
                            :itemTitle="formatText"
                            :readonly="role === 'dyeo' || role === 'cyeo'"
                            @update:modelValue="selectClubs"
                            hide-details
                        ></VAutocomplete>
                    </VCol>
                    <VCol cols="12" sm="6" md="6">
                        <VAutocomplete
                            variant="outlined"
                            density="compact"
                            required
                            label="Club"
                            v-model="form.rotary_club_id"
                            :items="selectedClubs"
                            itemValue="id"
                            :itemTitle="formatText"
                            :readonly="role === 'cyeo'"
                            hide-details
                        ></VAutocomplete>
                    </VCol>
                    <VCol cols="12" sm="6" md="6">
                        <VAutocomplete
                            variant="outlined"
                            density="compact"
                            v-model="form.exchange_type"
                            :items="['CAMPS & TOURS', 'STEP']"
                            label="Application Type"
                            required
                            hide-details
                        ></VAutocomplete>
                    </VCol>
                    <VCol cols="12" sm="6" md="6">
                        <VAutocomplete
                            variant="outlined"
                            density="compact"
                            v-model="form.application_status"
                            :items="[
                                'Accepted',
                                'Paid',
                                'Rejected',
                                'Withdrawn',
                                'Placed',
                            ]"
                            label="Status"
                            required
                            hide-details
                        ></VAutocomplete>
                    </VCol>
                    <VCol cols="12" sm="12" md="12">
                        <VTextarea
                            max-rows="2"
                            dense
                            label="Status Notes"
                            v-model="form.application_status_note"
                            max-height="75"
                        ></VTextarea>
                    </VCol>
                </VRow>
            </VCardText>
            <VCardActions class="mx-4 mb-4">
                <v-btn
                    color="secondary darken-1"
                    variant="outlined"
                    @click="showActionsModal = false"
                >
                    Close
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn
                    color="primary darken-3"
                    variant="flat"
                    :loading="saving"
                    :disabled="saving"
                    @click="submitActions"
                >
                    Save Application
                </v-btn>
            </VCardActions>
        </VCard>
    </VDialog>
</template>

<style scoped></style>
