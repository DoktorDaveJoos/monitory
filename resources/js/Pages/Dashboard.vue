<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Home } from 'lucide-vue-next';
import LayoutHeader from '@/Components/LayoutHeader.vue';
import DashboardChart from '@/Components/DashboardChart.vue';
import { Monitor, ResourceCollection, Stats as StatsType } from '@/types';
import { Label } from '@/Components/ui/label';
import Stats from '@/Components/Stats.vue';

defineProps<{
    monitors: ResourceCollection<Monitor>;
    check_labels: Array<string>;
    stats: StatsType;
}>();
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <LayoutHeader :icon="Home" label="Dashboard" />
        </template>

        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-10 2xl:grid-cols-12 gap-6 mb-10">
                <div class="hidden 2xl:block"></div>
                <Stats
                    class="col-span-2"
                    label="total checks"
                    :value="stats.total_checks"
                />
                <Stats
                    class="col-span-2"
                    label="total alerts"
                    :value="stats.total_notifications"
                />
                <Stats
                    class="col-span-2"
                    label="total monitors"
                    :value="stats.total_monitors"
                />
                <Stats
                    class="col-span-2"
                    label="uptime overall"
                    :value="stats.uptime_overall"
                />
                <Stats
                    class="col-span-2"
                    label="avg response time"
                    :value="stats.average_response_time"
                />
            </div>

            <Label class="mb-4">Https / Http</Label>
            <div class="pb-12 space-y-6">
                <DashboardChart
                    v-for="monitor in monitors.data"
                    :monitor="monitor"
                    :check_labels="check_labels"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
