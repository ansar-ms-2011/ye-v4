export interface PageProps {
    auth?: {
        role?: string;
    };
    [key: string]: unknown;
}

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

export interface Application {
    id: number | null;
    application_no: string | null;
    idapplication: string | null;
    media_id: number | null;
    emergency_contact: number | null;
    parent_div_sep: number | null;
    dob: string | null;
    gender: string | null;
    email_address: string | null;
    school: string | null;
    why_rotary: string | null;
    consider_alt_country: number | null;
    how_did_you_hear: string | null;
    firstname: string | null;
    surname: string | null;
    address: string | null;
    city: string | null;
    county: string | null;
    postcode: string | null;
    contact_no: string | null;
    alt_contact_no: string | null;
    parent_1: string | null;
    parent_2: string | null;
    citizen_of: string | null;
    other_info: string | null;
    exchange_type: string | null;
    pref_name: string | null;
    parent_support: string | null;
    dyeo_id: number | null;
    realm: string | null;
    application_status: string | null;
    application_status_note: string | null;
    dyeo_assigned: number | null;
    application_fee: string | number | null;
    application_fee_paid: number | null;
    parent1_rotarian: string | null;
    parent2_rotarian: string | null;
    parent1_rotary_club: string | null;
    parent2_rotary_club: string | null;
    parent1_email: string | null;
    parent2_email: string | null;
    parent1_tel: string | null;
    parent2_tel: string | null;
    parent1_mobile: string | null;
    parent2_mobile: string | null;
    parent1_btel: string | null;
    parent2_btel: string | null;
    parent1_occupation: string | null;
    parent2_occupation: string | null;
    religion: string | null;
    religion_detail: string | null;
    diet_restriction: string | null;
    smoke: number | null;
    smoke_why: string | null;
    drink: number | null;
    drink_why: string | null;
    illegal_drugs: number | null;
    illegal_drugs_why: string | null;
    native_language: string | null;
    medical_condition: number | null;
    dietary_restriction: number | null;
    treated_condition: number | null;
    prescribed_meds: number | null;
    special_req: number | null;
    medical_info: string | null;
    rotary_club_id: number | null;
    free_activities: string | null;
    attainment_vocation: string | null;
    special_interests: string | null;
    special_skills: string | null;
    contrib_entertainment: string | null;
    reason_for_camp: string | null;
    personal_remarks: string | null;
    place_of_birth: string | null;
    em_name: string | null;
    em_relationship: string | null;
    em_htel: string | null;
    em_mobile: string | null;
    em_email: string | null;
    country_citizenship: string | null;
    image_location: string | null;
    date_of_app: string | null;
    created_at: string | null;
    updated_at: string | null;
    [key: string]: any;
}
