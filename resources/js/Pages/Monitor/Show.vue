<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Chart } from '@/Components/ui/chart';
import {
    Monitor,
    MonitorStats as MonitorStatsType,
    OperatorsCollection,
    OptionEnum,
    ResourceCollection,
    ResourceItem,
    Trigger,
} from '@/types';
import {
    CircleAlert,
    EllipsisVertical,
    ExternalLink,
    Flame,
    Monitor as MonitorIcon,
    PlugZap,
    Plus,
    Trash2,
} from 'lucide-vue-next';
import LayoutHeader from '@/Components/LayoutHeader.vue';
import { computed, ref, watch } from 'vue';
import { Card } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Label } from '@/Components/ui/label';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/Components/ui/dialog';
import InputError from '@/Components/InputError.vue';
import { Input } from '@/Components/ui/input';
import { useToast } from '@/Components/ui/toast';
import MonitorStats from '@/Components/MonitorStats.vue';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import { Alert, AlertDescription, AlertTitle } from '@/Components/ui/alert';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu';
import { watchDeep } from '@vueuse/core';

const props = defineProps<{
    monitor: ResourceItem<Monitor>;
    check_labels: Array<string>;
    trigger: ResourceCollection<Trigger>;
    trigger_options: {
        trigger_types: ResourceCollection<OptionEnum>;
        operators: OperatorsCollection;
        http_status_codes: ResourceCollection<OptionEnum>;
    };
    monitor_stats: MonitorStatsType;
}>();

const confirmingMonitorDeletion = ref(false);
const nameConfirmation = ref('');
const createTriggerDialog = ref(false);
const options = ref([]);

const monitorForm = useForm<{
    method: string;
    interval: string;
}>({
    method: props.monitor.data.method,
    interval: props.monitor.data.interval.toString(),
});

const triggerForm = useForm<{
    type: string;
    operator: string;
    value: string;
}>({
    type: '',
    operator: '',
    value: '',
});

watch(confirmingMonitorDeletion, (value) => {
    if (!value) {
        nameConfirmation.value = '';
    }
});

watch(
    () => [monitorForm.method, monitorForm.interval],
    () => {
        monitorForm.put(route('monitor.update', props.monitor.data.id));
    },
    { immediate: false },
);

watchDeep(triggerForm, async () => {
    if (!triggerForm.type) {
        options.value = [];
        return;
    }

    const response = await axios.get(
        route('trigger.options', {
            monitor: props.monitor.data.id,
            type: triggerForm.type,
        }),
    );

    const { data } = response.data;

    options.value = data;
});

const operators = computed(() => {
    return (
        props.trigger_options.operators.data.find(
            (entry) => entry.trigger === triggerForm.type,
        )?.value ?? []
    );
});

const unit = computed(() => {
    return (
        props.trigger_options.trigger_types.data.find(
            (entry) => entry.value === triggerForm.type,
        )?.unit ?? ''
    );
});

const deleteMonitor = () => {
    useForm({}).delete(route('monitor.destroy', props.monitor.data.id), {
        onSuccess: () => {
            confirmingMonitorDeletion.value = false;
            useToast().toast({
                title: 'Deleted',
                description: 'Your monitor has been deleted.',
                variant: 'destructive',
            });
        },
    });
};

const addTrigger = () => {
    triggerForm.post(route('trigger.store', props.monitor.data.id), {
        onSuccess: () => {
            createTriggerDialog.value = false;
            triggerForm.reset();
            useToast().toast({
                title: 'Trigger Created',
                description: 'Your trigger has been created.',
            });
        },
    });
};

const deleteTrigger = (id: string | number) => {
    useForm({}).delete(
        route('trigger.destroy', {
            monitor: props.monitor.data.id,
            trigger: id,
        }),
        {
            onSuccess: () => {
                useToast().toast({
                    title: 'Trigger Deleted',
                    description: 'Your trigger has been deleted.',
                });
            },
        },
    );
};
</script>

<template>
    <Head title="Monitor" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex space-x-4 items-center overflow-hidden">
                <LayoutHeader :icon="MonitorIcon" :label="monitor.data.name" />
                <div
                    class="h-5 w-5 flex shrink-0 rounded-full items-center justify-center"
                    :class="
                        monitor.data.status
                            ? 'bg-success/40'
                            : 'bg-destructive/40'
                    "
                >
                    <div
                        class="h-3 w-3 rounded-full"
                        :class="
                            monitor.data.status
                                ? 'bg-success'
                                : 'bg-destructive'
                        "
                    />
                </div>
            </div>
        </template>
        <template #center>
            <Card class="px-4 py-2 h-10 flex space-x-2 overflow-hidden">
                <span class="font-bold">{{
                    monitor.data.method ?? 'Ping'
                }}</span>
                <div class="border-r-2 border-accent my-1" />
                <span class="truncate">{{
                    monitor.data.url ?? monitor.data.host
                }}</span>
            </Card>
        </template>
        <template #actions>
            <div class="flex">
                <DropdownMenu>
                    <DropdownMenuTrigger>
                        <EllipsisVertical class="w-4 h-4" />
                    </DropdownMenuTrigger>
                    <DropdownMenuContent>
                        <DropdownMenuLabel>Options</DropdownMenuLabel>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem :disabled="true">
                            Pause Monitor
                        </DropdownMenuItem>
                        <DropdownMenuItem
                            @click="confirmingMonitorDeletion = true"
                        >
                            Delete Monitor
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
            <Dialog
                v-model:open="confirmingMonitorDeletion"
                :default-open="false"
            >
                <DialogContent class="sm:max-w-[425px]">
                    <DialogHeader>
                        <DialogTitle
                            >Are you sure you want to delete your monitor?
                        </DialogTitle>
                        <DialogDescription>
                            Once your monitor is deleted, all of its resources
                            and data will be permanently deleted. Please enter
                            your password to confirm you would like to
                            permanently delete your monitor.
                        </DialogDescription>
                    </DialogHeader>
                    <div class="grid gap-4 py-4">
                        <div class="items-center gap-4">
                            <Label class="text-right"> Name </Label>
                            <div
                                class="font-mono px-4 mb-4 py-2 rounded-lg bg-foreground text-background"
                            >
                                {{ monitor.data.name }}
                            </div>
                            <Label
                                :for="`name-confirmation-${monitor.data.id}`"
                                class="text-right"
                            >
                                Repeat the monitor name to confirm
                            </Label>
                            <Input
                                :id="`name-confirmation-${monitor.data.id}`"
                                v-model="nameConfirmation"
                                class="col-span-3"
                            />
                            <InputError message="" class="mt-2" />
                        </div>
                    </div>
                    <DialogFooter>
                        <Button
                            variant="destructive"
                            :disabled="nameConfirmation !== monitor.data.name"
                            @click="deleteMonitor"
                        >
                            Delete Monitor
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </template>

        <div class="max-w-7xl mx-auto w-full">
            <Alert
                v-if="trigger.data.length === 0"
                variant="default"
                class="mb-6"
            >
                <Flame class="w-5 h-5 mr-2" />
                <AlertTitle>You made it!</AlertTitle>
                <AlertDescription>
                    Your monitor is up and running! 🚀

                    <br />

                    Make sure to setup your notification triggers to get
                    notified when your monitor fails.

                    <br />
                    <br />

                    <Button
                        size="xs"
                        class="text-xs"
                        variant="secondary"
                        @click="createTriggerDialog = true"
                    >
                        Create notification trigger
                        <Plus class="w-4 h-4 shrink-0 ml-1" />
                    </Button>
                </AlertDescription>
            </Alert>

            <Label>Overview</Label>
            <Card class="relative py-4 mt-1">
                <Chart
                    v-if="monitor.data.checks.length > 0"
                    :checks="monitor.data.checks"
                    :check_labels="check_labels"
                    :options="{
                        size: 'large',
                        fading: false,
                        show_x_ticks: true,
                    }"
                />
                <div v-else>
                    <div class="flex justify-center items-center h-40">
                        <PlugZap class="w-6 h-6 mr-2" />
                        <span
                            >Monitor just created. Waiting for first
                            check.</span
                        >
                    </div>
                </div>

                <div class="flex justify-center mt-2">
                    <div
                        :class="
                            monitor.type === 'http'
                                ? 'grid grid-cols-2 md:grid-cols-5 gap-2'
                                : 'grid grid-cols-2 gap-2'
                        "
                    >
                        <MonitorStats
                            v-if="monitor.type === 'http'"
                            label="2xx"
                            :value="monitor_stats['2xx'].absolute"
                            :trend="monitor_stats['2xx'].percentage"
                        />
                        <MonitorStats
                            v-if="monitor.type === 'http'"
                            label="4xx"
                            :value="monitor_stats['4xx'].absolute"
                            :trend="monitor_stats['4xx'].percentage"
                        />
                        <MonitorStats
                            v-if="monitor.type === 'http'"
                            label="5xx"
                            :value="monitor_stats['5xx'].absolute"
                            :trend="monitor_stats['5xx'].percentage"
                        />
                        <MonitorStats
                            label="Timeouts"
                            :value="monitor_stats.timeouts.absolute"
                            :trend="monitor_stats.timeouts.percentage"
                        />
                        <MonitorStats
                            label="Responsetime"
                            :value="monitor_stats.latency.overall"
                            :trend="monitor_stats.latency.last_hour"
                        />
                    </div>
                </div>
            </Card>

            <div class="grid xl:grid-cols-2 gap-4 mt-8">
                <div class="space-y-2">
                    <Label>Settings</Label>
                    <Card
                        v-if="monitor.type === 'http'"
                        class="flex justify-between items-center px-4 h-14"
                    >
                        <Label>Method</Label>
                        <Select v-model="monitorForm.method">
                            <SelectTrigger>
                                <SelectValue placeholder="Select a method" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>Methods</SelectLabel>
                                    <SelectItem value="GET"> GET</SelectItem>
                                    <SelectItem value="POST"> POST</SelectItem>
                                    <SelectItem value="PUT"> PUT</SelectItem>
                                    <SelectItem value="DELETE">
                                        DELETE
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                    </Card>
                    <Card class="flex justify-between items-center px-4 h-14">
                        <Label>Interval</Label>
                        <Select v-model="monitorForm.interval">
                            <SelectTrigger>
                                <SelectValue placeholder="Select an Interval" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>Interval</SelectLabel>
                                    <SelectItem value="1"> 60s</SelectItem>
                                    <SelectItem value="5"> 5m</SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                    </Card>
                    <InputError
                        :message="monitorForm.errors.interval"
                        class="mt-2"
                    />
                </div>
                <div class="space-y-2">
                    <Label>Notification Trigger</Label>
                    <Card
                        v-for="_trigger in trigger.data"
                        class="flex justify-between items-center px-4 h-14"
                    >
                        <div class="flex items-center space-x-2 max-w-full">
                            <CircleAlert
                                class="w-5 h-5 text-destructive shrink-0"
                            />
                            <span
                                class="font-light text-xs text-primary-foreground/50 uppercase truncate"
                                >When</span
                            >
                            <Label class="truncate">{{ _trigger.type }}</Label>
                            <div
                                class="font-light text-xs text-primary-foreground/50 uppercase truncate"
                            >
                                {{ _trigger.operator }}
                            </div>
                            <Label class="truncate">{{
                                _trigger.value.toString()
                            }}</Label>
                        </div>
                        <button @click="deleteTrigger(_trigger.id)">
                            <Trash2 class="w-5 h-5 text-destructive shrink-0" />
                        </button>
                    </Card>

                    <Dialog
                        v-model:open="createTriggerDialog"
                        :default-open="false"
                    >
                        <DialogTrigger as-child>
                            <button
                                class="rounded-xl w-full border-2 border-dashed text-primary border-primary h-14 flex justify-center items-center px-4 hover:bg-primary/10 hover:border-solid transition-all duration-150"
                            >
                                <span
                                    class="text-primary uppercase text-xs font-bold tracking-widest"
                                    >{{
                                        trigger.data.length === 0
                                            ? 'Create Notification Trigger'
                                            : 'More'
                                    }}</span
                                >
                                <Plus class="w-5 h-5 shrink-0 ml-1" />
                            </button>
                        </DialogTrigger>
                        <DialogContent class="sm:max-w-[425px]">
                            <DialogHeader>
                                <DialogTitle>Add Trigger</DialogTitle>
                                <DialogDescription>
                                    Add a trigger to notify you when your
                                    monitor fails.
                                </DialogDescription>
                            </DialogHeader>
                            <div class="grid gap-4 py-4">
                                <div class="items-center gap-4">
                                    <Label for="type"> Type </Label>
                                    <Select v-model="triggerForm.type">
                                        <SelectTrigger class="w-full">
                                            <SelectValue
                                                placeholder="Select a trigger type"
                                            />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectLabel
                                                    >Options
                                                </SelectLabel>
                                                <SelectItem
                                                    v-for="type in trigger_options
                                                        .trigger_types.data"
                                                    :value="type.value"
                                                >
                                                    {{ type.label }}
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                    <InputError
                                        :message="triggerForm.errors.type"
                                        class="mt-2"
                                    />

                                    <Label for="type"> Operator </Label>
                                    <Select
                                        v-model="triggerForm.operator"
                                        :disabled="operators.length === 0"
                                    >
                                        <SelectTrigger
                                            class="w-full"
                                            :class="
                                                operators.length === 0
                                                    ? 'bg-gray-200 cursor-not-allowed'
                                                    : ''
                                            "
                                        >
                                            <SelectValue
                                                :placeholder="
                                                    operators.length === 0
                                                        ? '-'
                                                        : 'Select a trigger operator'
                                                "
                                            />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectLabel
                                                    >Options
                                                </SelectLabel>
                                                <SelectItem
                                                    v-for="type in operators"
                                                    :value="
                                                        type.value.toString()
                                                    "
                                                >
                                                    {{ type.label }}
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                    <InputError
                                        :message="triggerForm.errors.operator"
                                        class="mt-2"
                                    />

                                    <Label for="type"> Value </Label>
                                    <Select
                                        v-if="options.length > 0"
                                        v-model="triggerForm.value"
                                    >
                                        <SelectTrigger class="w-full">
                                            <SelectValue
                                                placeholder="Select an Option"
                                            />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectLabel
                                                    >Options
                                                </SelectLabel>
                                                <SelectItem
                                                    v-for="option in options"
                                                    :value="
                                                        option.value.toString()
                                                    "
                                                >
                                                    {{ option.label }}
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                    <div
                                        v-else
                                        class="relative w-full items-center"
                                    >
                                        <Input
                                            :disabled="operators.length === 0"
                                            :placeholder="
                                                operators.length === 0
                                                    ? '-'
                                                    : 'Enter a value'
                                            "
                                            v-model="triggerForm.value"
                                            :class="
                                                operators.length === 0
                                                    ? 'bg-gray-200 cursor-not-allowed'
                                                    : ''
                                            "
                                        />
                                        <InputError
                                            :message="
                                                triggerForm.errors.operator
                                            "
                                            class="mt-2"
                                        />
                                        <span
                                            class="absolute end-3 inset-y-0 text-card uppercase tracking-wide font-bold text-xs flex items-center justify-center"
                                        >
                                            <span>{{ unit }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <DialogFooter>
                                <Button
                                    :disabled="triggerForm.processing"
                                    @click="addTrigger"
                                >
                                    Create Trigger
                                </Button>
                            </DialogFooter>
                        </DialogContent>
                    </Dialog>

                    <div class="flex justify-center">
                        <Link
                            :href="route('profile.edit')"
                            class="text-sm italic flex items-center text-foreground/50 hover:text-foreground/90 transition-colors duration-150"
                        >
                            Setup your notification channels
                            <ExternalLink class="w-4 h-4 shrink-0 ml-1" />
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
