<template>
    <VRow density="compact" class="ma-0">
        <VCol class="py-1" cols="12" md="6">
            <label for="dyeo_name">Full Name</label>
            <VTextField
                id="dyeo_name"
                v-model="dyeo.dyeo_name"
                name="dyeo_name"
                density="compact"
                variant="outlined"
                hide-details="auto"
                required
                autocomplete="dyeo_name"
                :error-messages="errors.dyeo_name"
            />
        </VCol>
        <VCol class="py-1" cols="12" md="6">
            <label for="dyeo_email">Email address</label>
            <VTextField
                id="dyeo_email"
                v-model="dyeo.dyeo_email"
                type="email"
                name="dyeo_email"
                density="compact"
                variant="outlined"
                hide-details="auto"
                required
                autocomplete="dyeo_email"
                :error-messages="errors.dyeo_email"
            />
        </VCol>
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
                id="dyeo_mobile"
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
            <VAutocomplete
                name="district_code"
                itemTitle="code"
                itemValue="code"
                variant="outlined"
                density="compact"
                hide-details="auto"
                v-model="dyeo.district_code"
                :items="districts"
                :error-messages="errors.district_code"
                disabled
                :menu-props="{ maxHeight: 300 }"
            />
        </VCol>
    </VRow>
</template>

<script setup lang="ts">
import { getNewDyeo } from '@/helpers';
import type { Dyeo } from '@/types/misc';

const dyeo = defineModel<Dyeo>({
    required: true,
    default: () => getNewDyeo(),
});

defineProps({
    errors: {
        type: Object,
        default: () => ({}),
    },
    districts: {
        type: Array,
        default: () => [],
    },
});
</script>
