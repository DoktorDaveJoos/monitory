export interface ResourceCollection<T> {
    data: Array<T>;
}

export interface ResourceItem<T> {
    data: T;
}

export interface Operator {
    trigger: string;
    value: Array<OptionEnum>;
}

export interface OperatorsCollection {
    data: Array<Operator>;
}

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string;
    created_at: string;
    updated_at: string;
    settings: {
        notifications: {
            mail: boolean;
            slack: boolean;
            sms: boolean;
        };
    };
    slack_connection: {
        channel: string;
    };
}

export interface MonitorListItem {
    id: number;
    name: string;
    type: string;
    status: boolean | null;
}

export interface Monitor {
    id: number;
    name: string;
    type: string;
    url: string;
    host: string;
    interval: number;
    active: boolean;
    method: string;
    uptime: string;
    status: boolean;
    last_checked_at: string;
    checks: Array<Check>;
}

export interface Check {
    id: number;
    success: boolean;
    response_time: number;
    status_code: number;
    response_headers: string;
    response_body: string;
    created_at: string;
    started_at: string;
}

export interface Trigger {
    id: number | string;
    type: string;
    operator: string;
    value: string;
}

export interface Stats {
    total_checks: number;
    total_notifications: number;
    total_monitors: number;
    uptime_overall: string;
    average_response_time: string;
}

export interface MonitorStat {
    absolute: number;
    percentage: string;
}

export interface MonitorStats {
    '2xx': MonitorStat;
    '4xx': MonitorStat;
    '5xx': MonitorStat;
    timeouts: MonitorStat;
    latency: {
        overall: number;
        last_hour: string;
    };
}

export interface OptionEnum {
    value: string;
    label: string;
    unit: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: ResourceItem<User>;
    };
    app: {
        version: string;
    };
    monitor_list: ResourceCollection<MonitorListItem>;
};
