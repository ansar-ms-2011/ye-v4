<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import type { ComputedRef } from 'vue';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { edit as editAppearance } from '@/routes/appearance';
import { edit as editProfile } from '@/routes/profile';
import { edit as editSecurity } from '@/routes/security';
import type { NavItem } from '@/types';

const page = usePage() as any;
console.log(page);
const sidebarNavItems: ComputedRef<NavItem[]> = computed<NavItem[]>(() => {
    if (page.props.auth?.user?.role?.name === 'applicant') {
        return [
            {
                title: 'Appearance',
                href: editAppearance(),
            },
        ];
    } else {
        return [
            {
                title: 'Profile',
                href: editProfile(),
            },
            {
                title: 'Security',
                href: editSecurity(),
            },
            {
                title: 'Appearance',
                href: editAppearance(),
            },
        ];
    }
});

const { isCurrentOrParentUrl } = useCurrentUrl();
</script>

<template>
    <div class="app-page settings-layout">
        <Heading
            variant="small"
            title="Settings"
            description="Manage your profile and account settings"
        />
        <hr style="border: 1px solid #f4f0f0">
        <div class="settings-grid">
            <nav aria-label="Settings">
                <VList class="settings-nav" nav density="compact">
                    <Link
                        v-for="item in sidebarNavItems"
                        :key="item.title"
                        :href="item.href"
                    >
                        <VListItem
                            :active="isCurrentOrParentUrl(item.href)"
                            :class="{
                                'settings-nav-item--active':
                                    isCurrentOrParentUrl(item.href),
                            }"
                            :title="item.title"
                            rounded="sm"
                        />
                    </Link>
                </VList>
            </nav>

            <section class="settings-panel">
                <slot />
            </section>
        </div>
    </div>
</template>
