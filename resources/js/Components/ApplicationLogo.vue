<template>
    <div class="flex flex-col items-center space-y-2">
        <img src="/img/logo.svg" alt="monitory.io" class="h-8" />
        <span
            v-if="!collapsed"
            class="text-sm font-mono font-medium text-primary-foreground/50 leading-tight"
        >
            monitory {{ $page.props.appVersion }}
        </span>
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
