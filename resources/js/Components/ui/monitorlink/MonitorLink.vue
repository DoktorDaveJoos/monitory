<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { cn } from '@/utils';
import { computed } from 'vue';
import { MonitorListItem } from '@/types';
import { PlugZap } from 'lucide-vue-next';

const props = defineProps<{
    monitorListItem: MonitorListItem;
    collapsed?: boolean;
}>();

const isActive = computed(() => {
    return route().current('monitor.show', props.monitorListItem.id);
});

// Upper case the first letter of each word
// and limit to max 2 characters
function initials(): string {
    return props.monitorListItem.name
        .split(' ')
        .map((word) => word.charAt(0))
        .join('')
        .slice(0, 2)
        .toUpperCase();
}
</script>

<template>
    <Link
        preserve-state
        :href="route('monitor.show', monitorListItem.id)"
        :class="
            cn(
                'inline-flex group cursor-pointer space-x-4 text-primary-foreground/80 overflow-hidden w-full h-10 px-4 py-2 font-medium items-center justify-start bg-background-dark hover:bg-background-dark/90 rounded-lg text-sm ring-offset-background focus:ring-2 focus:ring-offset-2 hover:text-foreground transition-colors duration-300',
                'inline-flex group cursor-pointer space-x-4 text-primary-foreground/80 overflow-hidden w-full h-10 px-4 py-2 font-medium items-center justify-start bg-background-dark hover:bg-background-dark/90 rounded-lg text-sm ring-offset-background focus:ring-2 focus:ring-offset-2 hover:text-foreground transition-colors duration-300',
                isActive &&
                    'text-primary-foreground ring-2 ring-offset-2 ring-offset-background',
                collapsed && 'px-0 justify-center',
                monitorListItem.status === null && 'ring-amber-300',
                monitorListItem.status && 'ring-success',
                monitorListItem.status === false && 'ring-destructive',
            )
        "
    >
        <template v-if="!collapsed">
            <template v-if="monitorListItem.status === null">
                <PlugZap class="w-4 h-4 shrink-0 text-amber-300" />
            </template>
            <template v-else-if="monitorListItem.status">
                <div
                    class="h-4 w-4 shrink-0 rounded-full bg-success/40 flex items-center justify-center"
                >
                    <div class="h-2 w-2 rounded-full bg-success"></div>
                </div>
            </template>
            <template v-else>
                <div
                    class="h-4 w-4 shrink-0 rounded-full bg-destructive/40 flex items-center justify-center"
                >
                    <div class="h-2 w-2 rounded-full bg-destructive"></div>
                </div>
            </template>
        </template>
        <span v-if="collapsed" class="truncate">{{ initials() }}</span>
        <span v-else class="truncate">{{ monitorListItem.name }}</span>
    </Link>
</template>
