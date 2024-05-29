<script setup lang="ts">
import { Monitor } from '@/types';
import { Card } from '@/Components/ui/card';
import { Chart } from '@/Components/ui/chart';
import { Trash } from 'lucide-vue-next';

defineProps<{
    monitor: Monitor;
    check_labels: Array<string>;
}>();
</script>

<template>
    <Card class="p-4 sm:p-8 flex items-center">
        <div class="h-4 w-4 shrink-0 bg-success rounded-full" />
        <div class="flex flex-col">
            <span>Google</span>
            <span>99.98%</span>
        </div>
        <div class="flex flex-1 relative">
            <Chart
                :checks="monitor.checks"
                :check_labels="check_labels"
                :options="{ show_x_ticks: false }"
            />
        </div>
        <div class="flex">
            <Trash class="h-6 w-6" />
            <Trash class="h-6 w-6" />
            <Trash class="h-6 w-6" />
        </div>
    </Card>
</template>
