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

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    app: {
        version: string;
    };
    monitor_list: {
        data: Array<MonitorListItem>;
    };
};
