<script setup lang="ts">
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
//import DeleteUser from '@/components/DeleteUser.vue';
import Heading from '@/components/Heading.vue';
import { getNewCyeo, getNewDyeo } from '@/helpers';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';
import type { Cyeo, Dyeo } from '@/types/misc';

type Props = {
    mustVerifyEmail: boolean;
    status?: string;
};

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Profile settings',
                href: edit(),
            },
        ],
    },
});

const page = usePage();
const role = computed(() => page.props.auth.user.role);
const user = computed(() => page.props.auth.user);
const cyeo = ref<Cyeo>((page.props.cyeo as Cyeo) ?? getNewCyeo());
const dyeo = ref<Dyeo>((page.props.dyeo as Dyeo) ?? getNewDyeo());
const clubs = computed(() => page.props.clubs as any[]);
const districts = computed(() => page.props.districts as any[]);
const full_name = ref(user.value.full_name);
const email = ref(user.value.email);

</script>

<template>
    <Head title="Profile settings" />

    <section class="settings-section">
        <Heading
            variant="small"
            title="Profile information"
            description="Update your name and email address"
        />

        <Form
            v-bind="ProfileController.update.form()"
            v-slot="{ errors, processing }"
        >
            <VRow density="compact" class="ma-0">
                <VCol class="py-1" cols="12" md="6">
                    <label for="name">Full Name</label>
                    <VTextField
                        id="full_name"
                        v-model="full_name"
                        name="full_name"
                        density="compact"
                        variant="outlined"
                        hide-details="auto"
                        required
                        autocomplete="full_name"
                        :error-messages="errors.full_name"
                    />
                </VCol>
                <VCol class="py-1" cols="12" md="6">
                    <label for="email">Email address</label>
                    <VTextField
                        id="email"
                        v-model="email"
                        type="email"
                        name="email"
                        density="compact"
                        variant="outlined"
                        hide-details="auto"
                        required
                        autocomplete="username"
                        :error-messages="errors.email"
                    />
                </VCol>
                <template v-if="role.name === 'cyeo' && cyeo">
                    <VCol class="py-1" cols="12" md="6">
                        <label for="email">address</label>
                        <VTextField
                            id="cyeo_address"
                            v-model="cyeo.cyeo_address"
                            type="text"
                            name="cyeo_address"
                            density="compact"
                            variant="outlined"
                            hide-details="auto"
                            autocomplete="cyeo_address"
                            :error-messages="errors.cyeo_address"
                        />
                    </VCol>
                    <VCol class="py-1" cols="12" md="6">
                        <label for="email">Country</label>
                        <VTextField
                            id="cyeo_country"
                            v-model="cyeo.cyeo_country"
                            type="text"
                            name="cyeo_country"
                            density="compact"
                            variant="outlined"
                            hide-details="auto"
                            autocomplete="cyeo_country"
                            :error-messages="errors.cyeo_country"
                        />
                    </VCol>

                    <VCol class="py-1" cols="12" md="6">
                        <label for="email">City</label>
                        <VTextField
                            id="cyeo_city"
                            v-model="cyeo.cyeo_city"
                            type="text"
                            name="cyeo_city"
                            density="compact"
                            variant="outlined"
                            hide-details="auto"
                            autocomplete="cyeo_city"
                            :error-messages="errors.cyeo_city"
                        />
                    </VCol>
                    <VCol class="py-1" cols="12" md="6">
                        <label for="email">State</label>
                        <VTextField
                            id="cyeo_state"
                            v-model="cyeo.cyeo_state"
                            type="text"
                            name="cyeo_state"
                            density="compact"
                            variant="outlined"
                            hide-details="auto"
                            autocomplete="cyeo_state"
                            :error-messages="errors.cyeo_state"
                        />
                    </VCol>

                    <VCol class="py-1" cols="12" md="6">
                        <label for="email">Post Code</label>
                        <VTextField
                            id="cyeo_postcode"
                            v-model="cyeo.cyeo_postcode"
                            type="text"
                            name="cyeo_postcode"
                            density="compact"
                            variant="outlined"
                            hide-details="auto"
                            autocomplete="cyeo_postcode"
                            :error-messages="errors.cyeo_postcode"
                        />
                    </VCol>
                    <VCol class="py-1" cols="12" md="6">
                        <label for="email">Mobile Number</label>
                        <VTextField
                            id="cyeo_mobile"
                            v-model="cyeo.cyeo_mobile"
                            type="text"
                            name="cyeo_mobile"
                            density="compact"
                            variant="outlined"
                            hide-details="auto"
                            autocomplete="cyeo_mobile"
                            :error-messages="errors.cyeo_mobile"
                        />
                    </VCol>

                    <VCol class="py-1" cols="12" md="6">
                        <label for="email">Home Telephone</label>
                        <VTextField
                            id="cyeo_htel"
                            v-model="cyeo.cyeo_htel"
                            type="text"
                            name="cyeo_htel"
                            density="compact"
                            variant="outlined"
                            hide-details="auto"
                            autocomplete="cyeo_htel"
                            :error-messages="errors.cyeo_htel"
                        />
                    </VCol>
                    <VCol class="py-1" cols="12" md="6">
                        <label for="email">Work Telephone</label>
                        <VTextField
                            id="cyeo_wtel"
                            v-model="cyeo.cyeo_wtel"
                            type="text"
                            name="cyeo_wtel"
                            density="compact"
                            variant="outlined"
                            hide-details="auto"
                            autocomplete="cyeo_wtel"
                            :error-messages="errors.cyeo_wtel"
                        />
                    </VCol>
                    <VCol class="py-1" cols="12" md="6">
                        <label for="ribi_club_id">Club Name</label>
                        <VCombobox
                            name="ribi_club_id"
                            itemTitle="name"
                            itemValue="id"
                            variant="outlined"
                            density="compact"
                            hide-details="auto"
                            v-model="cyeo.ribi_club_id"
                            :items="clubs"
                            :error-messages="errors.ribi_club_id"
                        ></VCombobox>
                    </VCol>
                </template>
                <template v-if="role.name === 'dyeo' && dyeo">
                    <VCol class="py-1" cols="12" md="6">
                        <label for="dyeo_address">DEYO Address</label>
                        <VTextField
                            id="dyeo_address"
                            v-model="dyeo.dyeo_address"
                            type="text"
                            name="dyeo_address"
                            density="compact"
                            variant="outlined"
                            hide-details="auto"
                            autocomplete="dyeo_address"
                            :error-messages="errors.dyeo_address"
                        />
                    </VCol>
                    <VCol class="py-1" cols="12" md="6">
                        <label for="dyeo_country">Country</label>
                        <VTextField
                            id="dyeo_country"
                            v-model="dyeo.dyeo_country"
                            type="text"
                            name="dyeo_country"
                            density="compact"
                            variant="outlined"
                            hide-details="auto"
                            autocomplete="dyeo_country"
                            :error-messages="errors.dyeo_country"
                        />
                    </VCol>

                    <VCol class="py-1" cols="12" md="6">
                        <label for="dyeo_city">City</label>
                        <VTextField
                            id="dyeo_city"
                            v-model="dyeo.dyeo_city"
                            type="text"
                            name="dyeo_city"
                            density="compact"
                            variant="outlined"
                            hide-details="auto"
                            autocomplete="dyeo_city"
                            :error-messages="errors.dyeo_city"
                        />
                    </VCol>
                    <VCol class="py-1" cols="12" md="6">
                        <label for="dyeo_state">State</label>
                        <VTextField
                            id="dyeo_state"
                            v-model="dyeo.dyeo_state"
                            type="text"
                            name="dyeo_state"
                            density="compact"
                            variant="outlined"
                            hide-details="auto"
                            autocomplete="dyeo_state"
                            :error-messages="errors.dyeo_state"
                        />
                    </VCol>

                    <VCol class="py-1" cols="12" md="6">
                        <label for="dyeo_postcode">Post Code</label>
                        <VTextField
                            id="dyeo_postcode"
                            v-model="dyeo.dyeo_postcode"
                            type="text"
                            name="dyeo_postcode"
                            density="compact"
                            variant="outlined"
                            hide-details="auto"
                            autocomplete="dyeo_postcode"
                            :error-messages="errors.dyeo_postcode"
                        />
                    </VCol>
                    <VCol class="py-1" cols="12" md="6">
                        <label for="dyeo_mobile">Mobile Number</label>
                        <VTextField
                            id="cyeo_mobile"
                            v-model="dyeo.dyeo_mobile"
                            type="text"
                            name="dyeo_mobile"
                            density="compact"
                            variant="outlined"
                            hide-details="auto"
                            autocomplete="dyeo_mobile"
                            :error-messages="errors.dyeo_mobile"
                        />
                    </VCol>

                    <VCol class="py-1" cols="12" md="6">
                        <label for="dyeo_htel">Home Telephone</label>
                        <VTextField
                            id="dyeo_htel"
                            v-model="dyeo.dyeo_htel"
                            type="text"
                            name="dyeo_htel"
                            density="compact"
                            variant="outlined"
                            hide-details="auto"
                            autocomplete="dyeo_htel"
                            :error-messages="errors.dyeo_htel"
                        />
                    </VCol>
                    <VCol class="py-1" cols="12" md="6">
                        <label for="dyeo_wtel">Work Telephone</label>
                        <VTextField
                            id="dyeo_wtel"
                            v-model="dyeo.dyeo_wtel"
                            type="text"
                            name="dyeo_wtel"
                            density="compact"
                            variant="outlined"
                            hide-details="auto"
                            autocomplete="dyeo_wtel"
                            :error-messages="errors.dyeo_wtel"
                        />
                    </VCol>
                    <VCol class="py-1" cols="12" md="6">
                        <label for="district">District Code</label>
                        <VCombobox
                            name="district_code"
                            itemTitle="code"
                            itemValue="id"
                            variant="outlined"
                            density="compact"
                            hide-details="auto"
                            v-model="dyeo.district_code"
                            :items="districts"
                            :error-messages="errors.district_code"
                        ></VCombobox>
                    </VCol>
                </template>
            </VRow>

            <VAlert
                v-if="mustVerifyEmail && !user.email_verified_at"
                type="warning"
                variant="tonal"
                class="mb-4"
            >
                Your email address is unverified.
                <Link :href="send()" as="button" class="text-primary">
                    Click here to resend the verification email.
                </Link>

                <div v-if="status === 'verification-link-sent'" class="mt-2">
                    A new verification link has been sent to your email address.
                </div>
            </VAlert>

            <VBtn
                class="w-25 mt-4"
                color="primary"
                type="submit"
                :loading="processing"
                :disabled="processing"
                data-test="update-profile-button"
            >
                Save
            </VBtn>
        </Form>
    </section>

    <!--    <DeleteUser />-->
</template>
