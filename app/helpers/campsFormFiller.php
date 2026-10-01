<?php

use Carbon\Carbon;
use Intervention\Image\Facades\Image;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\Filter\FilterException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use setasign\Fpdi\PdfReader\PdfReaderException;

// Set Memory limit to be used by PHP
ini_set('memory_limit', '250M');

/**
 * @throws PdfParserException
 */
function prepare_template($application): Fpdi
{
    // $templateCampsV2 = storage_path('app/pdf-templates/camps-v2.pdf');
    $templateCampsV2 = storage_path('app/pdf-templates/v13-camps-260613.pdf');
    $pdf = new Fpdi;
    $iconv_name = iconv('UTF-8', 'ISO-8859-1', $application->full_name);
    $pdf->SetTitle("Application No: $application->application_no, $iconv_name");
    $pdf->setSourceFile($templateCampsV2);
    $pdf->SetFont('Arial', 'B', 7);

    return $pdf;
}

/**
 * @throws PdfTypeException
 * @throws PdfReaderException
 * @throws CrossReferenceException
 * @throws PdfParserException
 * @throws FilterException
 */
function get_CAMPS_Pdf_v2($application): Fpdi
{
    $pdf = prepare_template($application);
    $image = null;
    $image_name = 'app/app-'.$application->id.'.png';
    $iconv_name = iconv('UTF-8', 'ISO-8859-1', $application->full_name);

    SaveApplicationMediaFiles($application);

    // ---Page 1---------------------------------------------------
    $pdf->addPage();
    $templateId = $pdf->importPage(1);
    $pdf->useTemplate($templateId);

    if ($application->dyeo) {
        $pdf->SetFont('Arial', 'B', 15);
        $pdf->SetXY(112, 63);
        $pdf->Write(0, $application->dyeo->district_code);
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->SetXY(112, 74);
        $pdf->Write(0, $application->dyeo->dyeo_name);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetXY(112, 80);
        $pdf->MultiCell(80, 6, $application->dyeo->dyeo_email, 0, 'L');
    }

    // ---Page 2 -------------------------------------------------
    $pdf->addPage();
    $templateId = $pdf->importPage(2);
    $pdf->useTemplate($templateId);

    if ($application->dyeo) {
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetXY(155, 12);
        $pdf->Write(0, $application->dyeo->district_code);
    }

    $photo_self_path = storage_path('app/media-library/'.$application->id.'/Photo-Self.png');
    if (file_exists($photo_self_path)) {
        $pdf->Image($photo_self_path, 152, 22, 40, 48);
        // Draw a border (rectangle) around the image
        $pdf->Rect(151, 21, 42, 50, 'D');
    }

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetXY(17, 80);
    $pdf->Write(0, $iconv_name);

    $pdf->SetXY(138, 80);
    $pdf->Write(0, $application->pref_name != '' ? $application->pref_name : $application->firstname);

    if ($application->gender == 'Female') {
        $pdf->Image(storage_path('app/box-crossed.png'), 177.5, 73.5, 3.5, 3.5);
    } else {
        $pdf->Image(storage_path('app/box-crossed.png'), 177.5, 77.5, 3.5, 3.5);
    }

    $pdf->SetFont('Arial', '', 10);
    $pdf->SetXY(17, 92);
    $dob = Carbon::parse($application->dob);
    $pdf->Write(0, $dob->format('Y-m-d'));

    $pdf->SetXY(73, 92);
    $pdf->Write(0, $application->country_citizenship);

    $pdf->SetXY(123, 92);
    $pdf->Write(0, $application->place_of_birth);

    // Fetch all addressed data and fill in
    $add_home = $application->address_home;

    // Fill-in Home Address
    if ($application->address_home) {
        $pdf->SetXY(17, 102);
        $pdf->Write(0, $add_home->street);
        $pdf->SetXY(93, 102);
        $pdf->Write(0, $add_home->city);
        $pdf->SetXY(136, 99);
        $pdf->MultiCell(25, 3, $add_home->postcode, 0, 'L');
        $pdf->SetXY(155.5, 99);
        $pdf->MultiCell(24, 3, $add_home->county, 0, 'L');
        $pdf->SetXY(174, 99);
        $pdf->MultiCell(20, 3, $add_home->country, 0, 'L');
    }

    $pdf->SetXY(17, 112);
    $pdf->Write(0, $application->email_address);
    $pdf->SetXY(108, 112);
    $pdf->Write(0, $application->contact_no);
    $pdf->SetXY(158, 112);
    $pdf->Write(0, $application->alt_contact_no);

    // Parent 1 Name
    $pdf->SetXY(20, 132);
    $address_parent1_name = iconv('UTF-8', 'ISO-8859-1', $application->parent_1);
    $pdf->Write(0, $address_parent1_name);
    // Parent 2 Name
    $pdf->SetXY(110, 132);
    $address_parent2_name = iconv('UTF-8', 'ISO-8859-1', $application->parent_2);
    $pdf->Write(0, $address_parent2_name);
    // Parent 1 Email
    $pdf->SetXY(20, 142);
    $pdf->Write(0, $application->parent1_email);

    // Parent 2 Email
    $pdf->SetXY(110, 142);
    $pdf->Write(0, $application->parent2_email);

    // Parent 1 Phone
    $pdf = printTelNos($pdf, 20, 150, $application->parent1_tel, $application->parent1_mobile, $application->parent1_btel);

    // Parent 1 Occupation
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetXY(65, 152);
    $pdf->Write(0, $application->parent1_occupation);

    // Parent 2 Phone
    $pdf = printTelNos($pdf, 110, 150, $application->parent2_tel, $application->parent2_mobile, $application->parent2_btel);

    // Parent 2 Occupation
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetXY(155, 152);
    $pdf->Write(0, $application->parent2_occupation);

    if ($application->parent1_rotarian == 'Yes') {
        $pdf->Image(storage_path('app/box-crossed-3.png'), 18.5, 159, 6, 6);

        $pdf->SetXY(65, 163);
        $pdf->Write(0, $application->parent1_rotary_club);
    } else {
        $pdf->Image(storage_path('app/box-crossed-3.png'), 35, 159, 6, 6);
    }

    if ($application->parent2_rotarian == 'Yes') {
        $pdf->Image(storage_path('app/box-crossed-3.png'), 106, 159, 6, 6);

        $pdf->SetXY(155, 163);
        $pdf->Write(0, $application->parent2_rotary_club);
    } else {
        $pdf->Image(storage_path('app/box-crossed-3.png'), 122, 159, 6, 6);
    }

    // Fill-in Parent 1 Address
    $address_parent1 = $application->address_parent1;
    if ($application->address_parent1) {
        $pdf->SetXY(20, 173);
        $pdf->Write(0, $address_parent1->street);
        $pdf->SetXY(95, 173);
        $pdf->Write(0, $address_parent1->city);
        $pdf->SetXY(140, 170);
        $pdf->MultiCell(20, 3, $address_parent1->county, 0, 'L');
        $pdf->SetXY(159, 170);
        $pdf->MultiCell(20, 3, $address_parent1->postcode, 0, 'L');
        $pdf->SetXY(179, 170);
        $pdf->MultiCell(20, 3, $address_parent1->country, 0, 'L');
    }

    // Emergency Contact Parent 1 or Parent 2
    $pdf->SetXY(155, 181);
    if (! $application->emergency_contact) {
        $pdf->Image(storage_path('app/box-crossed-3.png'), 136.5, 178, 6, 6);
    } else {
        $pdf->Image(storage_path('app/box-crossed-3.png'), 165, 178, 6, 6);
    }

    $address_emergency = $application->address_emergency;
    $pdf->SetXY(20, 200);
    $pdf->Write(0, $application->em_name);
    $pdf->SetXY(140, 200);
    $pdf->Write(0, $application->em_relationship);

    $pdf->SetXY(20, 209);
    $pdf->Write(0, $application->em_email);
    $pdf->SetXY(65, 209);
    $pdf->Write(0, $application->em_htel);
    $pdf->SetXY(155, 209);
    $pdf->Write(0, $application->em_mobile);

    $pdf->SetXY(20, 228);
    $pdf->Write(0, $application->religion);
    $pdf->SetXY(67, 228);
    $pdf->Write(0, $application->religion_detail);

    // Smoke
    if ($application->smoke) {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 21, 233.5, 5, 5);

        $pdf->SetXY(67, 236);
        $pdf->Write(0, $application->smoke_why);
    } else {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 36.5, 233.5, 5, 5);
    }
    // Drink
    if ($application->drink) {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 21, 241.5, 5, 5);

        $pdf->SetXY(67, 244);
        $pdf->Write(0, $application->drink_why);
    } else {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 36.5, 241.5, 5, 5);
    }

    if ($application->illegal_drugs) {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 21, 250.5, 5, 4);

        $pdf->SetXY(67, 252);
        $pdf->Write(0, $application->illegal_drugs_why);
    } else {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 36.5, 250.5, 5, 4);
    }

    // ---Page 3 -------------------------------------------------
    $pdf->addPage();
    $templateId = $pdf->importPage(3);
    $pdf->useTemplate($templateId);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetXY(136, 11);
    $pdf->Write(0, $iconv_name);
    if ($application->dyeo) {
        $pdf->SetXY(136, 17);
        $pdf->Write(0, $application->dyeo->district_code);
    }

    // Native Language
    $pdf->SetXY(17, 47);
    $pdf->Write(0, $application->native_language);
    // Non-Native Languages
    foreach ($application->languages as $index => $language) {
        if ($index > 2) {
            break;
        }
        $y = 57 + ($index * 6);
        $pdf->SetXY(17, $y);
        $pdf->Write(0, $language->language);
        $pdf->SetXY(85, $y);
        $pdf->Write(0, $language->years_studied);
        $pdf->SetXY(110, $y);
        $pdf->Write(0, $language->speaking);
        $pdf->SetXY(140, $y);
        $pdf->Write(0, $language->reading);
        $pdf->SetXY(172, $y);
        $pdf->Write(0, $language->writing);
    }

    if ($application->dietary_restriction) {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 158, 85, 5, 5);
    } else {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 181.5, 85, 5, 5);
    }

    if ($application->medical_condition) {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 158, 91, 5, 5);
    } else {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 181.5, 91, 5, 5);
    }

    if ($application->treated_condition) {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 158, 97, 5, 5);
    } else {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 181.5, 97, 5, 5);
    }

    if ($application->prescribed_meds) {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 158, 103, 5, 5);
    } else {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 181.5, 103, 5, 5);
    }

    if ($application->special_req) {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 158, 109, 5, 5);
    } else {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 181.5, 109, 5, 5);
    }

    // If any of above question is answered Yes then print medical info detail
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetXY(17, 126);
    $pdf->MultiCell(175, 4, $application->medical_info);

    $pdf->SetXY(17, 210);
    $pdf->Write(0, $application->club ? $application->club->district_code : '');
    $pdf->SetXY(77, 210);
    $pdf->Write(0, $application->club ? $application->club->club_name : '');
    $pdf->SetXY(161, 210);
    $pdf->Write(0, $application->rotary_club_id);

    $pdf->SetXY(17, 219);
    $pdf->Write(0, $application->dyeo ? $application->dyeo->dyeo_name : '');
    $pdf->SetXY(77, 219);
    $pdf->Write(0, $application->club ? $application->club->club_president : '');
    $pdf->SetXY(138, 219);
    $pdf->Write(0, $application->club && $application->club->cyeo ? $application->club->cyeo->cyeo_name : '');

    $pdf->SetXY(17, 228);
    $pdf->Write(0, $application->dyeo ? $application->dyeo->dyeo_email : '');
    $pdf->SetXY(77, 228);         // Newly added this field
    $pdf->Write(0, $application->club ? $application->club->club_president_email : '');
    $pdf->SetXY(138, 228);
    $pdf->Write(0, $application->club && $application->club->cyeo ? $application->club->cyeo->cyeo_email : '');

    $pdf->SetXY(17, 237);
    $pdf->Write(0, $application->dyeo ? $application->dyeo->dyeo_mobile : '');
    $pdf->SetXY(77, 237);          // Newly added this field
    $pdf->Write(0, $application->club ? $application->club->club_president_mobile : '');
    $pdf->SetXY(138, 237);
    $pdf->Write(0, $application->club && $application->club->cyeo ? $application->club->cyeo->cyeo_mobile : '');

    // ---Page 4 -------------------------------------------------
    $pdf->addPage();
    $templateId = $pdf->importPage(4);
    $pdf->useTemplate($templateId);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetXY(141, 15);
    $pdf->Write(0, $iconv_name);

    if ($application->dyeo) {
        $pdf->SetXY(141, 21);
        $pdf->Write(0, $application->dyeo->district_code);
    }

    // ---Page 5 -------------------------------------------------
    $pdf->addPage();
    $templateId = $pdf->importPage(5);
    $pdf->useTemplate($templateId);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetXY(141, 15);
    $pdf->Write(0, $iconv_name);

    if ($application->dyeo) {
        $pdf->SetXY(141, 21);
        $pdf->Write(0, $application->dyeo->district_code);
    }

    // ---Page 6 -------------------------------------------------
    $pdf->addPage();
    $templateId = $pdf->importPage(6);
    $pdf->useTemplate($templateId);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetXY(141, 15);
    $pdf->Write(0, $iconv_name);

    if ($application->dyeo) {
        $pdf->SetXY(141, 21);
        $pdf->Write(0, $application->dyeo->district_code);
    }

    // ---Page 7 -------------------------------------------------
    $pdf->addPage();
    $templateId = $pdf->importPage(7);
    $pdf->useTemplate($templateId);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetXY(141, 15);
    $pdf->Write(0, $iconv_name);

    if ($application->dyeo) {
        $pdf->SetXY(141, 21);
        $pdf->Write(0, $application->dyeo->district_code);
    }

    $pdf->SetFont('Arial', 'B', 9);
    //    Free Activities
    $h = 4;
    $left = 23;
    $pdf->SetXY($left, 70);
    $pdf->MultiCell(172, $h, $application->free_activities);

    // Vocation
    $pdf->SetXY($left, 102);
    $pdf->MultiCell(172, $h, $application->attainment_vocation);

    // Special Interests
    $pdf->SetXY($left, 135);
    $pdf->MultiCell(172, $h, $application->special_interests);

    //    //Special Skills
    //    $pdf->SetXY(20, 166);
    //    $pdf->MultiCell(150, $h, $application->special_skills);

    // Contribute to Entertainment
    $pdf->SetXY($left, 167);
    $pdf->MultiCell(172, $h, $application->contrib_entertainment);

    // Reason for Camp
    $pdf->SetXY($left, 201);
    $pdf->MultiCell(172, $h, $application->reason_for_camp);

    // Personal Remarks
    $pdf->SetXY($left, 233);
    $pdf->MultiCell(172, $h, $application->personal_remarks);

    // ---Page 8 -------------------------------------------------
    $pdf->addPage();
    $templateId = $pdf->importPage(8);
    $pdf->useTemplate($templateId);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetXY(141, 15);
    $pdf->Write(0, $iconv_name);

    if ($application->dyeo) {
        $pdf->SetXY(141, 21);
        $pdf->Write(0, $application->dyeo->district_code);
    }

    $pdf->SetFont('Arial', '', 12);
    $pdf->SetXY(20, 50);
    $pdf->Write(0, $iconv_name);

    $pdf->SetXY(141, 50);
    $pdf->Write(0, $application->pref_name != '' ? $application->pref_name : $application->firstname);

    if ($application->gender == 'Female') {
        $pdf->Image(storage_path('app/box-crossed.png'), 181, 43, 3, 3);
    } else {
        $pdf->Image(storage_path('app/box-crossed.png'), 181, 47, 3, 3);
    }

    $pdf->SetFont('Arial', '', 10);
    $pdf->SetXY(20, 60);
    $pdf->Write(0, $application->place_of_birth);

    $pdf->SetXY(110, 60);
    $pdf->Write(0, $application->country_citizenship);

    $pdf->SetXY(155, 60.5);
    $dob = Carbon::parse($application->dob);
    $pdf->Write(0, $dob->format('Y-m-d'));

    // Fetch all addressed data and fill in
    //    $add_home = $application->address_home;
    //    $add_postal = $application->address_postal;
    //    $address_parent2 = $application->address_parent2;

    // Fill in Home Address
    if ($application->address_home) {
        $pdf->SetXY(19, 70);
        $pdf->Write(0, $add_home->street);
        $pdf->SetXY(95, 70);
        $pdf->Write(0, $add_home->city);
        $pdf->SetXY(140, 67);
        $pdf->MultiCell(23, 3, $add_home->county, 0, 'L');
        $pdf->SetXY(160, 67);
        $pdf->MultiCell(24, 3, $add_home->postcode, 0, 'L');
        $pdf->SetXY(179, 67);
        $pdf->MultiCell(20, 3, $add_home->country, 0, 'L');
    }

    $pdf->SetXY(19, 80);
    $pdf->Write(0, $application->email_address);
    $pdf->SetXY(110, 80);
    $pdf->Write(0, $application->contact_no);
    $pdf->SetXY(156, 80);
    $pdf->Write(0, $application->alt_contact_no);

    // ---Page 9 -------------------------------------------------
    $pdf->addPage();
    $templateId = $pdf->importPage(9);
    $pdf->useTemplate($templateId);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetXY(141, 15);
    $pdf->Write(0, $iconv_name);

    if ($application->dyeo) {
        $pdf->SetXY(141, 21);
        $pdf->Write(0, $application->dyeo->district_code);
    }

    $photo_self_path = storage_path('app/media-library/'.$application->id.'/Passport-Scan.png');
    if (file_exists($photo_self_path)) {
        $pdf->Image($photo_self_path, 30, 42, 160, 0);
    }

    // ---------------
    return $pdf;
}

function get_CAMPS_Signing_Page($application): Fpdi
{
    $pdf = prepare_template($application);

    $image_name = 'app/app-'.$application->id.'.png';
    $iconv_name = iconv('UTF-8', 'ISO-8859-1', $application->full_name);

    if ($application->media_id && $application->image_data) {
        $image = Image::make($application->image_data);
        $image->save(storage_path($image_name));
    }

    // View Signing Pages ---------------------------------------
    // ---Page 3 -------------------------------------------------
    $pdf->addPage();
    $templateId = $pdf->importPage(3);
    $pdf->useTemplate($templateId);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetXY(136, 11);
    $pdf->Write(0, $iconv_name);
    if ($application->dyeo) {
        $pdf->SetXY(136, 17);
        $pdf->Write(0, $application->dyeo->district_code);
    }

    // Native Language
    $pdf->SetXY(20, 47);
    $pdf->Write(0, $application->native_language);
    // Non-Native Languages
    foreach ($application->languages as $index => $language) {
        if ($index > 2) {
            break;
        }
        $y = 57 + ($index * 6);
        $pdf->SetXY(17, $y);
        $pdf->Write(0, $language->language);
        $pdf->SetXY(85, $y);
        $pdf->Write(0, $language->years_studied);
        $pdf->SetXY(110, $y);
        $pdf->Write(0, $language->speaking);
        $pdf->SetXY(140, $y);
        $pdf->Write(0, $language->reading);
        $pdf->SetXY(172, $y);
        $pdf->Write(0, $language->writing);
    }

    if ($application->dietary_restriction) {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 158, 85, 5, 5);
    } else {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 181, 85, 5, 5);
    }

    if ($application->medical_condition) {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 158, 91, 5, 5);
    } else {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 181, 91, 5, 5);
    }

    if ($application->treated_condition) {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 158, 97, 5, 5);
    } else {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 181, 97, 5, 5);
    }

    if ($application->prescribed_meds) {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 158, 103, 5, 5);
    } else {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 181, 103, 5, 5);
    }

    if ($application->special_req) {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 158, 109, 5, 5);
    } else {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 181, 109.5, 5, 5);
    }

    // If any of above question is answered Yes then print medical info detail
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetXY(20, 126);
    $pdf->MultiCell(175, 4, $application->medical_info);

    $pdf->SetXY(20, 209);
    $pdf->Write(0, $application->club ? $application->club->district_code : '');
    $pdf->SetXY(79, 209);
    $pdf->Write(0, $application->club ? $application->club->club_name : '');
    $pdf->SetXY(163, 209);
    $pdf->Write(0, $application->rotary_club_id);

    $pdf->SetXY(17, 219);
    $pdf->Write(0, $application->dyeo ? $application->dyeo->dyeo_name : '');
    $pdf->SetXY(79, 219);
    $pdf->Write(0, $application->club ? $application->club->club_president : '');
    $pdf->SetXY(140, 219);
    $pdf->Write(0, $application->club && $application->club->cyeo ? $application->club->cyeo->cyeo_name : '');

    $pdf->SetXY(17, 228);
    $pdf->Write(0, $application->dyeo ? $application->dyeo->dyeo_email : '');
    $pdf->SetXY(79, 228);         // Newly added this field
    $pdf->Write(0, $application->club ? $application->club->club_president_email : '');
    $pdf->SetXY(140, 228);
    $pdf->Write(0, $application->club && $application->club->cyeo ? $application->club->cyeo->cyeo_email : '');

    $pdf->SetXY(17, 237);
    $pdf->Write(0, $application->dyeo ? $application->dyeo->dyeo_mobile : '');
    $pdf->SetXY(79, 237);          // Newly added this field
    $pdf->Write(0, $application->club ? $application->club->club_president_mobile : '');
    $pdf->SetXY(140, 237);
    $pdf->Write(0, $application->club && $application->club->cyeo ? $application->club->cyeo->cyeo_mobile : '');

    return $pdf;
}

// ------------Signing Page 5-6 CAMPS ---------------------------------------------------------------------------------
/**
 * @throws PdfTypeException
 * @throws CrossReferenceException
 * @throws PdfReaderException
 * @throws PdfParserException
 * @throws FilterException
 */
function get_CAMPS_Signing_Page_5_6($application): Fpdi
{
    $pdf = prepare_template($application);

    $image_name = 'app/app-'.$application->id.'.png';
    $iconv_name = iconv('UTF-8', 'ISO-8859-1', $application->full_name);

    if ($application->media_id && $application->image_data) {
        $image = Image::make($application->image_data);
        $image->save(storage_path($image_name));
    }

    // ---Prepare Signing Pages ----------------------------------
    // ---Page 5 -------------------------------------------------
    $pdf->addPage();
    $templateId = $pdf->importPage(5);
    $pdf->useTemplate($templateId);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetXY(141, 15);
    $pdf->Write(0, $iconv_name);

    if ($application->dyeo) {
        $pdf->SetXY(141, 21);
        $pdf->Write(0, $application->dyeo->district_code);
    }

    // ---Page 6 -------------------------------------------------
    $pdf->addPage();
    $templateId = $pdf->importPage(6);
    $pdf->useTemplate($templateId);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetXY(141, 15);
    $pdf->Write(0, $iconv_name);

    if ($application->dyeo) {
        $pdf->SetXY(141, 21);
        $pdf->Write(0, $application->dyeo->district_code);
    }

    return $pdf;
}
