<script setup lang="ts">
import { Label } from '@/Components/ui/label';

defineProps<{
    value?: string;
}>();
</script>

<template>
    <Label>
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </Label>
</template>
