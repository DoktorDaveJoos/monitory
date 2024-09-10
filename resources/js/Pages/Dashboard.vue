<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Home, Loader2, MonitorOff, PlugZap, Plus } from 'lucide-vue-next';
import LayoutHeader from '@/Components/LayoutHeader.vue';
import DashboardChart from '@/Components/DashboardChart.vue';
import { Monitor, ResourceCollection, Stats as StatsType } from '@/types';
import { Label } from '@/Components/ui/label';
import Stats from '@/Components/Stats.vue';
import { Button } from '@/Components/ui/button';
import { Switch } from '@/Components/ui/switch';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/Components/ui/dialog';
import { Input } from '@/Components/ui/input';
import { ref, watch } from 'vue';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/Components/ui/accordion';
import InputError from '@/Components/InputError.vue';
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
    auth: null,
    auth_username: null,
    auth_password: null,
    auth_token: null,
});
const connection = ref<boolean | null>(null);
const showAuth = ref<boolean>(false);

const submit = () => {
    form.post(route('monitor.store'), {
        onSuccess: () => {
            addMonitor.value = false;
        },
    });
};

const isChecking = ref(false);
const testConnection = async () => {
    try {
        isChecking.value = true;
        connection.value = await isUrlReachable(form.url);
        isChecking.value = false;
    } catch (error) {
        isChecking.value = false;
        connection.value = false;
    }
};

watch(
    () => form.auth,
    () => {
        resetAuth();
    },
);

const resetAuth = () => {
    form.reset('auth_username', 'auth_password', 'auth_token');
};

const handleAuthSwitch = (value: boolean) => {
    if (!value) {
        resetAuth();
        form.reset('auth');
    }

    showAuth.value = value;
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
                        <DialogTitle class="flex">Create Monitor</DialogTitle>
                        <DialogDescription>
                            Create a new monitor to keep track of your website
                            uptime.
                        </DialogDescription>
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
                            <div>
                                <div class="flex items-center space-x-2 mt-4">
                                    <Switch
                                        id="show-auth"
                                        :value="showAuth"
                                        @update:checked="handleAuthSwitch"
                                    />
                                    <Label for="show-auth"
                                        >use authentication</Label
                                    >
                                </div>
                            </div>
                            <Accordion
                                v-show="showAuth"
                                type="single"
                                class="w-full col-span-2"
                                collapsible
                                v-model="form.auth"
                            >
                                <AccordionItem value="basic_auth">
                                    <AccordionTrigger
                                        >Basic Auth
                                    </AccordionTrigger>
                                    <AccordionContent
                                        class="grid gap-4 grid-cols-2"
                                    >
                                        <div>
                                            <Label
                                                for="monitor-basic-auth-username"
                                                >Username</Label
                                            >
                                            <Input
                                                id="monitor-basic-auth-username"
                                                v-model="form.auth_username"
                                                :error="
                                                    form.errors.auth_username
                                                "
                                            />
                                            <InputError
                                                :message="
                                                    form.errors.auth_username
                                                "
                                            />
                                        </div>
                                        <div>
                                            <Label
                                                for="monitor-basic-auth-password"
                                                >Password</Label
                                            >
                                            <Input
                                                id="monitor-basic-auth-password"
                                                type="password"
                                                v-model="form.auth_password"
                                                :error="
                                                    form.errors.auth_password
                                                "
                                            />
                                            <InputError
                                                :message="
                                                    form.errors.auth_password
                                                "
                                            />
                                        </div>
                                    </AccordionContent>
                                </AccordionItem>
                                <AccordionItem value="digest_auth">
                                    <AccordionTrigger
                                        >Digest Auth
                                    </AccordionTrigger>
                                    <AccordionContent
                                        class="grid gap-4 grid-cols-2"
                                    >
                                        <div>
                                            <Label
                                                for="monitor-digest-auth-username"
                                                >Username</Label
                                            >
                                            <Input
                                                id="monitor-digest-auth-username"
                                                v-model="form.auth_username"
                                                :error="
                                                    form.errors.auth_username
                                                "
                                            />
                                            <InputError
                                                :message="
                                                    form.errors.auth_username
                                                "
                                            />
                                        </div>
                                        <div>
                                            <Label
                                                for="monitor-digest-auth-password"
                                                >Password</Label
                                            >
                                            <Input
                                                id="monitor-digest-auth-password"
                                                type="password"
                                                v-model="form.auth_password"
                                                :error="
                                                    form.errors.auth_password
                                                "
                                            />
                                            <InputError
                                                :message="
                                                    form.errors.auth_password
                                                "
                                            />
                                        </div>
                                    </AccordionContent>
                                </AccordionItem>
                                <AccordionItem value="bearer_token">
                                    <AccordionTrigger
                                        >Bearer Token
                                    </AccordionTrigger>
                                    <AccordionContent
                                        class="grid gap-4 grid-cols-1"
                                    >
                                        <div>
                                            <Label for="monitor-auth-token"
                                                >Token</Label
                                            >
                                            <Input
                                                id="monitor-auth-token"
                                                v-model="form.auth_token"
                                                :error="form.errors.auth_token"
                                            />
                                            <InputError
                                                :message="
                                                    form.errors.auth_token
                                                "
                                            />
                                        </div>
                                    </AccordionContent>
                                </AccordionItem>
                            </Accordion>
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
                            :disabled="!form.url || isChecking"
                            @click="testConnection"
                        >
                            Check Connection
                            <Loader2
                                v-if="isChecking"
                                class="h-5 w-5 ml-1 animate-spin"
                            />
                            <PlugZap v-else class="h-5 w-5 ml-1" />
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

            <Label v-if="monitors.data.length > 0" class="mb-4"
                >Https / Http</Label
            >
            <div class="pb-12 space-y-6">
                <div
                    v-if="monitors.data.length === 0"
                    class="flex items-center justify-center h-20"
                >
                    <MonitorOff class="h-6 w-6 text-foreground mr-2" />
                    <span class="text-foreground">No monitors yet.</span>
                </div>

                <DashboardChart
                    v-for="monitor in monitors.data"
                    :monitor="monitor"
                    :check_labels="check_labels"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
