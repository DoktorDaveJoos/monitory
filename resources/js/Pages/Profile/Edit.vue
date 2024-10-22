<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import { Card } from '@/Components/ui/card';
import { Settings } from 'lucide-vue-next';
import LayoutHeader from '@/Components/LayoutHeader.vue';
import UpdateNotificationChannels from '@/Pages/Profile/Partials/UpdateNotificationChannels.vue';
import { User } from '@/types';

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
    user: User;
}>();
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <LayoutHeader :icon="Settings" label="Settings" />
        </template>

        <div class="pb-12 space-y-6 max-w-7xl mx-auto w-full">
            <Card class="p-4 sm:p-8">
                <UpdateNotificationChannels :user="user" class="max-w-7xl" />
            </Card>

            <Card class="p-4 sm:p-8">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="max-w-7xl"
                />
            </Card>

            <Card class="p-4 sm:p-8">
                <UpdatePasswordForm class="max-7xl-xl" />
            </Card>

            <Card class="p-4 sm:p-8">
                <DeleteUserForm class="max-7w-xl" />
            </Card>
        </div>
    </AuthenticatedLayout>
</template>
