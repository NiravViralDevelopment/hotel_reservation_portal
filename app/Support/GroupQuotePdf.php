<?php

namespace App\Support;

use App\Models\Enquiry;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GroupQuotePdf
{
    public static function shouldGenerate(Enquiry $enquiry): bool
    {
        if (! $enquiry->is_confirm || $enquiry->is_cancel) {
            return false;
        }

        $status = strtolower((string) preg_replace('/[\s_\-]+/', '', (string) $enquiry->status));

        return $status === 'confirmed';
    }

    public static function generate(Enquiry $enquiry): bool
    {
        if (! self::shouldGenerate($enquiry)) {
            return false;
        }

        $enquiry->loadMissing(['hotel', 'travelAgency', 'assignedTo']);

        @ini_set('memory_limit', '512M');
        @set_time_limit(180);

        $filename = self::filename($enquiry);
        $html = view('group-bookings.group-quote-pdf', self::viewData($enquiry, $filename))->render();

        $options = new Options;
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');
        $options->setTempDir(storage_path('app/dompdf'));

        if (! is_dir(storage_path('app/dompdf'))) {
            mkdir(storage_path('app/dompdf'), 0775, true);
        }

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();
        if (! is_string($output) || ! str_starts_with($output, '%PDF')) {
            return false;
        }

        $disk = 'local';
        $directory = 'group-quote-pdfs/'.$enquiry->id;
        $path = $directory.'/'.$filename;

        $enquiry->deleteGroupQuotePdfFile();

        Storage::disk($disk)->makeDirectory($directory);
        Storage::disk($disk)->put($path, $output);

        $enquiry->forceFill([
            'group_quote_pdf_disk' => $disk,
            'group_quote_pdf_path' => $path,
            'group_quote_pdf_original_name' => $filename,
            'group_quote_pdf_mime_type' => 'application/pdf',
            'group_quote_pdf_size' => strlen($output),
            'group_quote_pdf_generated_at' => now(),
        ])->save();

        return true;
    }

    public static function generateIfNeeded(Enquiry $enquiry): bool
    {
        if (! self::shouldGenerate($enquiry)) {
            return false;
        }

        if ($enquiry->hasGroupQuotePdf() && Storage::disk($enquiry->group_quote_pdf_disk ?: 'local')->exists((string) $enquiry->group_quote_pdf_path)) {
            return true;
        }

        return self::generate($enquiry);
    }

    /**
     * @return array<string, mixed>
     */
    public static function viewData(Enquiry $enquiry, ?string $filename = null): array
    {
        $hotelName = $enquiry->hotel?->name ?: 'Hotel';
        $guestName = $enquiry->client ?: ($enquiry->group_name ?: 'Guest');
        $reference = $enquiry->block_id ?: ($enquiry->ref ?: '—');

        $rateParts = [];
        if ((int) ($enquiry->single_rooms ?? 0) > 0 || (float) ($enquiry->single_rate ?? 0) > 0) {
            $rateParts[] = self::money($enquiry->single_rate).' Single';
        }
        if ((int) ($enquiry->double_rooms ?? 0) > 0 || (float) ($enquiry->double_rate ?? 0) > 0) {
            $rateParts[] = self::money($enquiry->double_rate).' Double';
        }
        if ((int) ($enquiry->twin_rooms ?? 0) > 0 || (float) ($enquiry->twin_rate ?? 0) > 0) {
            $rateParts[] = self::money($enquiry->twin_rate).' Twin';
        }
        if ((int) ($enquiry->triple_rooms ?? 0) > 0 || (float) ($enquiry->triple_rate ?? 0) > 0) {
            $rateParts[] = self::money($enquiry->triple_rate).' Triple';
        }
        $ratesLabel = $rateParts !== [] ? implode(' / ', $rateParts) : '—';

        $roomParts = [];
        if ((int) ($enquiry->twin_rooms ?? 0) > 0) {
            $roomParts[] = (int) $enquiry->twin_rooms.' x Twin';
        }
        if ((int) ($enquiry->double_rooms ?? 0) > 0) {
            $roomParts[] = (int) $enquiry->double_rooms.' x Double';
        }
        if ((int) ($enquiry->single_rooms ?? 0) > 0) {
            $roomParts[] = (int) $enquiry->single_rooms.' x Single';
        }
        if ((int) ($enquiry->triple_rooms ?? 0) > 0) {
            $roomParts[] = (int) $enquiry->triple_rooms.' x Triple';
        }
        $roomsOffered = $roomParts !== [] ? implode(', ', $roomParts) : '—';

        $paymentLabel = '—';
        if (filled($enquiry->payment_term)) {
            $paymentLabel = (string) $enquiry->payment_term;
            if ($enquiry->payment_term_days !== null) {
                $days = (int) $enquiry->payment_term_days;
                $paymentLabel .= ' ('.$days.' '.($days === 1 ? 'day' : 'days').')';
            }
        } elseif (filled($enquiry->payment_due_date)) {
            $paymentLabel = $enquiry->payment_due_date->format('d M Y');
        }

        $basis = strtoupper((string) ($enquiry->basis ?: 'BB'));
        $basisNote = $basis === 'DBB'
            ? 'Rates are inclusive of Dinner, Bed & Breakfast'.($enquiry->has_commission ? '.' : ' and non – commissionable.')
            : 'Rates are inclusive of Bed & Breakfast'.($enquiry->has_commission ? '.' : ' and non – commissionable.');

        $rateRows = self::rateRows($enquiry, $ratesLabel);

        return [
            'filename' => $filename ?: self::filename($enquiry),
            'guestName' => $guestName,
            'hotelName' => $hotelName,
            'groupReference' => $reference,
            'rateRows' => $rateRows,
            'showNoTripleNote' => (int) ($enquiry->triple_rooms ?? 0) === 0,
            'roomsOffered' => $roomsOffered,
            'roomType' => 'Standard Double / Twin Room (Twin beds are subject to availability)',
            'cancellationPolicy' => filled($enquiry->cxl_policy) ? (string) $enquiry->cxl_policy : '—',
            'paymentLabel' => $paymentLabel,
            'optionDate' => $enquiry->option_date?->format('jS M Y') ?: '—',
            'commissionable' => $enquiry->has_commission === null ? 'Yes / No' : ($enquiry->has_commission ? 'Yes' : 'No'),
            'basisNote' => $basisNote,
            'dinnerRate' => $enquiry->dinner_rate !== null ? self::money($enquiry->dinner_rate) : null,
            'generatedOn' => now()->format('d M Y'),
        ];
    }

    /**
     * @return list<array{check_in: string, check_out: string, nights: string, rates: string}>
     */
    private static function rateRows(Enquiry $enquiry, string $fallbackRates): array
    {
        $segments = is_array($enquiry->stay_segments) ? $enquiry->stay_segments : [];
        $rows = [];

        foreach ($segments as $segment) {
            if (! is_array($segment)) {
                continue;
            }

            $checkIn = self::formatDate($segment['check_in'] ?? null);
            $checkOut = self::formatDate($segment['check_out'] ?? null);
            if ($checkIn === '—' && $checkOut === '—') {
                continue;
            }

            $nights = $segment['nights'] ?? null;
            if ($nights === null && ! empty($segment['check_in']) && ! empty($segment['check_out'])) {
                try {
                    $nights = \Illuminate\Support\Carbon::parse($segment['check_in'])
                        ->diffInDays(\Illuminate\Support\Carbon::parse($segment['check_out']));
                } catch (\Throwable) {
                    $nights = null;
                }
            }

            $segRates = [];
            foreach (['single_rate' => 'Single', 'double_rate' => 'Double', 'twin_rate' => 'Twin', 'triple_rate' => 'Triple'] as $key => $label) {
                if (isset($segment[$key]) && $segment[$key] !== '' && $segment[$key] !== null) {
                    $segRates[] = self::money($segment[$key]).' '.$label;
                }
            }

            $rows[] = [
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'nights' => $nights !== null ? (string) $nights : '—',
                'rates' => $segRates !== [] ? implode(' / ', $segRates) : $fallbackRates,
            ];
        }

        if ($rows !== []) {
            return $rows;
        }

        return [[
            'check_in' => $enquiry->check_in?->format('d/m/Y') ?: '—',
            'check_out' => $enquiry->check_out?->format('d/m/Y') ?: '—',
            'nights' => $enquiry->nights !== null ? (string) $enquiry->nights : '—',
            'rates' => $fallbackRates,
        ]];
    }

    private static function formatDate(mixed $value): string
    {
        if (! filled($value)) {
            return '—';
        }

        try {
            return \Illuminate\Support\Carbon::parse($value)->format('d/m/Y');
        } catch (\Throwable) {
            return '—';
        }
    }

    private static function money(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '£0';
        }

        $amount = round((float) $value, 2);
        if (fmod($amount, 1.0) === 0.0) {
            return '£'.number_format($amount, 0);
        }

        return '£'.number_format($amount, 2);
    }

    public static function filename(Enquiry $enquiry): string
    {
        $base = preg_replace('/[^A-Za-z0-9._-]+/', '-', (string) ($enquiry->block_id ?: $enquiry->ref ?: 'group-quote')) ?: 'group-quote';

        return Str::lower($base).'-quote.pdf';
    }
}
