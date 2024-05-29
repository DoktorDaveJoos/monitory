<template>
    <div class="flex flex-col items-center h-20">
        <img src="/img/logo.svg" alt="monitory.io" class="h-8" />
        <div
            v-if="!collapsed"
            class="text-sm mt-2 font-mono truncate font-medium text-primary-foreground/50 leading-tight"
        >
            monitory
        </div>
        <div
            v-if="!collapsed"
            class="font-mono truncate text-xs font-medium text-primary-foreground/50 leading-tight"
        >
            {{ $page.props.app.version }}
        </div>
    </div>
</template>
<script setup lang="ts">
defineProps({
    collapsed: {
        type: Boolean,
        default: false,
    },
});
</script>
