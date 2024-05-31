<script setup lang="ts">
import { Card } from '@/Components/ui/card';
import { Label } from '@/Components/ui/label';

defineProps<{
    label: string;
    value: string | number;
}>();
</script>

<template>
    <Card class="flex flex-col justify-between px-4 py-4">
        <div class="w-full text-center">
            <Label class="mb-4">{{ label }}</Label>
        </div>

        <div class="w-full text-center">
            <span class="font-mono text-lg">{{ value }}</span>
        </div>
    </Card>
</template>

<style scoped></style>
