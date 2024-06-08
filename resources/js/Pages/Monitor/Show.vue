<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Chart } from '@/Components/ui/chart';
import {
    Check,
    Monitor,
    OptionEnum,
    ResourceCollection,
    Trigger,
} from '@/types';
import {
    CircleAlert,
    ExternalLink,
    Monitor as MonitorIcon,
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

const props = defineProps<{
    monitor: Monitor;
    checks: ResourceCollection<Check>;
    check_labels: Array<string>;
    trigger: ResourceCollection<Trigger>;
    trigger_options: {
        trigger_types: ResourceCollection<OptionEnum>;
        operators: ResourceCollection<OptionEnum>;
        http_status_codes: ResourceCollection<OptionEnum>;
    };
}>();

const confirmingMonitorDeletion = ref(false);
const nameConfirmation = ref('');
const createTriggerDialog = ref(false);

const monitorForm = useForm<{
    method: string;
    interval: string;
}>({
    method: props.monitor.method,
    interval: props.monitor.interval.toString(),
});

const triggerForm = useForm<{
    type: string;
    operator: string;
    value: string | number;
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

const deleteMonitor = () => {
    useForm({}).delete(route('monitor.destroy', props.monitor.id), {
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

// @todo fix this
const up = computed(
    () => props.checks.data[props.checks.data.length - 1]?.success,
);

const addTrigger = () => {
    triggerForm.post(route('trigger.store', props.monitor.id), {
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
            monitor: props.monitor.id,
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
                <LayoutHeader :icon="MonitorIcon" :label="monitor.name" />
                <div
                    class="h-5 w-5 flex shrink-0 rounded-full items-center justify-center"
                    :class="up ? 'bg-success/40' : 'bg-destructive/40'"
                >
                    <div
                        class="h-3 w-3 rounded-full"
                        :class="up ? 'bg-success' : 'bg-destructive'"
                    />
                </div>
            </div>
        </template>
        <template #center>
            <Card class="px-4 py-2 h-10 flex space-x-2 overflow-hidden">
                <span class="font-bold">{{ monitor.method }}</span>
                <div class="border-r-2 border-accent my-1" />
                <span class="truncate">{{ monitor.url }}</span>
            </Card>
        </template>
        <template #actions>
            <div class="flex">
                <Dialog
                    v-model:open="confirmingMonitorDeletion"
                    :default-open="false"
                >
                    <DialogTrigger as-child>
                        <Button variant="destructive">
                            Delete
                            <Trash2 class="w-4 h-4 shrink-0 ml-1" />
                        </Button>
                    </DialogTrigger>
                    <DialogContent class="sm:max-w-[425px]">
                        <DialogHeader>
                            <DialogTitle
                                >Are you sure you want to delete your monitor?
                            </DialogTitle>
                            <DialogDescription>
                                Once your monitor is deleted, all of its
                                resources and data will be permanently deleted.
                                Please enter your password to confirm you would
                                like to permanently delete your monitor.
                            </DialogDescription>
                        </DialogHeader>
                        <div class="grid gap-4 py-4">
                            <div class="items-center gap-4">
                                <Label class="text-right"> Name </Label>
                                <div
                                    class="font-mono px-4 mb-4 py-2 rounded-lg bg-foreground text-background"
                                >
                                    {{ monitor.name }}
                                </div>
                                <Label
                                    :for="`name-confirmation-${monitor.id}`"
                                    class="text-right"
                                >
                                    Repeat the monitor name to confirm
                                </Label>
                                <Input
                                    :id="`name-confirmation-${monitor.id}`"
                                    v-model="nameConfirmation"
                                    class="col-span-3"
                                />
                                <InputError message="" class="mt-2" />
                            </div>
                        </div>
                        <DialogFooter>
                            <Button
                                variant="destructive"
                                :disabled="nameConfirmation !== monitor.name"
                                @click="deleteMonitor"
                            >
                                Delete Monitor
                            </Button>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>
            </div>
        </template>

        <div class="max-w-7xl mx-auto w-full">
            <Alert
                v-if="trigger.data.length === 0"
                variant="destructive"
                class="mb-6"
            >
                <CircleAlert class="w-4 h-4" />
                <AlertTitle>Missing Trigger</AlertTitle>
                <AlertDescription>
                    You don't have any triggers setup to notify you when your
                    monitor fails.
                </AlertDescription>
            </Alert>

            <Label>Overview</Label>
            <Card class="relative py-4 mt-1">
                <Chart
                    v-if="checks.data.length > 0"
                    :checks="checks"
                    :check_labels="check_labels"
                    :options="{
                        size: 'large',
                        fading: true,
                        show_x_ticks: true,
                    }"
                />

                <div class="flex justify-center mt-2">
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                        <MonitorStats label="Success" value="345" trend="5%" />
                        <MonitorStats label="4xx" value="45" trend="0%" />
                        <MonitorStats label="5xx" value="4" trend="-5%" />
                        <MonitorStats label="Timeouts" value="11" trend="5%" />
                        <MonitorStats
                            label="Responsetime"
                            value="186ms"
                            trend="5%"
                        />
                    </div>
                </div>
            </Card>

            <div class="grid md:grid-cols-2 gap-4 mt-8">
                <div class="space-y-2">
                    <Label>Settings</Label>
                    <Card class="flex justify-between items-center px-4 h-14">
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
                </div>
                <div class="space-y-2">
                    <Label>Notification Trigger</Label>
                    <Card
                        v-for="_trigger in trigger.data"
                        class="flex justify-between items-center px-4 h-14"
                    >
                        <div class="flex items-center space-x-2">
                            <CircleAlert
                                class="w-5 h-5 text-destructive shrink-0"
                            />
                            <span
                                class="font-light text-xs text-primary-foreground/50 uppercase"
                                >When</span
                            >
                            <Label>{{ _trigger.type }}</Label>
                            <span
                                class="font-light text-xs text-primary-foreground/50 uppercase"
                                >{{ _trigger.operator }}</span
                            >
                            <Label>{{ _trigger.value }}</Label>
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
                                            ? 'Create Trigger'
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
                                    <Select v-model="triggerForm.operator">
                                        <SelectTrigger class="w-full">
                                            <SelectValue
                                                placeholder="Select a trigger operator"
                                            />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectLabel
                                                    >Options
                                                </SelectLabel>
                                                <SelectItem
                                                    v-for="type in trigger_options
                                                        .operators.data"
                                                    :value="type.value"
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
                                        v-if="
                                            triggerForm.type ===
                                            'http_status_code'
                                        "
                                        v-model="triggerForm.value"
                                    >
                                        <SelectTrigger class="w-full">
                                            <SelectValue
                                                placeholder="Select an HTTP status code"
                                            />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectLabel
                                                    >Options
                                                </SelectLabel>
                                                <SelectItem
                                                    v-for="status_code in trigger_options
                                                        .http_status_codes.data"
                                                    :value="status_code.value"
                                                >
                                                    {{ status_code.label }}
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                    <Input v-else v-model="triggerForm.value" />
                                    <InputError
                                        :message="triggerForm.errors.operator"
                                        class="mt-2"
                                    />
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
