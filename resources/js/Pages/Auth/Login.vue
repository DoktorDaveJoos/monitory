<script setup lang="ts">
import { Checkbox } from '@/Components/ui/checkbox';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Input } from '@/Components/ui/input';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Label } from '@/Components/ui/label';
import { Card } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <Card class="py-4 px-6 w-full max-w-md">
            <form @submit.prevent="submit">
                <div>
                    <Label for="email">Email</Label>

                    <Input
                        id="email"
                        type="email"
                        v-model="form.email"
                        class="mt-1"
                        required
                        autofocus
                        autocomplete="username"
                    />

                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-4">
                    <Label for="password">Password</Label>

                    <Input
                        id="password"
                        type="password"
                        class="mt-1"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                    />

                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="flex items-center space-x-2 mt-4">
                    <Checkbox id="terms" v-model:checked="form.remember" />
                    <label
                        for="terms"
                        class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                    >
                        Remember me
                    </label>
                </div>

                <div class="flex items-center justify-end mt-4">
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="underline text-sm text-foreground/80 dark:text-gray-400 hover:text-foreground dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                    >
                        Forgot your password?
                    </Link>

                    <Button class="ms-4" :disabled="form.processing">
                        Log in
                    </Button>
                </div>
            </form>
        </Card>
    </GuestLayout>
</template>
