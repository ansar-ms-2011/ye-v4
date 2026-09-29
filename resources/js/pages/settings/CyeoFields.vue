<template>
    <VRow density="compact" class="ma-0">
        <VCol class="py-1" cols="12" md="6">
            <label for="cyeo_name">Full Name</label>
            <VTextField
                id="cyeo_name"
                v-model="cyeo.cyeo_name"
                name="cyeo_name"
                density="compact"
                variant="outlined"
                hide-details="auto"
                required
                autocomplete="cyeo_name"
                :error-messages="errors.cyeo_name"
            />
        </VCol>
        <VCol class="py-1" cols="12" md="6">
            <label for="cyeo_email">Email address</label>
            <VTextField
                id="cyeo_email"
                v-model="cyeo.cyeo_email"
                type="email"
                name="cyeo_email"
                density="compact"
                variant="outlined"
                hide-details="auto"
                required
                autocomplete="cyeo_email"
                :error-messages="errors.cyeo_email"
            />
        </VCol>
        <VCol class="py-1" cols="12" md="6">
            <label for="cyeo_address">Address</label>
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
            <label for="cyeo_country">Country</label>
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
            <label for="cyeo_city">City</label>
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
            <label for="cyeo_state">State</label>
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
            <label for="cyeo_postcode">Post Code</label>
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
            <label for="cyeo_mobile">Mobile Number</label>
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
            <label for="cyeo_htel">Home Telephone</label>
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
            <label for="cyeo_wtel">Work Telephone</label>
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
            <VAutocomplete
                name="ribi_club_id"
                itemTitle="name"
                itemValue="id"
                variant="outlined"
                density="compact"
                hide-details="auto"
                v-model="cyeo.ribi_club_id"
                :items="clubs"
                disabled
                :menu-props="{ maxHeight: 300 }"
                :error-messages="errors.ribi_club_id"
            />
        </VCol>
    </VRow>
</template>

<script setup lang="ts">
import { getNewCyeo } from '@/helpers';
import type { Cyeo } from '@/types/misc';

const cyeo = defineModel<Cyeo>({
    required: true,
    default: () => getNewCyeo(),
});

defineProps({
    errors: {
        type: Object,
        default: () => ({}),
    },
    clubs: {
        type: Array,
        default: () => [],
    },
});
</script>
