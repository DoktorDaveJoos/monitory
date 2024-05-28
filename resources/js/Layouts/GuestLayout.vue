<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-background-dark dark:bg-gray-900"
    >
        <div>
            <Link href="/">
                <ApplicationLogo
                    class="w-full h-20 fill-current text-gray-500"
                />
            </Link>
        </div>

        <slot />
    </div>
</template>
