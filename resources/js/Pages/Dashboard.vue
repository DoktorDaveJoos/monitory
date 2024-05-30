<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Home } from 'lucide-vue-next';
import LayoutHeader from '@/Components/LayoutHeader.vue';
import DashboardChart from '@/Components/DashboardChart.vue';
import { Monitor, ResourceCollection } from '@/types';
import { Label } from '@/Components/ui/label';

defineProps<{
    monitors: ResourceCollection<Monitor>;
    check_labels: Array<string>;
}>();
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <LayoutHeader :icon="Home" label="Dashboard" />
        </template>

        <Label class="mb-4">Https / Http</Label>
        <div class="pb-12 space-y-6">
            <DashboardChart
                v-for="monitor in monitors.data"
                :monitor="monitor"
                :check_labels="check_labels"
            />
        </div>
    </AuthenticatedLayout>
</template>
