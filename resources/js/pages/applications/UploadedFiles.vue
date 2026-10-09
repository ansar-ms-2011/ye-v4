<template>
    <div id="appForm">
        <v-row class="mb-3">
            <v-col cols="6" sm="6" md="6">
                <v-btn
                    color="secondary"
                    class="float-left"
                    prepend-icon="mdi-arrow-left"
                    @click="goBack"
                >
                    Back
                </v-btn>
            </v-col>
        </v-row>

        <v-card
            v-for="(file, index) in files"
            :key="index"
            hover
            raised
            class="mb-4"
        >
            <v-card-title class="text-h6 bg-primary-lighten-5">
                <span class="d-block w-25">{{
                    file.media_category_label
                }}</span>
            </v-card-title>

            <v-card-text class="d-flex justify-center">
                <div
                    class="d-flex align-center flex-column my-4"
                    style="width: 100%"
                >
                    <img
                        style="z-index: 999; width: 100%; cursor: zoom-in"
                        v-viewer
                        :src="file.media"
                        :alt="`Media File ${file.media_category_label}`"
                    />
                    <span class="mt-3 mb-3">
                        <b>Caption : </b>{{ file.brief_caption }}
                    </span>
                </div>
            </v-card-text>
        </v-card>
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { edit as editApplication } from '@/routes/application';

interface MediaFile {
    media: string;
    media_category_label: string;
    brief_caption: string;
}

const props = defineProps<{
    files: MediaFile[];
    applicationId: number | string;
}>();

function goBack() {
    router.visit(editApplication(props.applicationId as number));
}
</script>
