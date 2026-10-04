import type { Cyeo, Dyeo, Club } from '@/types/misc';

export function getNewCyeo(): Cyeo {
    return {
        application_no: '',
        created_at: '',
        cyeo_email: '',
        cyeo_fax: '',
        id: null,
        updated_at: '',
        user_id: null,
        cyeo_name: '',
        cyeo_sig: '',
        cyeo_address: '',
        cyeo_country: '',
        cyeo_city: '',
        cyeo_state: '',
        cyeo_postcode: '',
        cyeo_htel: '',
        cyeo_wtel: '',
        cyeo_mobile: '',
        ribi_club_id: null,
    };
}

export function getNewDyeo(): Dyeo{
    return {
        id: null,
        district_code: '',
        dyeo_name: '',
        dyeo_email: '',
        dyeo_contact_no: '',
        dyeo_address: '',
        dyeo_city: '',
        dyeo_state: '',
        dyeo_postcode: '',
        dyeo_country: '',
        dyeo_htel: '',
        dyeo_wtel: '',
        dyeo_mobile: '',
        dyeo_fax: '',
        user_id: null,
        created_at: '',
        updated_at: '',
    };
}

export function getNewClub(): Club{
    return {
        id: null,
        club_name: '',
        club_president: '',
        club_president_email: '',
        club_president_mobile: '',
        club_president_sig: '',
        club_other_name: '',
        club_other_sig: '',
        district_code: '',
        district_id: null,
        created_at: null,
        updated_at: null,
    }
}


export function getEmptyAddress(type = 'HOME') {
    return {
        id: null,
        address_type: type,
        application_id: '',
        application_no: 9999,
        street: '',
        city: '',
        county: '',
        country: '',
        postcode: '',
        created_at: '',
        updated_at: '',
    };
}

export function getEmptyLanguage() {
    return {
        id: null,
        application_id: '',
        application_no: 9999,
        language: null,
        years_studied: null,
        speaking: null,
        reading: null,
        writing: null,
        created_at: null,
        updated_at: null,
        remove: false,
    };
}

export function getEmptySibling() {
    return {
        id: null,
        application_id: '',
        full_name: '',
        age: '',
        occupation: '',
        gender: null,
        living_at_home: null,
        remove: false,
    };
}

export function getEmptyApplication() {
    return {
        id: null,
        emergency_contact: '',
        parent_div_sep: '',
        dob: '',
        gender: '',
        email_address: '',
        school: '',
        why_rotary: '',
        consider_alt_country: '',
        how_did_you_hear: '',
        firstname: '',
        surname: '',
        address: '',
        city: '',
        county: '',
        postcode: '',
        contact_no: '',
        alt_contact_no: '',
        parent_1: '',
        parent_2: '',
        application_no: '',
        other_info: '',
        exchange_type: '',
        pref_name: '',
        parent_support: '',
        dyeo_id: '',
        realm: '',
        application_status: '',
        application_status_note: '',
        dyeo_assigned: '',
        application_fee: '',
        application_fee_paid: '',
        parent1_rotarian: '',
        parent2_rotarian: '',
        parent1_rotary_club: '',
        parent2_rotary_club: '',
        parent1_email: '',
        parent2_email: '',
        parent1_tel: '',
        parent2_tel: '',
        parent1_mobile: '',
        parent2_mobile: '',
        parent1_btel: '',
        parent2_btel: '',
        parent1_occupation: '',
        parent2_occupation: '',
        religion: '',
        religion_detail: '',
        diet_restriction: '',
        smoke: '',
        smoke_why: '',
        drink: '',
        drink_why: '',
        illegal_drugs: '',
        illegal_drugs_why: '',
        native_language: '',
        dietary_restriction: '',
        medical_condition: '',
        treated_condition: '',
        prescribed_meds: '',
        special_req: '',
        medical_info: '',
        rotary_club_id: '',
        free_activities: '',
        attainment_vocation: '',
        special_interests: '',
        special_skills: '',
        contrib_entertainment: '',
        reason_for_camp: '',
        personal_remarks: '',
        place_of_birth: '',
        em_name: '',
        em_relationship: '',
        em_htel: '',
        em_mobile: '',
        em_email: '',
        citizen_of: '',
        country_citizenship: '',
        image_location: '',
        image_data: '',
        date_of_app: '',
        media_id: '',
        media_library: [],
        address_home: getEmptyAddress('HOME'),
        address_emergency: getEmptyAddress('EMERGENCY'),
        address_postal: getEmptyAddress('POSTAL'),
        address_parent1: getEmptyAddress('PARENT1'),
        address_parent2: getEmptyAddress('PARENT2'),
        languages: [],
        siblings: [],
        picture_changed: false,
    };
}

export function validateEmail(email : any) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}
