<script setup lang="ts">
import Chart from 'chart.js/auto';

import { tryOnMounted, useDateFormat } from '@vueuse/core';
import { Check, ResourceCollection } from '@/types';
import { fillMissingChecks, isMultipleOfTenMinutes } from '@/utils';

export interface ChartOptions {
    fading?: boolean;
    show_x_ticks?: boolean;
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

const getRed = (opacity: number) => `rgba(255, 107, 107, ${opacity})`;
const getGreen = (opacity: number) => `rgba(59, 237, 195, ${opacity})`;

// Make this robust by checking if the element exists
tryOnMounted(() => {
    const ctx: any = document.getElementById('myChart');

    if (!ctx && !(ctx instanceof HTMLElement)) {
        throw new Error('Could not find canvas element');
    }

    new Chart(ctx, {
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
                            ? getGreen((checks.length + index) / 100)
                            : getRed((checks.length + index) / 100),
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
                    ticks: {
                        callback: function (tickValue, index) {
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
                },
            },
            elements: {
                bar: {
                    backgroundColor: '#3BEDC3',
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
    <canvas class="max-h-20 inset-0" id="myChart"></canvas>
</template>
