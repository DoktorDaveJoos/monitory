<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { Monitor } from '@/types';
import { Card } from '@/Components/ui/card';
import { Chart } from '@/Components/ui/chart';
import { BellOff, Edit, PlugZap } from 'lucide-vue-next';
// import {
//     Tooltip,
//     TooltipContent,
//     TooltipTrigger,
// } from '@/Components/ui/tooltip';
import { ref, watch } from 'vue';
import { useToast } from '@/Components/ui/toast';

const props = defineProps<{
    monitor: Monitor;
    check_labels: Array<string>;
}>();

const confirmingMonitorDeletion = ref(false);
const nameConfirmation = ref('');

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
</script>

<template>
    <Card class="py-2 grid grid-cols-12">
        <div
            class="flex items-center space-x-4 px-4 col-span-2 border-r-2 border-accent/80 my-2"
        >
            <div
                class="h-4 w-4 shrink-0 rounded-full flex items-center justify-center"
                :class="monitor.status ? 'bg-success/40' : 'bg-destructive/40'"
            >
                <div
                    class="h-2 w-2 rounded-full"
                    :class="monitor.status ? 'bg-success' : 'bg-destructive'"
                />
            </div>
            <div class="flex flex-col overflow-hidden">
                <span class="truncate font-bold">{{ monitor.name }}</span>
                <span class="text-sm font-mono text-foreground/50">{{
                    monitor.uptime
                }}</span>
            </div>
        </div>

        <Chart
            v-if="monitor.checks.length > 0"
            class="col-span-9"
            :checks="monitor.checks"
            :check_labels="check_labels"
            :options="{
                show_x_ticks: false,
                fading: false,
                size: 'small',
                show_grid: false,
            }"
        />

        <div v-else class="col-span-9 flex items-center justify-center h-20">
            <PlugZap class="h-6 w-6 text-foreground mr-2" />
            <span class="text-foreground"
                >Monitor just created. Waiting for first check.</span
            >
        </div>

        <div
            class="flex justify-center space-x-4 col-span-1 border-l-2 border-accent/80 my-2 items-center"
        >
            <Link :href="route('monitor.show', monitor.id)">
                <Edit
                    class="h-4 w-4 hover:text-primary transition-colors duration-150"
                />
            </Link>
            <!--            <Tooltip content="Mute notifications">-->
            <!--                <TooltipTrigger>-->
            <BellOff class="h-4 w-4 text-foreground/50 cursor-not-allowed" />
            <!--                </TooltipTrigger>-->
            <!--                <TooltipContent>-->
            <!--                    <p>-->
            <!--                        Mute notifications for this monitor.-->
            <!--                        <br />You will still receive notifications for other-->
            <!--                        monitors.-->
            <!--                    </p>-->
            <!--                    <p>Only available on the <strong>SaaS plan</strong>.</p>-->
            <!--                </TooltipContent>-->
            <!--            </Tooltip>-->

            <!--            <Dialog-->
            <!--                v-model:open="confirmingMonitorDeletion"-->
            <!--                :default-open="false"-->
            <!--            >-->
            <!--                <DialogTrigger as-child>-->
            <!--                    <button>-->
            <!--                        <Trash2-->
            <!--                            class="h-4 w-4 text-destructive hover:text-destructive/80 transition-colors duration-150"-->
            <!--                        />-->
            <!--                    </button>-->
            <!--                </DialogTrigger>-->
            <!--                <DialogContent class="sm:max-w-[425px]">-->
            <!--                    <DialogHeader>-->
            <!--                        <DialogTitle-->
            <!--                            >Are you sure you want to delete your monitor?-->
            <!--                        </DialogTitle>-->
            <!--                        <DialogDescription>-->
            <!--                            Once your monitor is deleted, all of its resources-->
            <!--                            and data will be permanently deleted. Please enter-->
            <!--                            your password to confirm you would like to-->
            <!--                            permanently delete your monitor.-->
            <!--                        </DialogDescription>-->
            <!--                    </DialogHeader>-->
            <!--                    <div class="grid gap-4 py-4">-->
            <!--                        <div class="items-center gap-4">-->
            <!--                            <Label class="text-right"> Name </Label>-->
            <!--                            <div-->
            <!--                                class="font-mono px-4 mb-4 py-2 rounded-lg bg-foreground text-background"-->
            <!--                            >-->
            <!--                                {{ monitor.name }}-->
            <!--                            </div>-->
            <!--                            <Label-->
            <!--                                :for="`name-confirmation-${monitor.id}`"-->
            <!--                                class="text-right"-->
            <!--                            >-->
            <!--                                Repeat the monitor name to confirm-->
            <!--                            </Label>-->
            <!--                            <Input-->
            <!--                                :id="`name-confirmation-${monitor.id}`"-->
            <!--                                v-model="nameConfirmation"-->
            <!--                                class="col-span-3"-->
            <!--                            />-->
            <!--                            <InputError message="" class="mt-2" />-->
            <!--                        </div>-->
            <!--                    </div>-->
            <!--                    <DialogFooter>-->
            <!--                        <Button-->
            <!--                            variant="destructive"-->
            <!--                            :disabled="nameConfirmation !== monitor.name"-->
            <!--                            @click="deleteMonitor"-->
            <!--                        >-->
            <!--                            Delete Monitor-->
            <!--                        </Button>-->
            <!--                    </DialogFooter>-->
            <!--                </DialogContent>-->
            <!--            </Dialog>-->
        </div>
    </Card>
</template>
