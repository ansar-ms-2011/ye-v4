<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import WelcomeLogo from '@/assets/images/welcome-logo.png';
import { dashboard, login as officialLogin } from '@/routes';
import { index as applicantLogin } from '@/routes/applicant';

</script>

<template>
    <Head title="Welcome" />

    <VApp>
        <VMain>
            <VContainer class="py-6">
                <div class="d-flex justify-space-between align-center mb-10">
                    <div></div>
                    <div class="d-flex ga-2">
                        <Link v-if="$page.props.auth.user" :href="dashboard()">
                            <VBtn color="primary">Dashboard</VBtn>
                        </Link>
                        <template v-else>
                            <Link :href="officialLogin()">
                                <VBtn variant="outlined" color="primary"
                                    >Officials Log in</VBtn
                                >
                            </Link>
                            <Link :href="applicantLogin()">
                                <VBtn variant="outlined" color="primary"
                                    >Applicant Login</VBtn
                                >
                            </Link>
                        </template>
                    </div>
                </div>

                <VRow align="center" class="min-screen">
                    <VCol cols="12" md="6" offset-md="3">
                        <v-card
                            variant="elevated"
                            class="mx-auto pa-8"
                            color="surface-variant"
                            max-width="500"
                        >
                            <img
                                :src="WelcomeLogo"
                                alt="Youth Exchange Logo"
                                class="welcome-logo"
                            />
                        </v-card>
                    </VCol>
                </VRow>
            </VContainer>
        </VMain>
    </VApp>
</template>

<style scoped>
.min-screen {
    min-height: calc(100vh - 180px);
}
.welcome-logo {
    width: 100%;
    height: auto;
}
</style>
