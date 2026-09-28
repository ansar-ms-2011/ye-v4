export type User = {
    id: number | null;
    full_name: string | null;
    email: string;
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
    rotary_club_id: number | null;
    club: { club_name: string } | null;
    role_id: number | null;
    roles: { id: number; name: string }[];
    role: { id: number; name: string };
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

export interface UserFormModel {
    id: number | null;
    full_name: string;
    email: string;
    district: number | null;
    rotary_club_id: number | null;
    role_id: number | null;
    password: string;
    password_confirmation: string;
    active: boolean | null;
}
