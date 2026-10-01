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
function prepare_STEP_template($application, $prefix = ''): Fpdi
{
    // $templateCampsV2 = storage_path('app/pdf-templates/step-v2.pdf');
    $templateCampsV2 = storage_path('app/pdf-templates/v13-step-260613.pdf');
    $pdf = new Fpdi;
    $iconv_name = iconv('UTF-8', 'ISO-8859-1', $application->full_name);
    $pdf->SetTitle($prefix."Application No: $application->application_no, $iconv_name");
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
function get_STEP_Pdf_v2($application): Fpdi
{
    $pdf = prepare_STEP_template($application);
    $image = null;
    $image_name = 'app/app-'.$application->id.'.png';
    $iconv_name = iconv('UTF-8', 'ISO-8859-1', $application->full_name);

    if ($application->media_id && $application->image_data) {
        $image = Image::make($application->image_data);
        $image->save(storage_path($image_name));
    }
    // Fetch and store uploaded media files to storage for further use in template
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
        $pdf->SetXY(112, 73);
        $pdf->Write(0, $application->dyeo->dyeo_name);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(112, 80);
        $pdf->MultiCell(80, 4, $application->dyeo->dyeo_email, 0, 'L');
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
    $pdf->SetXY(17, 90);
    $pdf->Write(0, $iconv_name);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetXY(137, 85);
    $pdf->MultiCell(33, 3, ($application->pref_name != '' ? $application->pref_name : $application->firstname), 0, 'L');

    if ($application->gender == 'Female') {
        $pdf->Image(storage_path('app/box-crossed.png'), 177.5, 83, 3.5, 3.5);
    } else {
        $pdf->Image(storage_path('app/box-crossed.png'), 177.5, 86.5, 3.5, 3.5);
    }

    $pdf->SetFont('Arial', '', 10);
    $pdf->SetXY(17, 102);
    $dob = Carbon::parse($application->dob);
    $pdf->Write(0, $dob->format('Y-m-d'));

    $pdf->SetXY(72, 102);
    //    $pdf->Write(0, $application->citizen_of); this field is not being used while saving data.
    $pdf->Write(0, $application->country_citizenship);

    $pdf->SetXY(122, 102);
    $pdf->Write(0, $application->place_of_birth);

    // Fetch all addressed data and fill in
    $add_home = $application->address_home;

    $y = 108;
    // Fill-in Home Address
    if ($application->address_home) {
        $pdf->SetXY(17, $y);
        $pdf->MultiCell(70, 3, $add_home->street, 0, 'L');
        $pdf->SetXY(92, $y);
        $pdf->MultiCell(40, 3, $add_home->city, 0, 'L');
        $pdf->SetXY(136, $y);
        $pdf->MultiCell(20, 3, $add_home->postcode, 0, 'L');
        $pdf->SetXY(155, $y);
        $pdf->MultiCell(20, 3, $add_home->county, 0, 'L');
        $pdf->SetXY(174, $y);
        $pdf->MultiCell(20, 3, $add_home->country, 0, 'L');
    }

    $pdf->SetXY(17, 122);
    $pdf->Write(0, $application->email_address);
    $pdf->SetXY(107, 122);
    $pdf->Write(0, $application->contact_no);
    $pdf->SetXY(152, 122);
    $pdf->Write(0, $application->alt_contact_no);

    // Parent 1 Name
    $pdf->SetXY(17, 142);
    $address_parent1_name = iconv('UTF-8', 'ISO-8859-1', $application->parent_1);
    $pdf->Write(0, $address_parent1_name);
    // Parent 2 Name
    $pdf->SetXY(107, 142);
    $address_parent2_name = iconv('UTF-8', 'ISO-8859-1', $application->parent_2);
    $pdf->Write(0, $address_parent2_name);

    // Parent 1 Email
    $pdf->SetXY(17, 152);
    $pdf->Write(0, $application->parent1_email);
    // Parent 2 Email
    $pdf->SetXY(107, 152);
    $pdf->Write(0, $application->parent2_email);

    // Parent 1 Phone
    $pdf->SetXY(17, 162);
    $pdf->Write(0, $application->parent1_tel);
    // Parent 1 Occupation
    $pdf->SetXY(62, 162);
    $pdf->Write(0, $application->parent1_occupation);

    // Parent 2 Phone
    $pdf->SetXY(107, 162);
    $pdf->Write(0, $application->parent2_tel);
    // Parent 2 Occupation
    $pdf->SetXY(152, 162);
    $pdf->Write(0, $application->parent2_occupation);

    if ($application->parent1_rotarian == 'Yes') {
        $pdf->Image(storage_path('app/box-crossed-3.png'), 19, 168, 5, 6);

        $pdf->SetXY(65, 172);
        $pdf->Write(0, $application->parent1_rotary_club);
    } else {
        $pdf->Image(storage_path('app/box-crossed-3.png'), 35, 168, 5, 6);
    }

    if ($application->parent2_rotarian == 'Yes') {
        $pdf->Image(storage_path('app/box-crossed-3.png'), 106, 168, 5, 6);

        $pdf->SetXY(155, 172);
        $pdf->Write(0, $application->parent2_rotary_club);
    } else {
        $pdf->Image(storage_path('app/box-crossed-3.png'), 123, 168, 5, 6);
    }

    // Fill in Parent 1 Address
    $y = 180;
    $address_parent1 = $application->address_parent1;
    if ($application->address_parent1) {
        $pdf->SetXY(17, $y);
        $pdf->MultiCell(70, 3, $address_parent1->street, 0, 'L');
        $pdf->SetXY(92, $y);
        $pdf->MultiCell(40, 3, $address_parent1->city, 0, 'L');
        $pdf->SetXY(136, $y);
        $pdf->MultiCell(20, 3, $address_parent1->county, 0, 'L');
        $pdf->SetXY(155, $y);
        $pdf->MultiCell(20, 3, $address_parent1->postcode, 0, 'L');
        $pdf->SetXY(174, $y);
        $pdf->MultiCell(20, 3, $address_parent1->country, 0, 'L');
    }

    // Emergency Contact Parent 1 or Parent 2
    $pdf->SetXY(155, 190);
    if (! $application->emergency_contact) {
        $pdf->Image(storage_path('app/box-crossed.png'), 136, 188.5, 5, 5);
    } else {
        $pdf->Image(storage_path('app/box-crossed.png'), 167, 188.5, 5, 5);
    }

    $address_emergency = $application->address_emergency;
    $pdf->SetXY(17, 209);
    $pdf->Write(0, $application->em_name);
    $pdf->SetXY(137, 209);
    $pdf->Write(0, $application->em_relationship);

    $pdf->SetXY(17, 216);
    $pdf->MultiCell(43, 3, $application->em_email, 0, 'L');
    $pdf->SetXY(62, 218);
    $pdf->Write(0, $application->em_htel);
    $pdf->SetXY(152, 218);
    $pdf->Write(0, $application->em_mobile);
    //    $pdf->Cell();
    $sibling_y_axis = 242;
    foreach ($application->siblings as $sibling) {
        $pdf->SetXY(17, $sibling_y_axis);
        $pdf->Write(0, $sibling->full_name);
        if ($sibling->gender == 'Female') {
            $pdf->Image(storage_path('app/box-crossed-3.png'), 70, $sibling_y_axis - 3, 5, 5);
        } else {
            $pdf->Image(storage_path('app/box-crossed-3.png'), 80, $sibling_y_axis - 3, 5, 5);
        }
        $pdf->SetXY(105, $sibling_y_axis);
        $pdf->Write(0, $sibling->age);
        $pdf->SetXY(120, $sibling_y_axis);
        $pdf->Write(0, $sibling->occupation);
        if ($sibling->living_at_home == 'Yes') {
            $pdf->Image(storage_path('app/box-crossed-3.png'), 166, $sibling_y_axis - 3, 5, 5);
        } else {
            $pdf->Image(storage_path('app/box-crossed-3.png'), 180, $sibling_y_axis - 3, 5, 5);
        }
        $sibling_y_axis += 7;
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

    $pdf->SetFont('Arial', '', 10);
    $pdf->SetXY(17, 40);
    $pdf->Write(0, $application->religion);
    $pdf->SetXY(64, 40);
    $pdf->Write(0, $application->religion_detail);

    // Smoke
    if ($application->smoke) {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 20, 46, 5, 5);

        $pdf->SetXY(65, 48);
        $pdf->Write(0, $application->smoke_why);
    } else {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 35, 46, 5, 5);
    }
    // Drink
    if ($application->drink) {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 20, 54, 5, 5);

        $pdf->SetXY(65, 56);
        $pdf->Write(0, $application->drink_why);
    } else {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 35, 54, 5, 5);
    }

    if ($application->illegal_drugs) {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 20, 63, 5, 4);

        $pdf->SetXY(65, 65);
        $pdf->Write(0, $application->illegal_drugs_why);
    } else {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 35, 63, 5, 4);
    }

    // Native Language
    $pdf->SetXY(17, 90);
    $pdf->Write(0, $application->native_language);
    // Non-Native Languages
    foreach ($application->languages as $index => $language) {
        if ($index > 2) {
            break;
        }
        $y = 100 + ($index * 6);
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

    $y = 124;
    $x_yes = 156;
    $x_no = 180;
    if ($application->dietary_restriction) {
        $pdf->Image(storage_path('app/box-crossed-2.png'), $x_yes, $y, 5, 5);
    } else {
        $pdf->Image(storage_path('app/box-crossed-2.png'), $x_no, $y, 5, 5);
    }
    $y += 6;
    if ($application->medical_condition) {
        $pdf->Image(storage_path('app/box-crossed-2.png'), $x_yes, $y, 5, 5);
    } else {
        $pdf->Image(storage_path('app/box-crossed-2.png'), $x_no, $y, 5, 5);
    }
    $y += 6;
    if ($application->treated_condition) {
        $pdf->Image(storage_path('app/box-crossed-2.png'), $x_yes, $y, 5, 5);
    } else {
        $pdf->Image(storage_path('app/box-crossed-2.png'), $x_no, $y, 5, 5);
    }
    $y += 6;
    if ($application->prescribed_meds) {
        $pdf->Image(storage_path('app/box-crossed-2.png'), $x_yes, $y, 5, 5);
    } else {
        $pdf->Image(storage_path('app/box-crossed-2.png'), $x_no, $y, 5, 5);
    }
    $y += 6;
    if ($application->special_req) {
        $pdf->Image(storage_path('app/box-crossed-2.png'), $x_yes, $y, 5, 5);
    } else {
        $pdf->Image(storage_path('app/box-crossed-2.png'), $x_no, $y, 5, 5);
    }

    // If any of above question is answered Yes then print medical info detail
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(16, 164);
    $pdf->MultiCell(175, 3, $application->medical_info);

    $pdf->SetFont('Arial', '', 10);
    $y = 215;
    $pdf->SetXY(17, $y);
    $pdf->Write(0, $application->club ? $application->club->district_code : '');
    $pdf->SetXY(76, $y);
    $pdf->Write(0, $application->club ? $application->club->club_name : '');
    $pdf->SetXY(160, $y);
    $pdf->Write(0, $application->rotary_club_id);
    $y = 224;
    $pdf->SetXY(17, $y);
    $pdf->Write(0, $application->dyeo ? $application->dyeo->dyeo_name : '');
    $pdf->SetXY(76, $y);
    $pdf->Write(0, $application->club ? $application->club->club_president : '');
    $pdf->SetXY(137, $y);
    $pdf->Write(0, $application->club && $application->club->cyeo ? $application->club->cyeo->cyeo_name : '');
    $y = 233;
    $pdf->SetXY(17, $y);
    $pdf->Write(0, $application->dyeo ? $application->dyeo->dyeo_email : '');
    $pdf->SetXY(76, $y);         // Newly added this field
    $pdf->Write(0, $application->club ? $application->club->club_president_email : '');
    $pdf->SetXY(137, $y);
    $pdf->Write(0, $application->club && $application->club->cyeo ? $application->club->cyeo->cyeo_email : '');
    $y = 242;
    $pdf->SetXY(17, $y);
    $pdf->Write(0, $application->dyeo ? $application->dyeo->dyeo_mobile : '');
    $pdf->SetXY(76, $y);          // Newly added this field
    $pdf->Write(0, $application->club ? $application->club->club_president_mobile : '');
    $pdf->SetXY(136, $y);
    $pdf->Write(0, $application->club && $application->club->cyeo ? $application->club->cyeo->cyeo_mobile : '');

    // ---Page 4 to 6 -------------------------------------------------
    $pdf = addSimplePage($pdf, $application, $iconv_name, 4, 6);

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

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetXY(17, 65);
    $pdf->Write(0, $iconv_name);        // Applicant Name

    $pdf->SetXY(138, 63);
    $pdf->MultiCell(35, 4, ($application->pref_name != '' ? $application->pref_name : $application->firstname), 0, 'L');

    if ($application->gender == 'Female') {
        $pdf->Image(storage_path('app/box-crossed.png'), 178.5, 60, 3.5, 3.5);
    } else {
        $pdf->Image(storage_path('app/box-crossed.png'), 178.5, 64, 3.5, 3.5);
    }

    $pdf->SetFont('Arial', '', 12);
    $pdf->SetXY(17, 78);
    $pdf->Write(0, $application->place_of_birth);

    $pdf->SetXY(107, 78);
    $pdf->Write(0, $application->country_citizenship);

    $pdf->SetFont('Arial', '', 10);
    $pdf->SetXY(152, 78);
    $dob = Carbon::parse($application->dob);
    $pdf->Write(0, $dob->format('Y-m-d'));

    // Fetch all addressed data and fill in
    $add_home = $application->address_home;

    $y = 85;
    // Fill-in Home Address
    if ($application->address_home) {
        $pdf->SetXY(17, $y);
        $pdf->MultiCell(70, 3, $add_home->street, 0, 'L');
        $pdf->SetXY(92, $y);
        $pdf->MultiCell(40, 3, $add_home->city, 0, 'L');
        $pdf->SetXY(137, $y);
        $pdf->MultiCell(19, 3, $add_home->postcode, 0, 'L');
        $pdf->SetXY(157, $y);
        $pdf->MultiCell(20, 3, $add_home->county, 0, 'L');
        $pdf->SetXY(175, $y);
        $pdf->MultiCell(20, 3, $add_home->country, 0, 'L');
    }
    $y = 97;
    $pdf->SetXY(17, $y);
    $pdf->Write(0, $application->email_address);
    $pdf->SetXY(107, $y);
    $pdf->Write(0, $application->contact_no);
    $pdf->SetXY(152, $y);
    $pdf->Write(0, $application->alt_contact_no);

    // Page 8
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

    $pdf->SetFont('Arial', '', 9);

    $photo_self_path = storage_path('app/media-library/'.$application->id.'/Photo-Family.png');
    if (file_exists($photo_self_path)) {
        $pdf->Image($photo_self_path, 16, 73, 90, 87);
    }

    $media = $application->media_library()->where('media_category_label', 'Photo-Family')->first();
    if ($media) {
        $pdf->SetXY(16, 161);
        $pdf->MultiCell(90, 3, $media->brief_caption, 0, 'C');
    }

    $photo_self_path = storage_path('app/media-library/'.$application->id.'/Photo-Home.png');
    if (file_exists($photo_self_path)) {
        $pdf->Image($photo_self_path, 110, 73, 88, 87);
    }

    $media = $application->media_library()->where('media_category_label', 'Photo-Home')->first();
    if ($media) {
        $pdf->SetXY(110, 161);
        $pdf->MultiCell(90, 3, $media->brief_caption, 0, 'C');
    }

    $photo_self_path = storage_path('app/media-library/'.$application->id.'/Photo-Interest.png');
    if (file_exists($photo_self_path)) {
        $pdf->Image($photo_self_path, 16, 173, 90, 87);
    }

    $media = $application->media_library()->where('media_category_label', 'Photo-Interest')->first();
    if ($media) {
        $pdf->SetXY(16, 261);
        $pdf->MultiCell(90, 3, $media->brief_caption, 0, 'C');
    }

    $photo_self_path = storage_path('app/media-library/'.$application->id.'/Photo-Important.png');
    if (file_exists($photo_self_path)) {
        $pdf->Image($photo_self_path, 110, 173, 90, 87);
    }
    $media = $application->media_library()->where('media_category_label', 'Photo-Important')->first();
    if ($media) {
        $pdf->SetXY(110, 261);
        $pdf->MultiCell(90, 3, $media->brief_caption, 0, 'C');
    }

    // Page 9
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
        $pdf->Image($photo_self_path, 23, 90, 175, 0);
    }

    // Page 10
    $pdf->addPage();
    $templateId = $pdf->importPage(10);
    $pdf->useTemplate($templateId);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetXY(141, 15);
    $pdf->Write(0, $iconv_name);

    if ($application->dyeo) {
        $pdf->SetXY(141, 21);
        $pdf->Write(0, $application->dyeo->district_code);
    }

    // Page 11 Applicant-Letter Page 1
    $pdf->addPage();
    $templateId = $pdf->importPage(11);
    $pdf->useTemplate($templateId);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetXY(141, 15);
    $pdf->Write(0, $iconv_name);

    if ($application->dyeo) {
        $pdf->SetXY(141, 21);
        $pdf->Write(0, $application->dyeo->district_code);
    }

    $photo_self_path = storage_path('app/media-library/'.$application->id.'/Applicant-Letter-Page-1.png');
    if (file_exists($photo_self_path)) {
        $pdf->Image($photo_self_path, 23, 38, 176, 0);
    }

    // Page 12 Applicant-Letter Page 2
    $pdf->addPage();
    $templateId = $pdf->importPage(12);
    $pdf->useTemplate($templateId);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetXY(141, 15);
    $pdf->Write(0, $iconv_name);

    if ($application->dyeo) {
        $pdf->SetXY(141, 21);
        $pdf->Write(0, $application->dyeo->district_code);
    }

    $photo_self_path = storage_path('app/media-library/'.$application->id.'/Applicant-Letter-Page-2.png');
    if (file_exists($photo_self_path)) {
        $pdf->Image($photo_self_path, 23, 38, 176, 0);
    }

    // Page 13   Applicant-Letter Page 3
    $pdf->addPage();
    $templateId = $pdf->importPage(13);
    $pdf->useTemplate($templateId);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetXY(141, 15);
    $pdf->Write(0, $iconv_name);

    if ($application->dyeo) {
        $pdf->SetXY(141, 21);
        $pdf->Write(0, $application->dyeo->district_code);
    }

    $photo_self_path = storage_path('app/media-library/'.$application->id.'/Applicant-Letter-Page-3.png');
    if (file_exists($photo_self_path)) {
        $pdf->Image($photo_self_path, 23, 38, 176, 0);
    }

    // Page 14   Applicant-Letter Page 3
    $pdf->addPage();
    $templateId = $pdf->importPage(14);
    $pdf->useTemplate($templateId);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetXY(141, 15);
    $pdf->Write(0, $iconv_name);

    if ($application->dyeo) {
        $pdf->SetXY(141, 21);
        $pdf->Write(0, $application->dyeo->district_code);
    }

    $photo_self_path = storage_path('app/media-library/'.$application->id.'/Parent-Letter-Page-1.png');
    if (file_exists($photo_self_path)) {
        $pdf->Image($photo_self_path, 23, 38, 176, 0);
    }

    // Page 15   Applicant-Letter Page 3
    $pdf->addPage();
    $templateId = $pdf->importPage(15);
    $pdf->useTemplate($templateId);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetXY(141, 15);
    $pdf->Write(0, $iconv_name);

    if ($application->dyeo) {
        $pdf->SetXY(141, 21);
        $pdf->Write(0, $application->dyeo->district_code);
    }

    $photo_self_path = storage_path('app/media-library/'.$application->id.'/Parent-Letter-Page-2.png');
    if (file_exists($photo_self_path)) {
        $pdf->Image($photo_self_path, 23, 38, 176, 0);
    }

    // ----------Finally Return PDF -----
    return $pdf;
}

function addSimplePage($pdf, $application, $iconv_name, $from_page, $to_page)
{
    for ($page = $from_page; $page <= $to_page; $page++) {
        $pdf->addPage();
        $templateId = $pdf->importPage($page);
        $pdf->useTemplate($templateId);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(141, 15);
        $pdf->Write(0, $iconv_name);

        if ($application->dyeo) {
            $pdf->SetXY(141, 21);
            $pdf->Write(0, $application->dyeo->district_code);
        }
    }

    return $pdf;
}

function get_STEP_Signing_Page_3_5_6($application): Fpdi
{
    $pdf = prepare_STEP_template($application, 'Singing Page of ');
    $image = null;
    $image_name = 'app/app-'.$application->id.'.png';
    $iconv_name = iconv('UTF-8', 'ISO-8859-1', $application->full_name);

    if ($application->media_id && $application->image_data) {
        $image = Image::make($application->image_data);
        $image->save(storage_path($image_name));
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

    $pdf->SetFont('Arial', '', 10);
    $pdf->SetXY(17, 40);
    $pdf->Write(0, $application->religion);
    $pdf->SetXY(64, 40);
    $pdf->Write(0, $application->religion_detail);

    // Smoke
    if ($application->smoke) {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 20, 46, 5, 5);

        $pdf->SetXY(65, 48);
        $pdf->Write(0, $application->smoke_why);
    } else {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 35, 46, 5, 5);
    }
    // Drink
    if ($application->drink) {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 20, 54, 5, 5);

        $pdf->SetXY(65, 56);
        $pdf->Write(0, $application->drink_why);
    } else {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 35, 54, 5, 5);
    }

    if ($application->illegal_drugs) {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 20, 63, 5, 4);

        $pdf->SetXY(65, 65);
        $pdf->Write(0, $application->illegal_drugs_why);
    } else {
        $pdf->Image(storage_path('app/box-crossed-2.png'), 35, 63, 5, 4);
    }

    // Native Language
    $pdf->SetXY(20, 90);
    $pdf->Write(0, $application->native_language);
    // Non-Native Languages
    foreach ($application->languages as $index => $language) {
        if ($index > 2) {
            break;
        }
        $y = 100 + ($index * 6);
        $pdf->SetXY(20, $y);
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

    $y = 124;
    $x_yes = 156;
    $x_no = 180;
    if ($application->dietary_restriction) {
        $pdf->Image(storage_path('app/box-crossed-2.png'), $x_yes, $y, 5, 5);
    } else {
        $pdf->Image(storage_path('app/box-crossed-2.png'), $x_no, $y, 5, 5);
    }
    $y += 6;
    if ($application->medical_condition) {
        $pdf->Image(storage_path('app/box-crossed-2.png'), $x_yes, $y, 5, 5);
    } else {
        $pdf->Image(storage_path('app/box-crossed-2.png'), $x_no, $y, 5, 5);
    }
    $y += 6;
    if ($application->treated_condition) {
        $pdf->Image(storage_path('app/box-crossed-2.png'), $x_yes, $y, 5, 5);
    } else {
        $pdf->Image(storage_path('app/box-crossed-2.png'), $x_no, $y, 5, 5);
    }
    $y += 6;
    if ($application->prescribed_meds) {
        $pdf->Image(storage_path('app/box-crossed-2.png'), $x_yes, $y, 5, 5);
    } else {
        $pdf->Image(storage_path('app/box-crossed-2.png'), $x_no, $y, 5, 5);
    }
    $y += 6;
    if ($application->special_req) {
        $pdf->Image(storage_path('app/box-crossed-2.png'), $x_yes, $y, 5, 5);
    } else {
        $pdf->Image(storage_path('app/box-crossed-2.png'), $x_no, $y, 5, 5);
    }

    // If any of above question is answered Yes then print medical info detail
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(16, 164);
    $pdf->MultiCell(175, 3, $application->medical_info);

    $pdf->SetFont('Arial', '', 10);
    $y = 215;
    $pdf->SetXY(17, $y);
    $pdf->Write(0, $application->club ? $application->club->district_code : '');
    $pdf->SetXY(76, $y);
    $pdf->Write(0, $application->club ? $application->club->club_name : '');
    $pdf->SetXY(160, $y);
    $pdf->Write(0, $application->rotary_club_id);
    $y = 224;
    $pdf->SetXY(17, $y);
    $pdf->Write(0, $application->dyeo ? $application->dyeo->dyeo_name : '');
    $pdf->SetXY(76, $y);
    $pdf->Write(0, $application->club ? $application->club->club_president : '');
    $pdf->SetXY(137, $y);
    $pdf->Write(0, $application->club && $application->club->cyeo ? $application->club->cyeo->cyeo_name : '');
    $y = 233;
    $pdf->SetXY(17, $y);
    $pdf->Write(0, $application->dyeo ? $application->dyeo->dyeo_email : '');
    $pdf->SetXY(76, $y);         // Newly added this field
    $pdf->Write(0, $application->club ? $application->club->club_president_email : '');
    $pdf->SetXY(137, $y);
    $pdf->Write(0, $application->club && $application->club->cyeo ? $application->club->cyeo->cyeo_email : '');
    $y = 242;
    $pdf->SetXY(17, $y);
    $pdf->Write(0, $application->dyeo ? $application->dyeo->dyeo_mobile : '');
    $pdf->SetXY(76, $y);          // Newly added this field
    $pdf->Write(0, $application->club ? $application->club->club_president_mobile : '');
    $pdf->SetXY(136, $y);
    $pdf->Write(0, $application->club && $application->club->cyeo ? $application->club->cyeo->cyeo_mobile : '');

    // ---Page 5-6 -------------------------------------------------
    for ($page = 5; $page <= 6; $page++) {
        $pdf->addPage();
        $templateId = $pdf->importPage($page);
        $pdf->useTemplate($templateId);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(141, 15);
        $pdf->Write(0, $iconv_name);

        if ($application->dyeo) {
            $pdf->SetXY(141, 21);
            $pdf->Write(0, $application->dyeo->district_code);
        }
    }

    // ----------Finally Return PDF -----
    return $pdf;
}
