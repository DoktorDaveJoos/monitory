<script>
import {defineComponent} from "vue";
import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

export default defineComponent({
    components: {AuthenticatedLayout, Head}
})
</script>

<template>
    <Head title="Monitor"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Monitor</h2>
        </template>

    </AuthenticatedLayout>
</template>
