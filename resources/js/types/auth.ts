export type User = {
    id: number | null;
    full_name: string | null;
    email: string | null;
    avatar?: string | null;
    email_verified_at: string | null;
    remember_token: string | null;
    two_factor_secret: string | null;
    two_factor_recovery_codes: string | null;
    two_factor_confirmed_at: string | null;
    created_at: string | null;
    updated_at: string | null;
    district: string | null;
    application_id: number | null;
    active: boolean | null;
    [key: string]: unknown;
};

export type Auth = {
    user: User;
};

export type TwoFactorConfigContent = {
    title: string;
    description: string;
    buttonText: string;
};
