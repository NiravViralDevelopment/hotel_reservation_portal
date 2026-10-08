<?php

namespace App\Support;

use App\Models\Company;
use App\Models\Enquiry;
use App\Models\Hotel;
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
        $enquiry->loadMissing(['travelAgency', 'contact']);
        $hotel?->loadMissing(['company', 'managerUser']);

        $company = $hotel?->company;
        $agency = $enquiry->travelAgency;
        $contact = $enquiry->contact;
        $manager = $hotel?->managerUser;

        $hotelRole = $manager?->job_title ?: ($hotel?->manager_name ? 'Hotel Manager' : null);
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
            'hotelSignName' => self::show($hotel?->manager_name ?: $manager?->name),
            'hotelSignTitle' => self::show($hotelRole ?: 'Hotel Representative'),
            'clientSignName' => self::show($clientContact),
            'clientSignTitle' => self::show($clientRole ?: 'Client Representative'),
            'hotelSignDate' => $enquiry->contract_sent_on?->format('d/m/Y') ?: '—',
            'clientSignDate' => $enquiry->contract_received_on?->format('d/m/Y') ?: '—',
            'contractNotes' => filled($enquiry->booking_contract_notes)
                ? (string) $enquiry->booking_contract_notes
                : 'Add any additional commercial terms, special conditions, inclusions, exclusions, or notes applicable to this hotel contract.',
            'extras' => self::extras($enquiry),
        ])->render();
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

        $allowed = '<div><section><header><footer><small><h1><h2><h3><p><br><strong><b><em><i><u><ul><ol><li><table><thead><tbody><tr><th><td><span><hr>';

        return strip_tags($html, $allowed);
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
