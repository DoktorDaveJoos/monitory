<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import { Card } from '@/Components/ui/card';
import { Settings } from 'lucide-vue-next';
import LayoutHeader from '@/Components/LayoutHeader.vue';

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
}>();
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <LayoutHeader :icon="Settings" label="Settings" />
        </template>

        <div class="pb-12 space-y-6">
            <Card class="p-4 sm:p-8">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="max-w-xl"
                />
            </Card>

            <Card class="p-4 sm:p-8">
                <UpdatePasswordForm class="max-w-xl" />
            </Card>

            <Card class="p-4 sm:p-8">
                <DeleteUserForm class="max-w-xl" />
            </Card>
        </div>
    </AuthenticatedLayout>
</template>
