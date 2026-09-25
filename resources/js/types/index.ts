import { Auth } from './auth';

export * from './auth';

export interface FlashMessages {
    success?: string | null;
    error?: string | null;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: Auth;
    flash?: FlashMessages;
    [key: string]: unknown;
};

