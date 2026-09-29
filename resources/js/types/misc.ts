export type SelectOption = {
    value: string | number;
    text: string | null;
};

export type Cyeo = {
    id: number | null;
    cyeo_name: string;
    cyeo_sig: string;
    cyeo_address: string;
    cyeo_city: string;
    cyeo_state: string;
    cyeo_postcode: string;
    cyeo_country: string;
    cyeo_htel: string;
    cyeo_wtel: string;
    cyeo_mobile: string;
    cyeo_fax: string;
    cyeo_email: string;
    application_no: string;
    ribi_club_id: string | null;
    user_id: string | null;
    created_at: string;
    updated_at: string;
    club?: Club | null;
    district?: District | null;
    [key: string]: unknown;
};

export type Dyeo = {
    id: number | null;
    district_code: string;
    dyeo_name: string;
    dyeo_email: string;
    dyeo_contact_no: string;
    dyeo_address: string;
    dyeo_city: string;
    dyeo_state: string;
    dyeo_postcode: string;
    dyeo_country: string;
    dyeo_htel: string;
    dyeo_wtel: string;
    dyeo_mobile: string;
    dyeo_fax: string;
    user_id: string | null;
    created_at: string | null;
    updated_at: string | null;
};

export type Club = {
    id: number | null;
    club_name: string;
    club_president: string;
    club_president_email: string;
    club_president_mobile: string;
    club_president_sig: string;
    club_other_name: string;
    club_other_sig: string;
    district_code: string;
    district_id: string | null;
    created_at: string | null;
    updated_at: string | null;
};

export type District = {
    id: number | null;
    code: string | null;
}

export interface ClubFormModel {
    id: number | null;
    club_name: string | null;
    club_president: string | null;
    club_president_email: string | null;
    club_president_mobile: string | null;
    club_president_sig: string | null;
    club_other_name: string | null;
    club_other_sig: string | null;
    district_code: string | null;
    district_id: string | number | null;
    [key: string]: unknown;
}
