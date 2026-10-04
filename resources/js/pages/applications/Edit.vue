<script setup lang="ts">
import { getEmptyAddress } from '@/helpers';
import ApplicationForm from '@/pages/applications/ApplicationForm.vue';
const props = defineProps({
    ApplicationObj: { type: Object, required: true },
    mode: { type: String, default: 'edit' },
});

const application = {
    ...props.ApplicationObj,
    address_home: props.ApplicationObj.address_home || getEmptyAddress('HOME'),
    address_parent1:
        props.ApplicationObj.address_parent1 || getEmptyAddress('PARENT1'),
    address_parent2:
        props.ApplicationObj.address_parent2 || getEmptyAddress('PARENT2'),
    address_emergency:
        props.ApplicationObj.address_emergency || getEmptyAddress('EMERGENCY'),
    address_postal:
        props.ApplicationObj.address_postal || getEmptyAddress('POSTAL'),
    languages: props.ApplicationObj.languages || [],
    siblings: props.ApplicationObj.siblings || [],
    mode: props.mode,
    image_changed: false,
};
</script>

<template>
    <ApplicationForm :ApplicationObj="application" />
</template>

<style scoped></style>
