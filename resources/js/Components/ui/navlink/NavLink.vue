<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { cn } from '@/utils';
import { FunctionalComponent } from 'vue';

defineProps<{
    href: string;
    active?: boolean;
    collapsed?: boolean;
    icon: FunctionalComponent;
    label: string;
}>();
</script>

<template>
    <Link
        :href="href"
        :class="
            cn(
                'inline-flex group space-x-4 text-primary-foreground/80 overflow-hidden w-full h-10 px-4 py-2 font-medium items-center justify-start bg-background-dark hover:bg-background-dark/90 rounded-lg text-sm ring-offset-background focus-visible:outline-none focus:ring-2 focus:ring-offset-2 hover:text-foreground focus:ring-background-dark transition-colors duration-300',
                active &&
                    'text-primary-foreground ring-2 ring-offset-2 ring-offset-background ring-foreground/50',
                collapsed && 'px-0 justify-center',
            )
        "
    >
        <Component
            :is="icon"
            class="w-4 h-4 shrink-0 transition-colors duration-300"
            :class="active ? 'text-primary' : 'text-foreground/50'"
        />
        <span v-if="!collapsed" class="truncate">{{ label }}</span>
    </Link>
</template>
