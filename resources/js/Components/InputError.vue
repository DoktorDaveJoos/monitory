<script setup lang="ts">
defineProps<{
    message?: string;
}>();
</script>

<template>
    <div v-if="message">
        <p class="text-sm italic text-destructive">
            {{ message }}
        </p>
    </div>
</template>
