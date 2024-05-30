<script setup lang="ts">
import Chart from 'chart.js/auto';
import {
    tryOnMounted,
    useDateFormat,
    useResizeObserver,
    watchDebounced,
} from '@vueuse/core';
import { Check, ResourceCollection } from '@/types';
import { cn, fillMissingChecks, isMultipleOfTenMinutes } from '@/utils';
import { ref } from 'vue';

export interface ChartOptions {
    fading?: boolean;
    size?: 'small' | 'large';
    show_x_ticks?: boolean;
    show_grid?: boolean;
}

const props = withDefaults(
    defineProps<{
        checks: ResourceCollection<Check> | Array<Check>;
        check_labels: Array<string>;
        options?: ChartOptions;
    }>(),
    {
        options() {
            return {
                fading: false,
                show_x_ticks: true,
                show_grid: true,
            };
        },
    },
);

const checks = fillMissingChecks(
    Array.isArray(props.checks) ? props.checks : props.checks.data,
    props.check_labels,
);

Chart.defaults.font.family = 'DM Mono';
Chart.defaults.backgroundColor = 'rgba(59, 237, 195, 1)';
Chart.defaults.borderColor = 'rgba(232, 227, 253, .05)';
Chart.defaults.color = 'rgba(232, 227, 253, .5)';

const chartParent = ref(null);
const chartParentWidth = ref(0);

useResizeObserver(chartParent, (entries) => {
    const entry = entries[0];
    const { width } = entry.contentRect;

    chartParentWidth.value = width;
});

watchDebounced(
    chartParentWidth,
    () => {
        if (myChart.value) {
            myChart.value.resize();
        }
    },
    { debounce: 100 },
);

const getRed = (opacity: number, fading: boolean) =>
    fading ? `rgba(255, 107, 107, ${opacity})` : 'rgba(255, 107, 107, 1)';
const getGreen = (opacity: number, fading: boolean) =>
    fading ? `rgba(59, 237, 195, ${opacity})` : `rgba(59, 237, 195, 1)`;

const myChart = ref<Chart<'bar', number[], string> | null>(null);

// Make this robust by checking if the element exists
tryOnMounted(() => {
    const ctx: any = document.getElementById('myChart');

    if (!ctx && !(ctx instanceof HTMLElement)) {
        throw new Error('Could not find canvas element');
    }

    myChart.value = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: props.check_labels,
            datasets: [
                {
                    label: 'Response Time',
                    data: checks.map((check) => check.response_time),
                    borderWidth: 1,
                    backgroundColor: checks.map((check, index: number) =>
                        check.success
                            ? getGreen(
                                  (checks.length + index) / 100,
                                  props.options.fading ?? false,
                              )
                            : getRed(
                                  (checks.length + index) / 100,
                                  props.options.fading ?? false,
                              ),
                    ),
                    barThickness: 7,
                },
            ],
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    beginAtZero: false,
                    // offset: false,
                    grid: {
                        display: false,
                        offset: false,
                    },
                    border: {
                        display: false,
                    },
                    ticks: {
                        callback: function (_, index) {
                            if (!props.options.show_x_ticks) return null;

                            const value = props.check_labels[index];

                            if (isMultipleOfTenMinutes(value)) {
                                return useDateFormat(value, 'HH:mm A').value;
                            }

                            return null;
                        },
                    },
                },
                y: {
                    beginAtZero: false,
                    ticks: {
                        display: false,
                        stepSize: 250,
                    },
                    grid: {
                        display: props.options.show_grid,
                    },
                    border: {
                        display: false,
                    },
                },
            },
            elements: {
                bar: {
                    borderRadius: 10,
                    borderSkipped: false,
                },
            },
            plugins: {
                legend: {
                    display: false,
                },
            },
        },
    });
});
</script>

<template>
    <div ref="chartParent" class="relative">
        <canvas
            :class="
                cn(
                    options.size === 'large' && 'max-h-32',
                    options.size === 'small' && 'max-h-20',
                )
            "
            id="myChart"
        ></canvas>
    </div>
</template>
