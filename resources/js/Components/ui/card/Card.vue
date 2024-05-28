<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from '@/utils';

const props = defineProps<{
    class?: HTMLAttributes['class'];
}>();
</script>

<template>
    <div
        :class="
            cn('rounded-xl bg-card text-card-foreground shadow-xl', props.class)
        "
    >
        <slot />
    </div>
</template>
