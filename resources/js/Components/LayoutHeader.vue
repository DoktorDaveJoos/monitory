<script setup lang="ts">
import { FunctionalComponent } from 'vue';

defineProps<{
    icon: FunctionalComponent;
    label: string;
}>();
</script>

<template>
    <div class="flex items-center space-x-4 truncate">
        <Component :is="icon" class="w-6 h-6 text-foreground shrink-0" />
        <h2 class="text-2xl text-foreground truncate">
            {{ label }}
        </h2>
        <slot />
    </div>
</template>
