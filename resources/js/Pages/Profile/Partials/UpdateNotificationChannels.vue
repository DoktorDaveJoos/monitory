<script setup lang="ts">
import { router, useForm, usePage } from '@inertiajs/vue3';
import { Label } from '@/Components/ui/label';
import { Button } from '@/Components/ui/button';
import { Checkbox } from '@/Components/ui/checkbox';
import { CircleAlertIcon } from 'lucide-vue-next';
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
import InputError from '@/Components/InputError.vue';
import { ref } from 'vue';
import { User } from '@/types';

const props = defineProps<{
    user: User;
}>();

const form = useForm<{
    settings: {
        notifications: {
            email: boolean;
            slack: boolean;
            sms: boolean;
        };
    };
}>({
    settings: props.user.data.settings?.notifications
        ? { ...props.user.data.settings }
        : {
              notifications: {
                  email: true,
                  slack: false,
                  sms: false,
              },
          },
});

const channelForm = useForm({
    channel: props.user.data.slack_connection?.channel ?? '',
});

const updateSlackChannelModal = ref(false);
const deleteSlackConnectionModal = ref(false);

const updateSlackChannel = () => {
    channelForm.patch(route('profile.slack-channel'), {
        preserveScroll: true,
        onSuccess: () => {
            updateSlackChannelModal.value = false;
        },
    });
};

const destroySlackConnection = () => {
    channelForm.delete(route('profile.slack-connection.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            deleteSlackConnectionModal.value = false;
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium">Notification Channels</h2>

            <p class="mt-1 text-sm">
                Update your account's notification preferences.
            </p>
        </header>

        <form
            @submit.prevent="form.patch(route('profile.notification-settings'))"
            class="mt-6 space-y-6"
        >
            <div class="space-y-4">
                <div class="items-top flex gap-x-2">
                    <Checkbox
                        id="notification.email"
                        :checked="form.settings?.notifications?.email ?? true"
                        @update:checked="
                            (value) =>
                                (form.settings.notifications.email = value)
                        "
                    />
                    <div class="grid gap-1.5 leading-none">
                        <label
                            for="notification.email"
                            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                        >
                            Email
                        </label>
                        <p class="text-sm text-muted-foreground">
                            Enabled by default. You can unsubscribe at any time.
                        </p>
                    </div>
                </div>

                <div class="items-top flex gap-x-2">
                    <Checkbox
                        id="notification.slack"
                        :checked="form.settings?.notifications?.slack"
                        @update:checked="
                            (value) =>
                                (form.settings.notifications.slack = value)
                        "
                    />
                    <div class="grid gap-1.5 leading-none">
                        <label
                            for="notification.slack"
                            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                        >
                            Slack
                            <span
                                v-if="
                                    form.settings.notifications.slack &&
                                    !user.data.slack_connection
                                "
                                class="ml-1 text-red-500"
                            >
                                <CircleAlertIcon
                                    class="w-4 h-4 mr-1 -my-2 inline-block"
                                />
                                No Slack Account connected
                            </span>
                            <span
                                v-else-if="
                                    form.settings.notifications.slack &&
                                    !user.data.slack_connection?.channel
                                "
                                class="ml-1 -my-2 text-yellow-500"
                            >
                                <CircleAlertIcon
                                    class="w-4 h-4 mr-1 inline-block"
                                />
                                Missing Slack Channel
                            </span>
                            <span
                                class="text-muted-foreground ml-1"
                                v-else-if="form.settings.notifications.slack"
                            >
                                # {{ user.data.slack_connection.channel }}
                            </span>
                        </label>
                        <p
                            v-if="
                                !user.data.slack_connection &&
                                !user.data.slack_connection?.channel
                            "
                            class="text-sm text-muted-foreground"
                        >
                            To enable Slack notifications, you must
                            <a
                                v-if="!user.data.slack_connection"
                                :href="route('oauth.slack.redirect')"
                                class="text-primary hover:text-primary/80"
                            >
                                connect your account to Slack.
                            </a>
                            <Dialog
                                v-else-if="!user.data.slack_connection?.channel"
                                v-model:open="updateSlackChannelModal"
                                :default-open="false"
                            >
                                <DialogTrigger as-child>
                                    <span
                                        class="cursor-pointer text-primary hover:text-primary/80"
                                    >
                                        add a Slack channel.
                                    </span>
                                </DialogTrigger>
                                <DialogContent class="sm:max-w-[425px]">
                                    <DialogHeader>
                                        <DialogTitle
                                            >Add a Slack Channel
                                        </DialogTitle>
                                        <DialogDescription>
                                            The Slack channel you want to
                                            receive notifications in.
                                        </DialogDescription>
                                    </DialogHeader>
                                    <div class="grid gap-4 py-4">
                                        <div class="items-center gap-4">
                                            <Label
                                                for="slack-channel"
                                                class="text-right"
                                            >
                                                Slack Channel
                                            </Label>
                                            <div
                                                class="relative w-full max-w-sm items-center"
                                            >
                                                <Input
                                                    id="slack-channel"
                                                    v-model="
                                                        channelForm.channel
                                                    "
                                                    class="col-span-3 pl-8"
                                                />
                                                <span
                                                    class="absolute font-semibold text-accent-foreground start-1 inset-y-0 flex items-center justify-center px-2"
                                                >
                                                    #
                                                </span>
                                            </div>
                                            <InputError
                                                :message="
                                                    channelForm.errors.channel
                                                "
                                                class="mt-2"
                                            />
                                        </div>
                                    </div>
                                    <DialogFooter>
                                        <Button
                                            variant="default"
                                            :disabled="channelForm.processing"
                                            @click="updateSlackChannel"
                                        >
                                            Save
                                        </Button>
                                    </DialogFooter>
                                </DialogContent>
                            </Dialog>
                        </p>
                        <p v-else class="text-sm text-muted-foreground">
                            You can
                            <Dialog
                                v-model:open="updateSlackChannelModal"
                                :default-open="false"
                            >
                                <DialogTrigger as-child>
                                    <span
                                        class="cursor-pointer text-primary hover:text-primary/80"
                                    >
                                        change the Slack channel
                                    </span>
                                </DialogTrigger>
                                <DialogContent class="sm:max-w-[425px]">
                                    <DialogHeader>
                                        <DialogTitle
                                            >Add a Slack Channel
                                        </DialogTitle>
                                        <DialogDescription>
                                            The Slack channel you want to
                                            receive notifications in.
                                        </DialogDescription>
                                    </DialogHeader>
                                    <div class="grid gap-4 py-4">
                                        <div class="items-center gap-4">
                                            <Label
                                                for="slack-channel"
                                                class="text-right"
                                            >
                                                Slack Channel
                                            </Label>
                                            <div
                                                class="relative w-full max-w-sm items-center"
                                            >
                                                <Input
                                                    id="slack-channel"
                                                    v-model="
                                                        channelForm.channel
                                                    "
                                                    class="col-span-3 pl-8"
                                                />
                                                <span
                                                    class="absolute font-semibold text-accent-foreground start-1 inset-y-0 flex items-center justify-center px-2"
                                                >
                                                    #
                                                </span>
                                            </div>
                                            <InputError
                                                :message="
                                                    channelForm.errors.channel
                                                "
                                                class="mt-2"
                                            />
                                        </div>
                                    </div>
                                    <DialogFooter>
                                        <Button
                                            variant="default"
                                            :disabled="channelForm.processing"
                                            @click="updateSlackChannel"
                                        >
                                            Save
                                        </Button>
                                    </DialogFooter>
                                </DialogContent>
                            </Dialog>
                            at any time. Or
                            <Dialog
                                v-model:open="deleteSlackConnectionModal"
                                :default-open="false"
                            >
                                <DialogTrigger as-child>
                                    <span
                                        class="cursor-pointer text-yellow-700 hover:text-yellow-800"
                                    >
                                        disconnect your Slack Account.
                                    </span>
                                </DialogTrigger>
                                <DialogContent class="sm:max-w-[425px]">
                                    <DialogHeader>
                                        <DialogTitle
                                            >Disconnect Slack Account
                                        </DialogTitle>
                                        <DialogDescription>
                                            Are you sure you want to disconnect
                                            your Slack account?
                                        </DialogDescription>
                                    </DialogHeader>
                                    <DialogFooter>
                                        <Button
                                            variant="destructive"
                                            :disabled="channelForm.processing"
                                            @click="destroySlackConnection"
                                        >
                                            Disconnect
                                        </Button>
                                    </DialogFooter>
                                </DialogContent>
                            </Dialog>
                        </p>
                    </div>
                </div>

                <!--                <div class="items-top flex gap-x-2">-->
                <!--                    <Checkbox id="terms1" />-->
                <!--                    <div class="grid gap-1.5 leading-none">-->
                <!--                        <label-->
                <!--                            for="terms1"-->
                <!--                            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"-->
                <!--                        >-->
                <!--                            SMS-->
                <!--                        </label>-->
                <!--                        <p class="text-sm text-muted-foreground">-->
                <!--                            Not yet supported.-->
                <!--                        </p>-->
                <!--                    </div>-->
                <!--                </div>-->
            </div>

            <div class="flex items-center gap-4 mt-4">
                <Button :disabled="form.processing">Save</Button>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-foreground/80"
                    >
                        Saved.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
