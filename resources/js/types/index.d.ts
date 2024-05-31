export interface ResourceCollection<T> {
    data: Array<T>;
}

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string;
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

export interface Stats {
    total_checks: number;
    total_notifications: number;
    total_monitors: number;
    uptime_overall: string;
    average_response_time: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    app: {
        version: string;
    };
    monitor_list: ResourceCollection<MonitorListItem>;
};
