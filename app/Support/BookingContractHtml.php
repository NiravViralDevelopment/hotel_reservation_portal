<?php

namespace App\Support;

use App\Models\Company;
use App\Models\Enquiry;
use App\Models\EnquiryContractPhoto;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookingContractHtml
{
    public static function isTemplateDocument(?string $html): bool
    {
        return is_string($html) && str_contains($html, 'hotel-contract-document');
    }

    public static function build(Enquiry $enquiry, ?Hotel $hotel = null): string
    {
        $hotel ??= $enquiry->hotel;
        $enquiry->loadMissing(['travelAgency', 'contact', 'contractPhotos']);
        $hotel?->loadMissing(['company', 'managerUser']);

        $company = $hotel?->company;
        $agency = $enquiry->travelAgency;
        $contact = $enquiry->contact;
        $manager = $hotel?->managerUser;

        $signer = Auth::user();
        $hotelRole = $signer?->job_title
            ?: ($manager?->job_title ?: ($hotel?->manager_name ? 'Hotel Manager' : null));
        $hotelStreet = self::flatten($company?->trading_address ?: $company?->address ?: $company?->registered_address);
        $hotelCityLine = collect([$hotel?->city, $hotel?->country])->filter()->implode(', ');
        $hotelAddress = collect([$hotelStreet, $hotelCityLine])->filter()->implode(', ');
        $hotelPostcode = self::postcode($hotelStreet) ?: '—';

        $clientName = $agency?->name ?: ($enquiry->client ?: $enquiry->group_name);
        $clientCityLine = collect([$agency?->city, $agency?->country])->filter()->implode(', ');
        $clientContact = $enquiry->contact_name ?: ($contact?->name ?: $agency?->contact_name);
        $clientRole = $contact?->position;
        $clientPhone = $enquiry->mobile ?: ($contact?->phone ?: $agency?->phone);
        $clientEmail = $enquiry->email ?: ($contact?->email ?: $agency?->email);

        $nights = (int) ($enquiry->nights ?? 0);
        $paymentDays = $enquiry->payment_term_days !== null ? (int) $enquiry->payment_term_days : null;

        return view('group-bookings.partials.hotel-contract-document', [
            'hotelName' => self::show($hotel?->name),
            'hotelAddress' => self::show($hotelAddress),
            'hotelStreet' => self::show($hotelStreet ?: $hotelCityLine),
            'hotelPostcode' => $hotelPostcode,
            'hotelContact' => self::show($hotel?->manager_name ?: $manager?->name),
            'hotelRole' => self::show($hotelRole),
            'hotelPhone' => self::show($hotel?->phone ?: $manager?->phone),
            'hotelEmail' => self::show($hotel?->email ?: $manager?->email),
            'legalName' => self::show($company?->name ?: $hotel?->name),
            'registeredOffice' => self::show(self::registeredOffice($company, $hotelCityLine)),
            'regNumber' => self::show($company?->reg_number),
            'vatNumber' => self::show($company?->vat_number),
            'clientName' => self::show($clientName),
            'clientAddress' => self::show($clientCityLine),
            'clientPostcode' => '—',
            'clientContact' => self::show($clientContact),
            'clientRole' => self::show($clientRole),
            'clientPhone' => self::show($clientPhone),
            'clientEmail' => self::show($clientEmail),
            'groupName' => self::show($enquiry->group_name ?: $enquiry->client ?: $clientName),
            'arrivalDate' => $enquiry->check_in?->format('d F Y') ?: '—',
            'totalRooms' => self::roomSummary($enquiry),
            'nightsLabel' => $nights > 0 ? $nights.' '.Str::plural('Night', $nights) : '—',
            'rateLabel' => self::rateSummary($enquiry),
            'paymentLabel' => self::paymentLabel($enquiry, $paymentDays),
            'cancellationLabel' => self::show($enquiry->cxl_policy ?: ($paymentDays ? $paymentDays.' Days Prior to Arrival' : null)),
            'cancellationWindow' => $paymentDays
                ? '0 to '.$paymentDays.' days'
                : (filled($enquiry->cxl_policy) ? (string) $enquiry->cxl_policy : 'the agreed cancellation window'),
            'paymentWindow' => $paymentDays ? $paymentDays.' days' : 'the agreed payment window',
            'hotelLogoUrl' => self::hotelLogoDataUri($hotel),
            'hotelSignName' => self::show($signer?->name ?: ($hotel?->manager_name ?: $manager?->name)),
            'hotelSignTitle' => self::show($hotelRole ?: 'Hotel Representative'),
            'hotelSignatureUrl' => self::userSignatureDataUri($signer),
            'clientSignName' => self::show($clientContact),
            'clientSignTitle' => self::show($clientRole ?: 'Client Representative'),
            'hotelSignDate' => $enquiry->contract_sent_on?->format('d/m/Y') ?: now()->format('d/m/Y'),
            'clientSignDate' => $enquiry->contract_received_on?->format('d/m/Y') ?: '—',
            'contractNotes' => filled($enquiry->booking_contract_notes)
                ? (string) $enquiry->booking_contract_notes
                : 'Add any additional commercial terms, special conditions, inclusions, exclusions, or notes applicable to this hotel contract.',
            'extras' => self::extras($enquiry),
            'contractPhotos' => $enquiry->contractPhotos,
            'enquiry' => $enquiry,
        ])->render();
    }

    public static function syncPhotosSection(string $html, Enquiry $enquiry, bool $embedPhotos = false): string
    {
        $enquiry->loadMissing('contractPhotos');
        $block = view('group-bookings.partials.contract-photos', [
            'enquiry' => $enquiry,
            'photos' => $enquiry->contractPhotos,
            'embedPhotos' => $embedPhotos,
        ])->render();

        if (preg_match('/<div\b[^>]*\bid=["\']contract-photos-section["\'][^>]*>/i', $html, $match, PREG_OFFSET_CAPTURE)) {
            $openPos = (int) $match[0][1];
            $closePos = self::findMatchingCloseDiv($html, $openPos);
            if ($closePos !== null) {
                return substr($html, 0, $openPos).$block.substr($html, $closePos);
            }
        }

        if (preg_match('/<div class="footer">/', $html, $match, PREG_OFFSET_CAPTURE)) {
            $pos = (int) $match[0][1];

            return substr($html, 0, $pos).$block."\n".substr($html, $pos);
        }

        return $html.$block;
    }

    public static function htmlForPdf(Enquiry $enquiry, ?Hotel $hotel = null): string
    {
        $hotel ??= $enquiry->hotel;
        $savedHtml = $enquiry->booking_contract_html;
        $html = filled($savedHtml)
            ? (string) $savedHtml
            : self::build($enquiry, $hotel);

        $html = self::syncPhotosSection($html, $enquiry, true);

        return self::convertEmbeddedImagesToJpeg($html);
    }

    public static function photoDataUri(EnquiryContractPhoto $photo): ?string
    {
        if (! $photo->hasFile()) {
            return null;
        }

        return self::storageJpegDataUri(
            $photo->disk ?: 'local',
            (string) $photo->path,
            900
        ) ?: self::storageDataUri(
            $photo->disk ?: 'local',
            (string) $photo->path,
            $photo->mime_type ?: 'image/jpeg'
        );
    }

    public static function convertEmbeddedImagesToJpeg(string $html): string
    {
        return (string) preg_replace_callback(
            '/src=(["\'])(data:image\/(?:png|jpeg|jpg|gif|webp);base64,[A-Za-z0-9+\/=]+)\1/i',
            function (array $matches): string {
                $converted = self::dataUriToJpegDataUri($matches[2]);

                return 'src='.$matches[1].($converted ?: $matches[2]).$matches[1];
            },
            $html
        );
    }

    public static function sanitize(?string $html): ?string
    {
        if ($html === null) {
            return null;
        }

        $html = trim($html);
        if ($html === '') {
            return null;
        }

        $allowed = '<div><section><header><footer><small><h1><h2><h3><p><br><strong><b><em><i><u><ul><ol><li><table><thead><tbody><tr><th><td><span><hr><img>';

        return strip_tags($html, $allowed);
    }

    private static function hotelLogoDataUri(?Hotel $hotel): ?string
    {
        if (! $hotel || ! $hotel->hasLogo()) {
            return null;
        }

        return self::storageDataUri(
            $hotel->logo_disk ?: 'local',
            (string) $hotel->logo_path,
            $hotel->logo_mime_type ?: 'image/png'
        );
    }

    private static function userSignatureDataUri(?User $user): ?string
    {
        if (! $user || ! $user->hasSignature()) {
            return null;
        }

        return self::storageDataUri(
            $user->signature_disk ?: 'local',
            (string) $user->signature_path,
            $user->signature_mime_type ?: 'image/png'
        );
    }

    private static function storageDataUri(string $disk, string $path, string $mime): ?string
    {
        if ($path === '' || str_contains($path, '..') || ! Storage::disk($disk)->exists($path)) {
            return null;
        }

        $binary = Storage::disk($disk)->get($path);
        if (! is_string($binary) || $binary === '') {
            return null;
        }

        return 'data:'.$mime.';base64,'.base64_encode($binary);
    }

    private static function storageJpegDataUri(string $disk, string $path, int $maxWidth = 900): ?string
    {
        if ($path === '' || str_contains($path, '..') || ! Storage::disk($disk)->exists($path)) {
            return null;
        }

        $binary = Storage::disk($disk)->get($path);
        if (! is_string($binary) || $binary === '') {
            return null;
        }

        return self::binaryToJpegDataUri($binary, $maxWidth);
    }

    private static function dataUriToJpegDataUri(string $dataUri): ?string
    {
        if (! preg_match('/^data:image\/[a-z0-9+.-]+;base64,(.+)$/i', $dataUri, $match)) {
            return null;
        }

        $binary = base64_decode($match[1], true);
        if ($binary === false || $binary === '') {
            return null;
        }

        return self::binaryToJpegDataUri($binary, 900);
    }

    private static function binaryToJpegDataUri(string $binary, int $maxWidth = 900): ?string
    {
        if (! function_exists('imagecreatefromstring')) {
            return null;
        }

        $source = @imagecreatefromstring($binary);
        if ($source === false) {
            return null;
        }

        $width = imagesx($source);
        $height = imagesy($source);
        if ($width < 1 || $height < 1) {
            imagedestroy($source);

            return null;
        }

        $targetWidth = $width;
        $targetHeight = $height;
        if ($width > $maxWidth) {
            $targetWidth = $maxWidth;
            $targetHeight = (int) max(1, round($height * ($maxWidth / $width)));
        }

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        if ($canvas === false) {
            imagedestroy($source);

            return null;
        }

        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $white);
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
        imagedestroy($source);

        ob_start();
        imagejpeg($canvas, null, 82);
        $jpeg = ob_get_clean();
        imagedestroy($canvas);

        if (! is_string($jpeg) || $jpeg === '') {
            return null;
        }

        return 'data:image/jpeg;base64,'.base64_encode($jpeg);
    }

    private static function findMatchingCloseDiv(string $html, int $openPos): ?int
    {
        if (! preg_match('/<div\b[^>]*>/i', $html, $openMatch, 0, $openPos)) {
            return null;
        }

        $pos = $openPos + strlen($openMatch[0]);
        $depth = 1;
        $length = strlen($html);

        while ($pos < $length && $depth > 0) {
            $nextOpen = stripos($html, '<div', $pos);
            $nextClose = stripos($html, '</div>', $pos);

            if ($nextClose === false) {
                return null;
            }

            if ($nextOpen !== false && $nextOpen < $nextClose) {
                $depth++;
                $pos = $nextOpen + 4;
                continue;
            }

            $depth--;
            $pos = $nextClose + 6;
            if ($depth === 0) {
                return $pos;
            }
        }

        return null;
    }

    private static function show(mixed $value): string
    {
        return filled($value) ? trim((string) $value) : '—';
    }

    private static function flatten(?string $value): string
    {
        if (! filled($value)) {
            return '';
        }

        return trim((string) preg_replace('/\s+/', ' ', str_replace(["\r\n", "\r", "\n"], ', ', $value)));
    }

    private static function postcode(string $address): ?string
    {
        if (preg_match('/\b([A-Z]{1,2}\d[A-Z\d]?\s*\d[A-Z]{2})\b/i', $address, $match)) {
            return strtoupper($match[1]);
        }

        return null;
    }

    private static function registeredOffice(?Company $company, string $cityLine): ?string
    {
        $office = self::flatten($company?->registered_address ?: $company?->address);

        return $office !== '' ? $office : ($cityLine !== '' ? $cityLine : null);
    }

    private static function roomSummary(Enquiry $enquiry): string
    {
        $parts = [];
        foreach ([
            'single_rooms' => 'Single',
            'double_rooms' => 'Double',
            'triple_rooms' => 'Triple',
        ] as $field => $label) {
            $count = (int) ($enquiry->{$field} ?? 0);
            if ($count > 0) {
                $parts[] = str_pad((string) $count, 2, '0', STR_PAD_LEFT).' '.$label;
            }
        }

        return $parts !== [] ? implode(', ', $parts) : '—';
    }

    private static function rateSummary(Enquiry $enquiry): string
    {
        $money = fn ($value) => '£'.number_format((float) $value, 2);
        $parts = [];

        if ($enquiry->single_rate !== null && $enquiry->single_rate !== '') {
            $parts[] = $money($enquiry->single_rate).' SGL';
        }
        if ($enquiry->double_rate !== null && $enquiry->double_rate !== '') {
            $parts[] = $money($enquiry->double_rate).' DBL';
        }
        if ($enquiry->triple_rate !== null && $enquiry->triple_rate !== '') {
            $parts[] = $money($enquiry->triple_rate).' TPL';
        }

        if ($parts === []) {
            return '—';
        }

        $label = implode(' / ', $parts).' per room per night';
        if (filled($enquiry->basis)) {
            $label .= ', '.$enquiry->basis;
        }
        if ($enquiry->has_tax && $enquiry->tax_percentage !== null && $enquiry->tax_percentage !== '') {
            $label .= ', including VAT at '.rtrim(rtrim(number_format((float) $enquiry->tax_percentage, 2), '0'), '.').'%';
        }

        return $label;
    }

    private static function paymentLabel(Enquiry $enquiry, ?int $paymentDays): string
    {
        if ($paymentDays) {
            $label = $paymentDays.' Days Prior to Arrival';
            if (filled($enquiry->payment_term)) {
                $label .= ' ('.$enquiry->payment_term.')';
            }

            return $label;
        }

        return self::show($enquiry->payment_term);
    }

    /**
     * @return list<string>
     */
    private static function extras(Enquiry $enquiry): array
    {
        $lines = [];

        if ($enquiry->dinner_revenue !== null && (float) $enquiry->dinner_revenue > 0) {
            $lines[] = 'Dinner revenue: £'.number_format((float) $enquiry->dinner_revenue, 2).'.';
        }
        if (filled($enquiry->remarks)) {
            $lines[] = trim((string) $enquiry->remarks);
        }
        if ($lines === []) {
            $lines[] = 'Additional inclusions and commercial notes for this booking.';
        }

        return $lines;
    }
}
