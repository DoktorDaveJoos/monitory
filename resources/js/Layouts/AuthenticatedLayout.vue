<script setup lang="ts">
import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { cn } from '@/utils';
import { TooltipProvider } from 'radix-vue';
import {
    ResizableHandle,
    ResizablePanel,
    ResizablePanelGroup,
} from '@/Components/ui/resizable';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import NavLink from '@/Components/ui/navlink/NavLink.vue';
import { Home, LogOut, Monitor, MonitorOff, Settings } from 'lucide-vue-next';
import { Label } from '@/Components/ui/label';
import { MonitorLink } from '@/Components/ui/monitorlink';
import Toaster from '@/Components/ui/toast/Toaster.vue';

const collapsed = ref(false);

const hasMonitors = computed(() => {
    return usePage().props.monitor_list.data?.length > 0;
});
</script>

<template>
    <!--    <div>-->
    <!--        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">-->
    <!--            <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">-->
    <!--                &lt;!&ndash; Primary Navigation Menu &ndash;&gt;-->
    <!--                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">-->
    <!--                    <div class="flex justify-between h-16">-->
    <!--                        <div class="flex">-->
    <!--                            &lt;!&ndash; Logo &ndash;&gt;-->
    <!--                            <div class="shrink-0 flex items-center">-->
    <!--                                <Link :href="route('dashboard')">-->
    <!--                                    <ApplicationLogo-->
    <!--                                        class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200"-->
    <!--                                    />-->
    <!--                                </Link>-->
    <!--                            </div>-->

    <!--                            &lt;!&ndash; Navigation Links &ndash;&gt;-->
    <!--                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">-->
    <!--                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">-->
    <!--                                    Dashboard-->
    <!--                                </NavLink>-->
    <!--                            </div>-->
    <!--                        </div>-->

    <!--                        <div class="hidden sm:flex sm:items-center sm:ms-6">-->
    <!--                            &lt;!&ndash; Settings Dropdown &ndash;&gt;-->
    <!--                            <div class="ms-3 relative">-->
    <!--                                <Dropdown align="right" width="48">-->
    <!--                                    <template #trigger>-->
    <!--                                        <span class="inline-flex rounded-md">-->
    <!--                                            <button-->
    <!--                                                type="button"-->
    <!--                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"-->
    <!--                                            >-->
    <!--                                                {{ $page.props.auth.user.name }}-->

    <!--                                                <svg-->
    <!--                                                    class="ms-2 -me-0.5 h-4 w-4"-->
    <!--                                                    xmlns="http://www.w3.org/2000/svg"-->
    <!--                                                    viewBox="0 0 20 20"-->
    <!--                                                    fill="currentColor"-->
    <!--                                                >-->
    <!--                                                    <path-->
    <!--                                                        fill-rule="evenodd"-->
    <!--                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"-->
    <!--                                                        clip-rule="evenodd"-->
    <!--                                                    />-->
    <!--                                                </svg>-->
    <!--                                            </button>-->
    <!--                                        </span>-->
    <!--                                    </template>-->

    <!--                                    <template #content>-->
    <!--                                        <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>-->
    <!--                                        <DropdownLink :href="route('logout')" method="post" as="button">-->
    <!--                                            Log Out-->
    <!--                                        </DropdownLink>-->
    <!--                                    </template>-->
    <!--                                </Dropdown>-->
    <!--                            </div>-->
    <!--                        </div>-->

    <!--                        &lt;!&ndash; Hamburger &ndash;&gt;-->
    <!--                        <div class="-me-2 flex items-center sm:hidden">-->
    <!--                            <button-->
    <!--                                @click="showingNavigationDropdown = !showingNavigationDropdown"-->
    <!--                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"-->
    <!--                            >-->
    <!--                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">-->
    <!--                                    <path-->
    <!--                                        :class="{-->
    <!--                                            hidden: showingNavigationDropdown,-->
    <!--                                            'inline-flex': !showingNavigationDropdown,-->
    <!--                                        }"-->
    <!--                                        stroke-linecap="round"-->
    <!--                                        stroke-linejoin="round"-->
    <!--                                        stroke-width="2"-->
    <!--                                        d="M4 6h16M4 12h16M4 18h16"-->
    <!--                                    />-->
    <!--                                    <path-->
    <!--                                        :class="{-->
    <!--                                            hidden: !showingNavigationDropdown,-->
    <!--                                            'inline-flex': showingNavigationDropdown,-->
    <!--                                        }"-->
    <!--                                        stroke-linecap="round"-->
    <!--                                        stroke-linejoin="round"-->
    <!--                                        stroke-width="2"-->
    <!--                                        d="M6 18L18 6M6 6l12 12"-->
    <!--                                    />-->
    <!--                                </svg>-->
    <!--                            </button>-->
    <!--                        </div>-->
    <!--                    </div>-->
    <!--                </div>-->

    <!--                &lt;!&ndash; Responsive Navigation Menu &ndash;&gt;-->
    <!--                <div-->
    <!--                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"-->
    <!--                    class="sm:hidden"-->
    <!--                >-->
    <!--                    <div class="pt-2 pb-3 space-y-1">-->
    <!--                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">-->
    <!--                            Dashboard-->
    <!--                        </ResponsiveNavLink>-->
    <!--                    </div>-->

    <!--                    &lt;!&ndash; Responsive Settings Options &ndash;&gt;-->
    <!--                    <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">-->
    <!--                        <div class="px-4">-->
    <!--                            <div class="font-medium text-base text-gray-800 dark:text-gray-200">-->
    <!--                                {{ $page.props.auth.user.name }}-->
    <!--                            </div>-->
    <!--                            <div class="font-medium text-sm text-gray-500">{{ $page.props.auth.user.email }}</div>-->
    <!--                        </div>-->

    <!--                        <div class="mt-3 space-y-1">-->
    <!--                            <ResponsiveNavLink :href="route('profile.edit')"> Profile </ResponsiveNavLink>-->
    <!--                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">-->
    <!--                                Log Out-->
    <!--                            </ResponsiveNavLink>-->
    <!--                        </div>-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--            </nav>-->

    <!--            &lt;!&ndash; Page Content &ndash;&gt;-->
    <!--            <main>-->
    <!--                <slot />-->
    <!--            </main>-->
    <!--        </div>-->
    <!--    </div>-->

    <TooltipProvider :delay-duration="0">
        <ResizablePanelGroup
            id="resize-panel-group"
            direction="horizontal"
            class="min-h-screen max-h-screen items-stretch"
            auto-save-id="auto-save-id"
        >
            <ResizablePanel
                collapsible
                id="resize-panel-1"
                :collapsed-size="0"
                :min-size="10"
                :max-size="25"
                :class="
                    cn(
                        'p-4 flex flex-col',
                        collapsed &&
                            'min-w-[60px] transition-all duration-150 ease-in-out px-2',
                    )
                "
                @collapse="collapsed = true"
                @expand="collapsed = false"
            >
                <template v-slot:default="{ isCollapsed }">
                    <ApplicationLogo :collapsed="isCollapsed" />
                    <div class="flex flex-col flex-1">
                        <div class="mt-16 space-y-3">
                            <NavLink
                                :collapsed="isCollapsed"
                                :href="route('dashboard')"
                                :active="route().current('dashboard')"
                                :icon="Home"
                                label="Dashboard"
                            >
                            </NavLink>
                            <NavLink
                                :collapsed="isCollapsed"
                                :href="route('profile.edit')"
                                :active="route().current('profile*')"
                                :icon="Settings"
                                label="Settings"
                            >
                            </NavLink>
                        </div>
                        <div
                            class="mt-16 space-y-3 flex-1"
                            :class="isCollapsed && 'text-center'"
                        >
                            <Label class="inline-flex items-center">
                                <Component
                                    :is="hasMonitors ? Monitor : MonitorOff"
                                    class="w-4 h-4"
                                    :class="!isCollapsed && 'mr-2'"
                                />
                                <span v-if="!isCollapsed" class="truncate"
                                    >Monitors</span
                                >
                            </Label>
                            <template v-if="!hasMonitors">
                                <div
                                    v-if="!isCollapsed"
                                    class="truncate text-xs italic"
                                >
                                    No monitors yet.
                                </div>
                            </template>
                            <template v-else>
                                <MonitorLink
                                    v-for="monitor in $page.props.monitor_list
                                        .data"
                                    :key="monitor.id"
                                    :collapsed="isCollapsed"
                                    :monitor-list-item="monitor"
                                >
                                </MonitorLink>
                            </template>
                        </div>
                        <NavLink
                            method="post"
                            as="button"
                            :icon="LogOut"
                            :href="route('logout')"
                            label="Logout"
                        />
                    </div>
                </template>
            </ResizablePanel>
            <ResizableHandle id="resize-handle-1" />
            <ResizablePanel
                id="resize-panel-2"
                :default-size="collapsed ? 100 : 75"
                :min-size="75"
                class="bg-background-dark"
            >
                <div
                    class="flex flex-col px-4 sm:px-8 lg:px-12 py-4 h-full overflow-y-auto"
                >
                    <!-- Page Heading -->
                    <header v-if="$slots.header">
                        <div
                            class="max-w-7xl mx-auto pt-6 pb-10 flex justify-between items-center space-x-4 xl:space-x-6"
                        >
                            <slot name="header" />
                            <slot name="center" />
                            <slot name="actions" />
                        </div>
                    </header>

                    <slot />
                </div>
            </ResizablePanel>
        </ResizablePanelGroup>
    </TooltipProvider>
    <Toaster />
</template>
