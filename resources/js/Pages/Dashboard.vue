<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Home } from 'lucide-vue-next';
import LayoutHeader from '@/Components/LayoutHeader.vue';
import DashboardChart from '@/Components/DashboardChart.vue';
import { Monitor, ResourceCollection } from '@/types';
import { Label } from '@/Components/ui/label';
import Stats from '@/Components/Stats.vue';

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

        <div class="grid grid-cols-12 gap-6 mb-10">
            <div></div>
            <Stats class="col-span-2" label="total checks" value="3.523.326" />
            <Stats class="col-span-2" label="total notifications" value="251" />
            <Stats class="col-span-2" label="total monitors" value="1" />
            <Stats class="col-span-2" label="uptime overall" value="99.45%" />
            <Stats class="col-span-2" label="avg response time" value="186ms" />
        </div>

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
