<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';

const handle = () => {
    console.log('Primary Button Clicked');
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <Card class="max-w-6xl mx-auto">
                <CardHeader>
                    <CardTitle>Card Title</CardTitle>
                    <CardDescription>Card Description</CardDescription>
                </CardHeader>
                <CardContent>
                    Some Card Content

                    <div class="flex space-x-4">
                        <div
                            class="grid w-full max-w-sm items-center gap-1.5 my-6"
                        >
                            <Label for="email">Email</Label>
                            <Input
                                id="email"
                                type="email"
                                placeholder="Email"
                            />
                        </div>
                        <div
                            class="grid w-full max-w-sm items-center gap-1.5 my-6"
                        >
                            <Label for="type">Type</Label>
                            <Select id="type">
                                <SelectTrigger class="w-[180px]">
                                    <SelectValue placeholder="Select a fruit" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Fruits</SelectLabel>
                                        <SelectItem value="apple">
                                            Apple
                                        </SelectItem>
                                        <SelectItem value="banana">
                                            Banana
                                        </SelectItem>
                                        <SelectItem value="blueberry">
                                            Blueberry
                                        </SelectItem>
                                        <SelectItem value="grapes">
                                            Grapes
                                        </SelectItem>
                                        <SelectItem value="pineapple">
                                            Pineapple
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>
                </CardContent>
                <CardFooter class="space-x-4">
                    <Button @click="handle">Primary</Button>
                    <Button variant="secondary">Secondary</Button>
                    <Button variant="destructive">Destructive</Button>
                    <Button variant="outline">Outline</Button>
                    <Button variant="ghost">Ghost</Button>
                    <Button variant="link">Link</Button>
                </CardFooter>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>
