<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { ref, reactive, computed, nextTick } from 'vue';
import { getEmptyLanguage, getEmptySibling, validateEmail } from '@/helpers';
import ActionModal from '@/pages/applications/ActionModal.vue';
import { store, update, removeMedia } from '@/routes/application';

const props = defineProps({
    ApplicationObj: { type: Object, required: true },
    mode: { type: String, default: 'edit' },
});

const role = usePage().props.auth?.user?.role?.name;

const formRef = ref(null);
const langFormRef = ref(null);
const siblingFormRef = ref(null);
const uploadFormRef = ref(null);

const valid = ref(false);
const langFormValid = ref(false);
const uploadFormValid = ref(false);
const siblingValid = ref(false);

const saving = ref(false);
const edited_sibling = ref(null);
const edited_sibling_index = ref(null);

const yearsStudiedOptions = Array.from({ length: 25 }, (_, i) => i + 1);
const language_options = ['Poor', 'Fair', 'Good', 'Fluent'];

const defaultLanguage = reactive(getEmptyLanguage());
const defaultSibling = reactive(getEmptySibling());
const alert = reactive({ show: false, type: 'info', message: '' });

const form = reactive(props.ApplicationObj);

const snackbar = reactive({
    show: false,
    message: '',
    color: 'success',
    timeout: 3000,
    location: '',
});
const list_categories = [
    'Photo-Self',
    'Passport-Scan',
    'Photo-Family',
    'Photo-Home',
    'Photo-Interest',
    'Photo-Important',
    'Applicant-Letter-Page-1',
    'Applicant-Letter-Page-2',
    'Applicant-Letter-Page-3',
    'Parent-Letter-Page-1',
    'Parent-Letter-Page-2',
];

function buildMediaLibrary() {
    const initial_list = props.ApplicationObj?.media_library || [];
    const media_library = [];

    const addCategory = (category_label) => {
        const found = initial_list.find(
            (i) => i.media_category_label === category_label,
        );
        media_library.push(
            found ?? {
                id: null,
                media: null,
                application_id: null,
                media_category_label: category_label,
                brief_caption: '',
                file_name: '',
                size: null,
                media_category_id: '',
                changed: false,
                remove: false,
                selected: false,
            },
        );
    };

    list_categories.forEach((label) => {
        if (
            form.exchange_type === 'CAMPS & TOURS' &&
            (label === 'Photo-Self' || label === 'Passport-Scan')
        ) {
            addCategory(label);
        } else if (form.exchange_type !== 'CAMPS & TOURS') {
            addCategory(label);
        }
    });

    return media_library;
}

form.media_library = buildMediaLibrary();

function showSnackbar(msg, color = 'success', location = '') {
    snackbar.message = msg;
    snackbar.color = color;
    snackbar.show = true;
    snackbar.location = location;
}

const loading = reactive({
    paymentEmail: false,
    guideEmail1: false,
    guideEmail2: false,
});

const formRules = {
    nameRules: [
        (v) => !!v || 'Required Field',
        (v) => (v && v.length <= 100) || 'Name must be < 100 characters',
    ],
    emailRules: [(v) => !v || validateEmail(v) || 'Email format is not valid'],
    districtRules: [(v) => !!v || 'Required Field'],
};

const langFormRules = {
    nameRules: [(v) => !!v || 'Required Field'],
    yearsRules: [(v) => !!v || 'Required Field'],
    speakingRules: [(v) => !!v || 'Required Field'],
    readingRules: [(v) => !!v || 'Required Field'],
    writingRules: [(v) => !!v || 'Required Field'],
};

const siblingsFormRules = {
    fullNameRules: [(v) => !!v || 'Required Field'],
    ageRules: [(v) => !!v || 'Required Field'],
    genderRules: [(v) => !!v || 'Required Field'],
    occupationRules: [],
    livingAtHomeRules: [(v) => !!v || 'Required Field'],
};

const uploadFileRules = [
    (value) =>
        !value ||
        value.size < 5120000 ||
        'Upload size should be less than 5 MB!',
    (value) =>
        !value ||
        ['png', 'gif', 'jpg', 'jpeg', 'bmp'].some((ext) =>
            value.name.toLowerCase().endsWith(ext),
        ) ||
        'Only PNG, JPG, JPEG, GIF and BMP files are allowed',
];

const uploadCaptionRules = [
    (v) => !!v || 'Caption Required For File Upload',
    (v) => v === null || v.length < 151 || 'Maximum 150 Characters Are Allowed',
];

/* ---------------- TABLE HEADERS ---------------- */
const languagesTableHeaders = [
    {
        title: 'Non Native Language',
        key: 'language',
        headerProps: {
            class: 'bg-secondary-lighten-3 text-black',
        },
        sortable: false,
    },
    {
        title: 'Years Studied',
        key: 'years_studied',
        headerProps: {
            class: 'bg-secondary-lighten-3 text-black',
        },
        sortable: false,
    },
    {
        title: 'Speaking',
        key: 'speaking',
        headerProps: {
            class: 'bg-secondary-lighten-3 text-black',
        },
        sortable: false,
    },
    {
        title: 'Reading',
        key: 'reading',
        headerProps: {
            class: 'bg-secondary-lighten-3 text-black',
        },
        sortable: false,
    },
    {
        title: 'Writing',
        key: 'writing',
        headerProps: {
            class: 'bg-secondary-lighten-3 text-black',
        },
        sortable: false,
    },
    {
        title: 'Actions',
        key: 'actions',
        sortable: false,
        width: '150px',
        headerProps: {
            class: 'bg-secondary-lighten-3 text-black',
        },
    },
];

const siblingsTableHeaders = [
    {
        title: 'Full Name',
        key: 'full_name',
        headerProps: {
            class: 'bg-secondary-lighten-3 text-black',
        },
        sortable: false,
    },
    {
        title: 'Gender',
        key: 'gender',
        headerProps: {
            class: 'bg-secondary-lighten-3 text-black',
        },
        sortable: false,
    },
    {
        title: 'Age',
        key: 'age',
        headerProps: {
            class: 'bg-secondary-lighten-3 text-black',
        },
        sortable: false,
    },
    {
        title: 'Occupation',
        key: 'occupation',
        headerProps: {
            class: 'bg-secondary-lighten-3 text-black',
        },
        sortable: false,
    },
    {
        title: 'Living At Home',
        key: 'living_at_home',
        headerProps: {
            class: 'bg-secondary-lighten-3 text-black',
        },
        sortable: false,
    },
    {
        title: 'Actions',
        key: 'actions',
        sortable: false,
        width: '150px',
        headerProps: {
            class: 'bg-secondary-lighten-3 text-black',
        },
    },
];

const filteredLanguages = computed(
    () => form.languages?.filter((l) => !l.remove) || [],
);
const siblings = computed(() => form.siblings?.filter((s) => !s.remove) || []);
const filledMediaFiles = computed(
    () => form.media_library?.filter((m) => m.media !== null) || [],
);

function validateMedicalInfo(v) {
    // console.log('validateMedicalInfo', v);
    const answeredYes = !!(
        form.medical_condition ||
        form.treated_condition ||
        form.prescribed_meds ||
        form.special_req
    );

    return answeredYes && !v
        ? 'Medication and the Reason Prescribed is required if any of above question is answered as Yes.'
        : true;
}

function goBack() {
    window.history.back();
}
function viewFiles() {
    router.visit(props.routes.uploadedFiles.replace(':id', form.id));
}
function goToEmailGuide() {
    router.visit(props.routes.emailGuide.replace(':id', form.id));
}

async function submitForm() {
    const { valid: mainFormValidity } = await formRef.value?.validate();
    const { valid: uploadFormValidity } = await uploadFormRef.value?.validate();

    console.log(mainFormValidity, uploadFormValidity);

    await nextTick(() => {
        if (!(mainFormValidity && uploadFormValidity)) {
            showSnackbar(
                'Please fill out all required fields',
                'error',
                'top end',
            );
            scrollToFirstError();

            return;
        }

        saving.value = true;

        const options = {
            preserveScroll: true,
            preserveState: false, // <-- re-mounts component
            onSuccess: (page) => {
                // Re-sync local reactive form from fresh props
                Object.assign(form, page.props.ApplicationObj);
                form.media_library = buildMediaLibrary();
            },
            onFinish: () => {
                saving.value = false;
            },
        };

        if (form.id > 0) {
            router.put(update({ id: form.id }), form, options);
        } else {
            router.post(store(), form, options);
        }
    });
}

function scrollToFirstError() {
    const elements = Array.from(
        document.getElementsByClassName('v-messages__message'),
    ).filter((el) => el.id !== 'sibling-note' && el.id !== 'language-note');

    if (elements.length > 0) {
        elements[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

function sendEmail(type, loadingKey) {
    loading[loadingKey] = true;
    const routeMap = {
        payment: props.routes.sendPaymentEmail,
        'guide-part-1': props.routes.sendGuide1,
        'guide-part-2': props.routes.sendGuide2,
    };
    router.post(
        routeMap[type].replace(':id', form.id),
        {},
        {
            preserveScroll: true,
            onSuccess: (p) =>
                showSnackbar(p.props.flash?.success || 'Email sent'),
            onError: () => showSnackbar('Failed to send email', 'red'),
            onFinish: () => {
                loading[loadingKey] = false;
            },
        },
    );
}

async function addLanguage() {
    if (filteredLanguages.value.length >= 3) {
        showSnackbar('Maximum 03 Languages can be added', 'error');

        return false;
    }

    if (!langFormValid.value) {
        langFormRef.value?.validate();
        showSnackbar('Please complete the language fields', 'error');

        return false;
    }

    form.languages.push({ ...defaultLanguage });
    Object.assign(defaultLanguage, getEmptyLanguage());

    await nextTick();
    langFormRef.value?.resetValidation();
}

function removeLanguage(item, index) {
    form.languages[index].remove = true;
}

async function addSibling() {
    siblingFormRef.value?.validate();

    if (siblingValid.value && form.siblings.length >= 4) {
        showSnackbar('Maximum 04 Siblings can be added', 'red');

        return false;
    }

    form.siblings.push({ ...defaultSibling });
    Object.assign(defaultSibling, getEmptySibling());

    await nextTick();
    siblingFormRef.value?.resetValidation();
}
function editSibling(item, index) {
    Object.assign(defaultSibling, item);
    edited_sibling.value = item;
    edited_sibling_index.value = index;
}
function saveSibling() {
    Object.assign(form.siblings[edited_sibling_index.value], defaultSibling);
    Object.assign(defaultSibling, getEmptySibling());
    siblingFormRef.value?.resetValidation();
    edited_sibling.value = null;
    edited_sibling_index.value = null;
}
function removeSibling(item, index) {
    form.siblings[index].remove = true;
}

function handleFileSelection(event, mediaObj) {
    const file = event.target.files[0];

    if (file) {
        const reader = new FileReader();
        reader.addEventListener(
            'load',
            () => {
                mediaObj.media = reader.result;
                mediaObj.selected = true;
                mediaObj.file_name = file.name;
                mediaObj.size = file.size;
            },
            false,
        );
        reader.readAsDataURL(file);
    } else {
        mediaObj.media = null;
        mediaObj.selected = false;
        mediaObj.file_name = '';
        mediaObj.size = 0;
    }
}

function removedUploadedMedia(media) {
    router.post(
        removeMedia({ id: media.id }),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                media.id = null;
                media.media = null;
                media.file_name = '';
            },
        },
    );
}

function handleActionUpdated(updatedForm) {
    // form.value = updatedForm;
    form.dyeo_id = updatedForm.dyeo_id;
    form.rotary_club_id = updatedForm.rotary_club_id;
    form.exchange_type = updatedForm.exchange_type;
    form.application_status = updatedForm.application_status;
    form.application_status_note = updatedForm.application_status_note;
    form.application_fee_paid = updatedForm.application_fee_paid;
    form.dyeo_assigned = updatedForm.dyeo_assigned || null;
    console.log(updatedForm, form);
    submitForm();
}
</script>

<template>
    <div id="appForm">
        <VCard v-show="alert.show">
            <v-alert dense id="alertBox" dismissible :type="alert.type">
                {{ alert.message }}
            </v-alert>
        </VCard>

        <VSnackbar
            v-model="snackbar.show"
            :timeout="snackbar.timeout"
            :color="snackbar.color"
            elevation="24"
            :location="snackbar.location"
        >
            {{ snackbar.message }}
            <template v-slot:actions>
                <VBtn
                    color="blue"
                    variant="text"
                    @click="snackbar.show = false"
                >
                    Close
                </VBtn>
            </template>
        </VSnackbar>

        <VRow class="mb-4" v-if="['admin', 'applicant'].includes(role)">
            <VCol cols="3" sm="3" md="3" class="d-flex justify-content-start">
                <VBtn
                    variant="outlined"
                    color="secondary"
                    style="float: left"
                    @click="goBack"
                >
                    Back
                </VBtn>
                <VBtn
                    :disabled="filledMediaFiles.length === 0"
                    class="ml-2"
                    color="primary"
                    variant="outlined"
                    @click="viewFiles"
                >
                    View Files
                    {{
                        filledMediaFiles.length > 0
                            ? '(' + filledMediaFiles.length + ')'
                            : ''
                    }}
                </VBtn>
            </VCol>

            <VCol cols="6" sm="6" md="6">
                <template v-if="role !== 'applicant'">
                    <VRow>
                        <VCol
                            cols="12"
                            sm="12"
                            md="12"
                            class="d-flex justify-content-between"
                        >
                            <VBtn
                                color="primary darken-1"
                                class="mr-2"
                                variant="outlined"
                                @click="
                                    sendEmail('guide-part-1', 'guideEmail1')
                                "
                                :loading="loading.guideEmail1"
                            >
                                SEND GUIDE (PART 1)
                            </VBtn>
                            <VBtn
                                color="primary darken-1"
                                class="mr-2"
                                variant="outlined"
                                @click="sendEmail('payment', 'paymentEmail')"
                                :loading="loading.paymentEmail"
                                :disabled="
                                    loading.paymentEmail ||
                                    form.application_status === 'Paid'
                                "
                            >
                                SEND PAYMENT EMAIL
                            </VBtn>
                            <VBtn
                                color="primary darken-1"
                                class="mr-2"
                                variant="outlined"
                                @click="
                                    sendEmail('guide-part-2', 'guideEmail2')
                                "
                                :loading="loading.guideEmail2"
                            >
                                SEND GUIDE (PART 2)
                            </VBtn>
                        </VCol>
                    </VRow>
                </template>
            </VCol>

            <VCol cols="3" sm="3" md="3">
                <div class="d-flex justify-end">
                    <VBtn
                        color="primary darken-5"
                        variant="outlined"
                        @click="goToEmailGuide"
                    >
                        EMAILS SENT HISTORY
                    </VBtn>
                    <ActionModal
                        :ApplicationObj="form"
                        @actionUpdated="handleActionUpdated"
                    />
                </div>
            </VCol>
        </VRow>

        <VForm ref="formRef" v-model="valid" lazy-validation>
            <VCard class="mb-3" elevation="3">
                <VCardTitle class="text-h5 bg-primary-lighten-5 mb-3">
                    <span>Applicant Information</span>
                </VCardTitle>
                <VCardText>
                    <VRow>
                        <VCol cols="12" sm="3" md="3">
                            <VTextField
                                label="DYEO Name"
                                :modelValue="form.dyeo?.dyeo_name"
                                hide-details
                                readonly
                            ></VTextField>
                        </VCol>
                        <VCol cols="12" sm="3" md="3">
                            <VTextField
                                label="District"
                                :modelValue="form.dyeo?.district_code"
                                hide-details
                                readonly
                            ></VTextField>
                        </VCol>
                        <VCol cols="12" sm="3" md="3">
                            <VTextField
                                label="Exchange Type"
                                :modelValue="form.exchange_type"
                                hide-details
                                readonly
                            ></VTextField>
                        </VCol>
                        <VCol cols="12" sm="3" md="3">
                            <VTextField
                                label="Application No"
                                v-model="form.application_no"
                                hide-details
                                :readonly="
                                    mode === 'create' || role === 'applicant'
                                "
                            ></VTextField>
                        </VCol>
                    </VRow>
                    <VRow>
                        <VCol cols="12" sm="3" md="3">
                            <VTextField
                                label="First Name"
                                hide-details
                                v-model="form.firstname"
                            ></VTextField>
                        </VCol>
                        <VCol cols="12" sm="3" md="3">
                            <VTextField
                                label="Surname"
                                hide-details
                                v-model="form.surname"
                            ></VTextField>
                        </VCol>
                        <VCol cols="12" sm="3" md="3">
                            <VTextField
                                label="Preferred Name"
                                hide-details
                                v-model="form.pref_name"
                            ></VTextField>
                        </VCol>
                        <VCol cols="12" sm="3" md="3">
                            <VAutocomplete
                                variant="outlined"
                                density="compact"
                                hide-details
                                v-model="form.gender"
                                :items="['Male', 'Female']"
                                label="Gender"
                            ></VAutocomplete>
                        </VCol>
                    </VRow>
                    <VRow>
                        <VCol cols="12" sm="3" md="3">
                            <VTextField
                                label="Date of Birth"
                                v-model="form.dob"
                            ></VTextField>
                        </VCol>
                        <VCol cols="12" sm="3" md="3">
                            <VTextField
                                label="Country of Citizenship"
                                v-model="form.country_citizenship"
                            ></VTextField>
                        </VCol>
                        <VCol cols="12" sm="3" md="3">
                            <VTextField
                                label="Mobile Phone"
                                v-model="form.alt_contact_no"
                            ></VTextField>
                        </VCol>
                        <VCol cols="12" sm="3" md="3">
                            <VTextField
                                label="Home Phone"
                                v-model="form.contact_no"
                            ></VTextField>
                        </VCol>
                    </VRow>
                    <VRow>
                        <VCol cols="12" sm="6" md="6">
                            <VTextField
                                label="Place of Birth (City, State. Country)"
                                v-model="form.place_of_birth"
                            ></VTextField>
                        </VCol>
                        <VCol cols="12" sm="6" md="6">
                            <VTextField
                                label="Email Address"
                                :rules="formRules.emailRules"
                                v-model="form.email_address"
                            ></VTextField>
                        </VCol>
                    </VRow>
                </VCardText>
            </VCard>

            <!-- ============ ADDRESS INFORMATION ============ -->
            <VCard class="mb-3" elevation="3">
                <VCardTitle class="text-h5 bg-primary-lighten-5 mb-3">
                    Address Information
                </VCardTitle>
                <VCardText>
                    <VLabel class="mb-2">Home Address</VLabel>
                    <VRow class="mb-2">
                        <VCol cols="12" sm="3" md="3">
                            <VTextField
                                label="Street"
                                v-model="form.address_home.street"
                            ></VTextField>
                        </VCol>
                        <VCol cols="12" sm="3" md="3">
                            <VTextField
                                label="Town / City"
                                v-model="form.address_home.city"
                            ></VTextField>
                        </VCol>
                        <VCol cols="12" sm="2" md="2">
                            <VTextField
                                label="County"
                                v-model="form.address_home.county"
                            ></VTextField>
                        </VCol>
                        <VCol cols="12" sm="2" md="2">
                            <VTextField
                                label="Postal Code"
                                v-model="form.address_home.postcode"
                            ></VTextField>
                        </VCol>
                        <VCol cols="12" sm="2" md="2">
                            <VTextField
                                label="Country"
                                v-model="form.address_home.country"
                            ></VTextField>
                        </VCol>
                    </VRow>

                    <VLabel class="mb-2">Postal Address (If different)</VLabel>
                    <VRow>
                        <VCol cols="12" sm="3" md="3">
                            <VTextField
                                label="Street"
                                v-model="form.address_postal.street"
                            ></VTextField>
                        </VCol>
                        <VCol cols="12" sm="3" md="3">
                            <VTextField
                                label="Town / City"
                                v-model="form.address_postal.city"
                            ></VTextField>
                        </VCol>
                        <VCol cols="12" sm="2" md="2">
                            <VTextField
                                label="County"
                                v-model="form.address_postal.county"
                            ></VTextField>
                        </VCol>
                        <VCol cols="12" sm="2" md="2">
                            <VTextField
                                label="Postal Code"
                                v-model="form.address_postal.postcode"
                            ></VTextField>
                        </VCol>
                        <VCol cols="12" sm="2" md="2">
                            <VTextField
                                label="Country"
                                v-model="form.address_postal.country"
                            ></VTextField>
                        </VCol>
                    </VRow>
                </VCardText>
            </VCard>

            <!-- ============ LANGUAGE SECTION ============ -->
            <VCard class="mb-3" elevation="3">
                <VCardTitle class="text-h5 bg-primary-lighten-5 mb-3">
                    Languages
                </VCardTitle>
                <VCardText>
                    <VRow class="mb-4">
                        <VCol class="mt-2" cols="12" sm="5" md="5">
                            <VTextField
                                label="Your Native Language"
                                v-model="form.native_language"
                            ></VTextField>
                        </VCol>
                        <VCol class="mt-5" cols="12" sm="5" md="5">
                            <span>* Please enter your top 3 languages *</span>
                        </VCol>
                    </VRow>

                    <VForm
                        ref="langFormRef"
                        v-model="langFormValid"
                        lazy-validation
                    >
                        <VRow class="mb-0">
                            <VCol cols="12" sm="2" md="2">
                                <VTextField
                                    label="Language"
                                    v-model="defaultLanguage.language"
                                    :rules="langFormRules.nameRules"
                                    required
                                ></VTextField>
                            </VCol>
                            <VCol cols="12" sm="2" md="2">
                                <VAutocomplete
                                    :items="yearsStudiedOptions"
                                    label="Years Studied"
                                    v-model="defaultLanguage.years_studied"
                                    :rules="langFormRules.yearsRules"
                                    variant="outlined"
                                    density="compact"
                                    required
                                ></VAutocomplete>
                            </VCol>
                            <VCol cols="12" sm="2" md="2">
                                <VAutocomplete
                                    :items="language_options"
                                    label="Speaking"
                                    v-model="defaultLanguage.speaking"
                                    :rules="langFormRules.speakingRules"
                                    required
                                    variant="outlined"
                                    density="compact"
                                ></VAutocomplete>
                            </VCol>
                            <VCol cols="12" sm="2" md="2">
                                <VAutocomplete
                                    :items="language_options"
                                    label="Reading"
                                    v-model="defaultLanguage.reading"
                                    :rules="langFormRules.readingRules"
                                    required
                                    density="compact"
                                    variant="outlined"
                                ></VAutocomplete>
                            </VCol>
                            <VCol cols="12" sm="2" md="2">
                                <VAutocomplete
                                    :items="language_options"
                                    label="Writing"
                                    v-model="defaultLanguage.writing"
                                    :rules="langFormRules.writingRules"
                                    required
                                    density="compact"
                                    variant="outlined"
                                ></VAutocomplete>
                            </VCol>
                            <VCol
                                cols="12"
                                sm="2"
                                md="2"
                                class="d-flex justify-end"
                            >
                                <v-btn
                                    variant="outlined"
                                    color="primary"
                                    min-width="70"
                                    @click="addLanguage"
                                >
                                    Add
                                </v-btn>
                            </VCol>
                        </VRow>
                    </VForm>

                    <VRow>
                        <VCol cols="12" sm="12" md="12">
                            <VDataTable
                                hide-default-footer
                                class="elevation-1"
                                :headers="languagesTableHeaders"
                                :items="filteredLanguages"
                                density="compact"
                            >
                                <template
                                    v-slot:[`item.actions`]="{ item, index }"
                                >
                                    <v-icon
                                        style="margin-left: 10px"
                                        color="red"
                                        @click="removeLanguage(item, index)"
                                    >
                                        mdi-delete
                                    </v-icon>
                                </template>
                            </VDataTable>
                            <div
                                v-if="filteredLanguages?.length === 3"
                                class="mt-3 v-messages theme--light error--text"
                                role="alert"
                            >
                                <div class="v-messages__wrapper float-right">
                                    <div
                                        id="language-note"
                                        class="v-messages__message"
                                    >
                                        Maximum 03 Languages can be added
                                    </div>
                                </div>
                            </div>
                        </VCol>
                    </VRow>
                </VCardText>
            </VCard>

            <!-- ============ SIBLINGS (if STEP) ============ -->
            <VCard
                class="mb-3"
                elevation="3"
                v-if="form.exchange_type === 'STEP'"
            >
                <VCardTitle class="text-h5 bg-primary-lighten-5 mb-3">
                    Siblings
                </VCardTitle>
                <VCardText>
                    <VForm ref="siblingFormRef" v-model="siblingValid">
                        <VRow>
                            <VCol cols="12" sm="3" md="3">
                                <VTextField
                                    maxLength="100"
                                    label="Full Name"
                                    v-model="defaultSibling.full_name"
                                    :rules="siblingsFormRules.fullNameRules"
                                    required
                                ></VTextField>
                            </VCol>
                            <VCol cols="12" sm="2" md="2">
                                <VAutocomplete
                                    :items="['Male', 'Female']"
                                    label="Gender"
                                    v-model="defaultSibling.gender"
                                    :rules="siblingsFormRules.genderRules"
                                    required
                                    density="compact"
                                    variant="outlined"
                                ></VAutocomplete>
                            </VCol>
                            <VCol cols="12" sm="2" md="2">
                                <VTextField
                                    label="Age"
                                    v-model.number="defaultSibling.age"
                                    :rules="siblingsFormRules.ageRules"
                                    required
                                ></VTextField>
                            </VCol>
                            <VCol cols="12" sm="2" md="2">
                                <VTextField
                                    maxLength="50"
                                    label="Occupation"
                                    v-model="defaultSibling.occupation"
                                    :rules="siblingsFormRules.occupationRules"
                                    required
                                ></VTextField>
                            </VCol>
                            <VCol cols="12" sm="2" md="2">
                                <VAutocomplete
                                    :items="['Yes', 'No']"
                                    label="Living At Home"
                                    v-model="defaultSibling.living_at_home"
                                    :rules="siblingsFormRules.livingAtHomeRules"
                                    required
                                    density="compact"
                                    variant="outlined"
                                ></VAutocomplete>
                            </VCol>
                            <VCol cols="12" sm="1" md="1" class="d-flex justify-end">
                                <v-btn
                                    v-if="edited_sibling !== null"
                                    variant="outlined"
                                    color="primary"
                                    min-width="70"
                                    @click="saveSibling"
                                >
                                    Save
                                </v-btn>
                                <v-btn
                                    v-else
                                    variant="outlined"
                                    color="primary"
                                    min-width="70"
                                    @click="addSibling"
                                >
                                    Add
                                </v-btn>
                            </VCol>
                        </VRow>
                    </VForm>

                    <VRow>
                        <VCol cols="12" sm="12" md="12">
                            <VDataTable
                                headerProps=""
                                :headers="siblingsTableHeaders"
                                :items="siblings"
                                hide-default-footer
                                density="compact"
                                class="elevation-1"
                            >
                                <template
                                    v-slot:[`item.actions`]="{ item, index }"
                                >
                                    <v-icon
                                        style="margin-left: 10px"
                                        color="green"
                                        @click="editSibling(item, index)"
                                    >
                                        mdi-pencil
                                    </v-icon>
                                    <v-icon
                                        style="margin-left: 10px"
                                        color="red"
                                        @click="removeSibling(item, index)"
                                    >
                                        mdi-delete
                                    </v-icon>
                                </template>
                            </VDataTable>
                            <div
                                v-if="form.siblings.length === 4"
                                class="mt-3 v-messages theme--light error--text"
                                role="alert"
                            >
                                <div class="v-messages__wrapper float-right">
                                    <div
                                        id="sibling-note"
                                        class="v-messages__message"
                                    >
                                        Maximum 04 Siblings can be added
                                    </div>
                                </div>
                            </div>
                        </VCol>
                    </VRow>
                </VCardText>
            </VCard>

            <!-- ============ MEDICAL ============ -->
            <VCard class="mb-3" elevation="3" id="medical-box">
                <VCardTitle class="text-h5 bg-primary-lighten-5 mb-3">
                    Medical Information
                </VCardTitle>
                <VCardText>
                    <VRow density="compact">
                        <VCol cols="12" sm="8" md="8">
                            <p class="ma-1">
                                Do you have any dietary restrictions and/or
                                allergies (e.g. vegetarian, vegan, nut, gluten,
                                lactose, etc.)?
                            </p>
                        </VCol>
                        <VCol cols="12" sm="4" md="4">
                            <v-radio-group
                                inline
                                hide-details
                                v-model="form.dietary_restriction"
                            >
                                <v-radio label="Yes" :value="1"></v-radio>
                                <v-radio label="No" :value="0"></v-radio>
                            </v-radio-group>
                        </VCol>
                    </VRow>

                    <VRow density="compact">
                        <VCol cols="12" sm="8" md="8">
                            <p class="ma-1">
                                Do you have any mental health/medical/dental
                                conditions?
                            </p>
                        </VCol>
                        <VCol cols="12" sm="4" md="4">
                            <v-radio-group
                                inline
                                hide-details
                                v-model="form.medical_condition"
                            >
                                <v-radio label="Yes" :value="1"></v-radio>
                                <v-radio label="No" :value="0"></v-radio>
                            </v-radio-group>
                        </VCol>
                    </VRow>

                    <VRow density="compact">
                        <VCol cols="12" sm="8" md="8">
                            <p class="ma-1">
                                Have you been treated for mental health/medical
                                conditions in the past two years?
                            </p>
                        </VCol>
                        <VCol cols="12" sm="4" md="4">
                            <v-radio-group
                                inline
                                hide-details
                                v-model="form.treated_condition"
                            >
                                <v-radio label="Yes" :value="1"></v-radio>
                                <v-radio label="No" :value="0"></v-radio>
                            </v-radio-group>
                        </VCol>
                    </VRow>

                    <VRow density="compact">
                        <VCol cols="12" sm="8" md="8">
                            <p class="ma-1">
                                Have you taken any prescribed medications in the
                                past six months?
                            </p>
                        </VCol>
                        <VCol cols="12" sm="4" md="4">
                            <v-radio-group
                                inline
                                hide-details
                                v-model="form.prescribed_meds"
                            >
                                <v-radio label="Yes" :value="1"></v-radio>
                                <v-radio label="No" :value="0"></v-radio>
                            </v-radio-group>
                        </VCol>
                    </VRow>

                    <VRow density="compact">
                        <VCol cols="12" sm="8" md="8">
                            <p>
                                Do you have any special health requirements
                                (disabilities, allergies etc.)?
                            </p>
                        </VCol>
                        <VCol cols="12" sm="4" md="4">
                            <v-radio-group
                                inline
                                hide-details
                                v-model="form.special_req"
                            >
                                <v-radio label="Yes" :value="1"></v-radio>
                                <v-radio label="No" :value="0"></v-radio>
                            </v-radio-group>
                        </VCol>
                    </VRow>

                    <VRow density="compact">
                        <VCol cols-="12" md="12" sm="12">
                            <p class="ma-1">
                                If you have answered <b>YES</b> to any of the
                                above please explain fully...
                            </p>
                            <v-textarea
                                counter
                                maxlength="650"
                                dense
                                name="input-7-1"
                                filled
                                rows="2"
                                label="Medication and the Reason Prescribed"
                                auto-grow
                                :rules="[validateMedicalInfo]"
                                v-model="form.medical_info"
                            ></v-textarea>
                        </VCol>
                    </VRow>
                </VCardText>
            </VCard>

            <!-- ============ EMERGENCY CONTACT ============ -->
            <VCard class="mb-3" elevation="3" id="emergency-contact-box">
                <VCardTitle class="text-h5 bg-primary-lighten-5 mb-3">
                    Emergency Contact Details
                </VCardTitle>
                <VCardText>
                    <p class="ma-1">
                        Emergency Contact Details in Home Country, other than
                        your Parents / Guardian
                    </p>
                    <VRow>
                        <VCol cols="12" sm="3" md="3">
                            <VTextField
                                label="Name"
                                v-model="form.em_name"
                            ></VTextField>
                        </VCol>
                        <VCol cols="12" sm="3" md="3">
                            <VTextField
                                label="Email"
                                v-model="form.em_email"
                            ></VTextField>
                        </VCol>
                        <VCol cols="12" sm="2" md="2">
                            <VTextField
                                label="Relationship"
                                v-model="form.em_relationship"
                            ></VTextField>
                        </VCol>
                        <VCol cols="12" sm="2" md="2">
                            <VTextField
                                label="Home Phone No"
                                v-model="form.em_htel"
                            ></VTextField>
                        </VCol>
                        <VCol cols="12" sm="2" md="2">
                            <VTextField
                                label="Mobile Phone No"
                                v-model="form.em_mobile"
                            ></VTextField>
                        </VCol>
                    </VRow>

                    <VRow>
                        <VCol cols="12" sm="3" md="3">
                            <VTextField
                                label="Street"
                                v-model="form.address_emergency.street"
                            ></VTextField>
                        </VCol>
                        <VCol cols="12" sm="3" md="3">
                            <VTextField
                                label="Town / City"
                                v-model="form.address_emergency.city"
                            ></VTextField>
                        </VCol>
                        <VCol cols="12" sm="2" md="2">
                            <VTextField
                                label="County"
                                v-model="form.address_emergency.county"
                            ></VTextField>
                        </VCol>
                        <VCol cols="12" sm="2" md="2">
                            <VTextField
                                label="Postal Code"
                                v-model="form.address_emergency.postcode"
                            ></VTextField>
                        </VCol>
                        <VCol cols="12" sm="2" md="2">
                            <VTextField
                                label="Country"
                                v-model="form.address_emergency.country"
                            ></VTextField>
                        </VCol>
                    </VRow>
                </VCardText>
            </VCard>

            <!-- ============ PERSONAL BACKGROUND ============ -->
            <VCard class="mb-3" elevation="3" id="personal-info-box">
                <VCardTitle class="text-h5 bg-primary-lighten-5 mb-3">
                    Applicant Personal Background
                </VCardTitle>
                <VCardText>
                    <VRow density="compact">
                        <VCol cols="12" sm="4" md="4">
                            <p class="ma-1">Religion Name</p>
                        </VCol>
                        <VCol cols="12" sm="8" md="8">
                            <VTextField
                                style="width: 200px"
                                maxlength="100"
                                v-model="form.religion"
                            ></VTextField>
                        </VCol>
                    </VRow>

                    <VRow density="compact">
                        <VCol cols="12" sm="4" md="4">
                            <p class="ma-1">
                                Do you have any special requirements regarding
                                religious observance? Please detail
                            </p>
                        </VCol>
                        <VCol cols="12" sm="8" md="8">
                            <v-textarea
                                rows="1"
                                counter
                                maxlength="200"
                                auto-grow
                                v-model="form.religion_detail"
                            ></v-textarea>
                        </VCol>
                    </VRow>

                    <VRow density="compact">
                        <VCol cols="12" sm="4" md="4">
                            <p class="ma-1">
                                Do you smoke or use tobacco products?
                            </p>
                        </VCol>
                        <VCol cols="12" sm="8" md="8">
                            <v-radio-group
                                inline
                                hide-details
                                v-model="form.smoke"
                            >
                                <v-radio label="Yes" :value="1"></v-radio>
                                <v-radio label="No" :value="0"></v-radio>
                            </v-radio-group>
                            <VTextField
                                v-if="form.smoke === 1"
                                label="If Yes, Explain Why?"
                                v-model="form.smoke_why"
                            ></VTextField>
                        </VCol>
                    </VRow>

                    <VRow density="compact">
                        <VCol cols="12" sm="4" md="4">
                            <p class="ma-1">Do you drink alcohol?</p>
                        </VCol>
                        <VCol cols="12" sm="8" md="8">
                            <v-radio-group
                                inline
                                hide-details
                                v-model="form.drink"
                            >
                                <v-radio label="Yes" :value="1"></v-radio>
                                <v-radio label="No" :value="0"></v-radio>
                            </v-radio-group>
                            <VTextField
                                v-if="form.drink === 1"
                                label="If Yes, Explain Why?"
                                v-model="form.drink_why"
                            ></VTextField>
                        </VCol>
                    </VRow>

                    <VRow density="compact">
                        <VCol cols="12" sm="4" md="4">
                            <p class="ma-1">
                                Have you ever used illegal drugs?
                            </p>
                        </VCol>
                        <VCol cols="12" sm="8" md="8">
                            <v-radio-group
                                inline
                                hide-details
                                v-model="form.illegal_drugs"
                            >
                                <v-radio label="Yes" :value="1"></v-radio>
                                <v-radio label="No" :value="0"></v-radio>
                            </v-radio-group>
                            <VTextField
                                v-if="form.illegal_drugs === 1"
                                label="If Yes, Explain Why?"
                                v-model="form.illegal_drugs_why"
                            ></VTextField>
                        </VCol>
                    </VRow>

                    <span class="text-">
                        Answering <b>YES</b> to these questions will not
                        automatically eliminate you as a candidate...
                    </span>
                </VCardText>
            </VCard>

            <!-- ============ CAMPS ============ -->
            <VCard
                class="mb-3"
                elevation="3"
                id="personal-info-camp--box"
                v-if="form.exchange_type === 'CAMPS & TOURS'"
            >
                <VCardTitle class="text-h5 bg-primary-lighten-5 mb-3">
                    Personal Background - Camps
                </VCardTitle>
                <VCardText>
                    <VRow>
                        <VCol cols="12" sm="12" md="12">
                            <v-textarea
                                rows="2"
                                filled
                                dense
                                auto-grow
                                counter
                                maxlength="650"
                                label="What are your free time activities?"
                                v-model="form.free_activities"
                            ></v-textarea>
                        </VCol>
                        <VCol cols="12" sm="12" md="12">
                            <v-textarea
                                rows="2"
                                filled
                                dense
                                auto-grow
                                counter
                                maxlength="650"
                                label="What are your school, college or university education attainments and vocation?"
                                v-model="form.attainment_vocation"
                            ></v-textarea>
                        </VCol>
                        <VCol cols="12" sm="12" md="12">
                            <v-textarea
                                rows="2"
                                filled
                                dense
                                auto-grow
                                counter
                                maxlength="650"
                                label="What are your special interests, skills & accomplishments?"
                                v-model="form.special_interests"
                            ></v-textarea>
                        </VCol>
                        <VCol cols="12" sm="12" md="12">
                            <v-textarea
                                rows="2"
                                filled
                                dense
                                auto-grow
                                counter
                                maxlength="650"
                                label="Can you contribute to entertainment (e.g. play a musical instrument)?"
                                v-model="form.contrib_entertainment"
                            ></v-textarea>
                        </VCol>
                        <VCol cols="12" sm="12" md="12">
                            <v-textarea
                                label="What is the reason for your programme participation (e.g. choice of specific youth camp)?"
                                v-model="form.reason_for_camp"
                                rows="2"
                                filled
                                dense
                                auto-grow
                                counter
                                maxlength="650"
                            ></v-textarea>
                        </VCol>
                        <VCol cols="12" sm="12" md="12">
                            <v-textarea
                                label="Other personal remarks"
                                rows="2"
                                filled
                                dense
                                auto-grow
                                counter
                                maxlength="650"
                                v-model="form.personal_remarks"
                            ></v-textarea>
                        </VCol>
                    </VRow>
                </VCardText>
            </VCard>

            <!-- ============ FILE UPLOAD ============ -->
            <VCard class="mb-3" elevation="3" id="personal-info-camp--box">
                <VCardTitle class="text-h5 bg-primary-lighten-5 mb-3">
                    File / Scan Upload Section
                </VCardTitle>
                <VCardText>
                    <VForm ref="uploadFormRef" v-model="uploadFormValid">
                        <VRow
                            v-for="(media, index) in form.media_library"
                            :key="index"
                        >
                            <VCol cols="12" sm="3" md="3">
                                <VTextField
                                    readonly
                                    label="Media Category"
                                    v-model="media.media_category_label"
                                ></VTextField>
                            </VCol>
                            <VCol cols="12" sm="4" md="4">
                                <VTextField
                                    label="Image Caption"
                                    counter
                                    maxlength="150"
                                    v-model="media.brief_caption"
                                    :rules="
                                        media.media === null ||
                                        media.media === ''
                                            ? []
                                            : uploadCaptionRules
                                    "
                                ></VTextField>
                            </VCol>
                            <VCol
                                cols="12"
                                sm="4"
                                md="4"
                                style="text-align: center"
                            >
                                <img
                                    v-if="media.id > 0 && media.media"
                                    :id="'img-' + media.media_category_label"
                                    :src="media.media"
                                    alt="Media File"
                                    width="140"
                                />
                                <v-file-input
                                    v-else
                                    @change="handleFileSelection($event, media)"
                                    accept=".jpg,.jpeg, png,.bmp,.gif,.svg,.webp"
                                    label="Select File To Upload"
                                    :rules="uploadFileRules"
                                    hide-details
                                    density="compact"
                                    variant="outlined"
                                ></v-file-input>
                            </VCol>
                            <VCol cols="12" sm="1" md="1">
                                <v-btn
                                    :disabled="media.id === null"
                                    small
                                    color="red"
                                    class="text-white"
                                    @click="removedUploadedMedia(media)"
                                >
                                    REMOVE
                                </v-btn>
                            </VCol>
                        </VRow>
                    </VForm>
                </VCardText>
            </VCard>
        </VForm>

        <VRow>
            <VCol sm="12" md="12">
                <v-fab
                    app
                    extended
                    border
                    :loading="saving"
                    :disabled="saving"
                    color="primary"
                    location="bottom center"
                    min-width="250"
                    hover-elevation="5"
                    size="large"
                    text="Save Application"
                    prepend-icon="mdi-content-save"
                    @click="submitForm"
                />
            </VCol>
        </VRow>
    </div>
</template>

<style scoped>
#appForm .v-card__title {
    height: 38px !important;
    padding: 5px !important;
}

#appForm .v-input:not(.v-input--radio-group, .v-input--checkbox) {
    padding-top: 0 !important;
    margin-top: 0 !important;
}

#medical-box .v-input--radio-group,
#personal-info-box .v-input--radio-group {
    padding-top: 0 !important;
    margin-top: 0 !important;
}

.v-fab {
    bottom: 2px !important;
}

.pic-box {
    width: 150px;
    margin-left: auto;
    margin-right: auto;
    display: block;
}
</style>
