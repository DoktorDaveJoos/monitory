import { type ClassValue, clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';
import { Check } from '@/types';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export const isMultipleOfTenMinutes = (dateString: string): boolean => {
    const date = new Date(dateString);
    if (isNaN(date.getTime())) {
        throw new Error('Invalid date string');
    }
    const minutes = date.getMinutes();
    return minutes % 10 === 0;
};

export const fillMissingChecks = (
    checks: Check[],
    labels: string[],
): Check[] => {
    const filledChecks: Check[] = [];
    for (let i = 0; i < labels.length; i++) {
        const label = labels[i];
        const check = checks.find((c) => c.started_at === label);

        filledChecks[i] = <Check>check || {
            id: -1,
            success: false,
            response_time: 0,
            status_code: 0,
            created_at: '',
            started_at: label,
        };
    }
    return filledChecks;
};

export async function isUrlReachable(url: string) {
    try {
        const response = await fetch(url, { method: 'HEAD', mode: 'no-cors' });
        // If we get a response, even if it's 4xx or 5xx, consider it reachable
        return true;
    } catch (error) {
        // If there's an error, such as a network error or DNS resolution failure, consider it unreachable
        return false;
    }
}
