<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Home, PlugZap, Plus } from 'lucide-vue-next';
import LayoutHeader from '@/Components/LayoutHeader.vue';
import DashboardChart from '@/Components/DashboardChart.vue';
import { Monitor, ResourceCollection, Stats as StatsType } from '@/types';
import { Label } from '@/Components/ui/label';
import Stats from '@/Components/Stats.vue';
import { Button } from '@/Components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/Components/ui/dialog';
import { Input } from '@/Components/ui/input';
import { ref } from 'vue';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import InputError from '@/Components/InputError.vue';
import axios from 'axios';
import { isUrlReachable } from '@/utils';

defineProps<{
    monitors: ResourceCollection<Monitor>;
    check_labels: Array<string>;
    stats: StatsType;
}>();

const addMonitor = ref(false);
const form = useForm({
    name: '',
    interval: '',
    method: '',
    type: '',
    url: '',
});
const connection = ref<boolean | null>(null);

const submit = () => {
    form.post(route('monitor.store'), {
        onSuccess: () => {
            addMonitor.value = false;
        },
    });
};

const testConnection = async () => {
    connection.value = await isUrlReachable(form.url);
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <LayoutHeader :icon="Home" label="Dashboard" />
        </template>
        <template #actions>
            <Dialog v-model:open="addMonitor" :default-open="false">
                <DialogTrigger as-child>
                    <Button :href="route('monitor.store')">
                        Add monitor
                        <Plus class="h-5 w-5 ml-1" />
                    </Button>
                </DialogTrigger>
                <DialogContent class="sm:max-w-2xl">
                    <DialogHeader>
                        <DialogTitle class="flex"
                            >Create Monitor
                            <Plus class="h-4 w-4 ml-1" />
                        </DialogTitle>
                    </DialogHeader>
                    <div class="grid gap-4 grid-cols-2 mb-4">
                        <div>
                            <Label for="monitor-type">Monitor Type</Label>
                            <Select v-model="form.type">
                                <SelectTrigger class="w-full">
                                    <SelectValue
                                        placeholder="Select a monitor type"
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Options</SelectLabel>
                                        <SelectItem value="http">
                                            http / https
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.type" />
                        </div>
                        <div>
                            <Label for="interval">Interval</Label>
                            <Select v-model="form.interval">
                                <SelectTrigger class="w-full">
                                    <SelectValue
                                        placeholder="Select an interval"
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Options</SelectLabel>
                                        <SelectItem value="1">
                                            60 seconds
                                        </SelectItem>
                                        <SelectItem value="5">
                                            5 minutes
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.interval" />
                        </div>

                        <template v-if="form.type === 'http'">
                            <div class="col-span-2">
                                <Label for="monitor-url">URL</Label>
                                <Input
                                    id="monitor-url"
                                    v-model="form.url"
                                    :error="form.errors.url"
                                />
                                <InputError :message="form.errors.url" />
                            </div>
                            <div>
                                <Label for="monitor-name">Monitor Name</Label>
                                <Input
                                    id="monitor-name"
                                    v-model="form.name"
                                    :error="form.errors.name"
                                />
                                <InputError :message="form.errors.name" />
                            </div>
                            <div>
                                <Label for="monitor-method"
                                    >HTTP/s Method</Label
                                >
                                <Select v-model="form.method">
                                    <SelectTrigger class="w-full">
                                        <SelectValue
                                            placeholder="Select a method"
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectLabel>Options</SelectLabel>
                                            <SelectItem value="GET">
                                                GET
                                            </SelectItem>
                                            <SelectItem value="POST">
                                                POST
                                            </SelectItem>
                                            <SelectItem value="PUT">
                                                PUT
                                            </SelectItem>
                                            <SelectItem value="DELETE">
                                                DELETE
                                            </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.method" />
                            </div>
                        </template>
                    </div>
                    <DialogFooter class="space-x-2">
                        <div
                            v-if="connection"
                            class="text-success text-xs font-bold inline-flex items-center"
                        >
                            <div class="h-2 w-2 rounded-full bg-success mr-1" />
                            Connected
                        </div>
                        <div
                            v-else-if="connection === false"
                            class="text-destructive text-xs font-bold inline-flex items-center"
                        >
                            <div
                                class="h-2 w-2 rounded-full bg-destructive mr-1"
                            />
                            Unreachable
                        </div>
                        <Button
                            variant="secondary"
                            :disabled="!form.url"
                            @click="testConnection"
                        >
                            Check Connection
                            <PlugZap class="h-5 w-5 ml-1" />
                        </Button>
                        <Button :disabled="form.processing" @click="submit">
                            Create
                            <Plus class="h-5 w-5 ml-1" />
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
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
