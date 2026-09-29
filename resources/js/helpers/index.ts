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
