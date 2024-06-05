<script setup lang="ts">
import { cn } from '@/utils';
import { ArrowDownCircleIcon } from '@heroicons/vue/20/solid';

const props = defineProps<{
    label: string;
    value: string | number;
    trend: string;
}>();

function checkPercentage() {
    // Remove the percentage sign and convert to a number
    const number = parseFloat(props.trend);

    // Check if the number is positive, negative, or zero
    if (isNaN(number)) {
        throw new Error('Invalid percentage value');
    } else if (number > 0) {
        return 'positive';
    } else if (number < 0) {
        console.log('is negative');
        return 'negative';
    } else {
        return 'neutral';
    }
}
</script>

<template>
    <div
        class="flex flex-col space-y-0.5 justify-between px-4 py-4 font-mono overflow-hidden"
    >
        <div
            class="w-full text-center overflow-hidden text-primary-foreground/50 truncate"
        >
            <span class="font-light">{{ label }}</span>
        </div>

        <div class="w-full text-center">
            <span class="text-xl font-medium">{{ value }}</span>
        </div>

        <div class="w-full text-center">
            <div
                class="font-medium flex justify-center items-center"
                :class="
                    cn(
                        'text-primary-foreground/50',
                        checkPercentage() === 'positive' && 'text-success',
                        checkPercentage() === 'negative' && 'text-destructive',
                    )
                "
            >
                <ArrowDownCircleIcon
                    :class="
                        cn(
                            'w-3 h-3 shrink-0 mr-0.5',
                            checkPercentage() === 'positive' &&
                                'transform rotate-180',
                            checkPercentage() === 'neutral' &&
                                'transform -rotate-90',
                        )
                    "
                />
                {{ Math.abs(parseFloat(trend)) }}%
            </div>
        </div>
    </div>
</template>

<style scoped></style>
