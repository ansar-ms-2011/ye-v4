<script setup lang="ts">
import {
    getEmptyApplication,
    getEmptyAddress,
} from '@/helpers';
import ApplicationForm from './ApplicationForm.vue';


const application = {
    ...getEmptyApplication,
    address_home: getEmptyAddress('HOME'),
    address_parent1: getEmptyAddress('PARENT1'),
    address_parent2: getEmptyAddress('PARENT2'),
    address_emergency: getEmptyAddress('EMERGENCY'),
    address_postal: getEmptyAddress('POSTAL'),
    languages: [],
    siblings: [],
    mode: 'create',
};
</script>

<template>
    <ApplicationForm :ApplicationObj="application" />
</template>

<style scoped></style>
