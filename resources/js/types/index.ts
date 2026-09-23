import { Auth } from './auth';

export * from './auth';

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: Auth;
    [key: string]: unknown;
};
