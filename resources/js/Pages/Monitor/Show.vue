<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Chart } from '@/Components/ui/chart';
import { Check, Monitor } from '@/types';
import { Monitor as MonitorIcon } from 'lucide-vue-next';
import LayoutHeader from '@/Components/LayoutHeader.vue';
import { computed } from 'vue';

const props = defineProps<{
    monitor: Monitor;
    checks: {
        data: Array<Check>;
    };
    check_labels: Array<string>;
}>();

const up = computed(
    () => props.checks.data[props.checks.data.length - 1].success,
);
</script>

<template>
    <Head title="Monitor" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex space-x-4 items-center">
                <LayoutHeader :icon="MonitorIcon" :label="monitor.name" />
                <div
                    class="h-5 w-5 flex rounded-full items-center justify-center"
                    :class="up ? 'bg-success/40' : 'bg-destructive/40'"
                >
                    <div
                        class="h-3 w-3 rounded-full"
                        :class="up ? 'bg-success' : 'bg-destructive'"
                    />
                </div>
            </div>
        </template>

        <Chart :checks="checks" :check_labels="check_labels" />
    </AuthenticatedLayout>
</template>
