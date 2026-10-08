<?php

namespace App\Support;

use App\Models\Enquiry;
use App\Models\Hotel;

class BookingContractHtml
{
    public static function loadingPlaceholder(Enquiry $enquiry, ?Hotel $hotel = null): string
    {
        $hotelName = e($hotel?->name ?: 'selected hotel');

        return <<<HTML
<div class="contract-doc-inner">
  <h1>Hotel Contract</h1>
  <p class="contract-lead">Loading paragraphs from the {$hotelName} PDF… You can edit every paragraph once it appears.</p>
  <p class="text-secondary">If text does not appear, open the Hotel PDF tab or click “Load paragraphs from hotel PDF”.</p>
</div>
HTML;
    }

    public static function build(Enquiry $enquiry, ?Hotel $hotel = null): string
    {
        $hotel ??= $enquiry->hotel;
        $agency = $enquiry->travelAgency?->name ?: ($enquiry->agency_ref ?: '—');
        $money = fn ($value) => $value === null || $value === ''
            ? '—'
            : '£'.number_format((float) $value, 2);
        $date = fn ($value) => $value?->format('d M Y') ?? '—';
        $text = fn ($value) => filled($value) ? e((string) $value) : '—';

        $paymentTerm = $enquiry->payment_term
            ? e($enquiry->payment_term).($enquiry->payment_term_days !== null ? ' ('.(int) $enquiry->payment_term_days.' days)' : '')
            : '—';

        $hotelName = $hotel?->name ? e($hotel->name) : '—';
        $hotelCode = $hotel?->code ? e($hotel->code) : '—';
        $hotelCity = collect([$hotel?->city, $hotel?->country])->filter()->implode(', ') ?: '—';

        return <<<HTML
<div class="contract-doc-inner">
  <h1>Group Booking Contract</h1>
  <p class="contract-lead">This booking-specific contract is based on the selected hotel agreement and group booking details. Edit any text below. Saving does not change the hotel’s original uploaded contract PDF.</p>

  <h2>1. Hotel</h2>
  <table>
    <tr><th>Hotel</th><td>{$hotelName}</td></tr>
    <tr><th>Code</th><td>{$hotelCode}</td></tr>
    <tr><th>Location</th><td>{$hotelCity}</td></tr>
    <tr><th>Master contract file</th><td>{$text($hotel?->document_original_name)}</td></tr>
  </table>

  <h2>2. Group booking</h2>
  <table>
    <tr><th>Block ID</th><td>{$text($enquiry->block_id)}</td></tr>
    <tr><th>Group / client</th><td>{$text($enquiry->group_name ?: $enquiry->client)}</td></tr>
    <tr><th>Agency / ref</th><td>{$text($agency)}</td></tr>
    <tr><th>Contact</th><td>{$text($enquiry->contact_name)}</td></tr>
    <tr><th>Email</th><td>{$text($enquiry->email)}</td></tr>
    <tr><th>Arrival</th><td>{$date($enquiry->check_in)}</td></tr>
    <tr><th>Departure</th><td>{$date($enquiry->check_out)}</td></tr>
    <tr><th>Nights</th><td>{$text($enquiry->nights)}</td></tr>
    <tr><th>Basis</th><td>{$text($enquiry->basis)}</td></tr>
  </table>

  <h2>3. Rooms and rates</h2>
  <table>
    <tr><th>Single rooms / rate</th><td>{$text($enquiry->single_rooms)} / {$money($enquiry->single_rate)}</td></tr>
    <tr><th>Double rooms / rate</th><td>{$text($enquiry->double_rooms)} / {$money($enquiry->double_rate)}</td></tr>
    <tr><th>Triple rooms / rate</th><td>{$text($enquiry->triple_rooms)} / {$money($enquiry->triple_rate)}</td></tr>
    <tr><th>Total room nights</th><td>{$text($enquiry->total_rns)}</td></tr>
    <tr><th>Total revenue</th><td>{$money($enquiry->total_revenue)}</td></tr>
  </table>

  <h2>4. Contract and payment terms</h2>
  <table>
    <tr><th>Contract sent on</th><td>{$date($enquiry->contract_sent_on)}</td></tr>
    <tr><th>Contract received on</th><td>{$date($enquiry->contract_received_on)}</td></tr>
    <tr><th>Payment term</th><td>{$paymentTerm}</td></tr>
    <tr><th>Payment due date</th><td>{$date($enquiry->payment_due_date)}</td></tr>
    <tr><th>Payment status</th><td>{$text($enquiry->payment_status)}</td></tr>
    <tr><th>CXL policy</th><td>{$text($enquiry->cxl_policy)}</td></tr>
    <tr><th>CXL due date</th><td>{$date($enquiry->cxl_due_date)}</td></tr>
  </table>

  <h2>5. Agreement notes</h2>
  <p>Click into this document and change any wording, dates, rates, or terms needed for this booking. Use the “Replace PDF” option if you also want to attach a different full contract PDF for this booking only.</p>
  <p><em>{$text($enquiry->booking_contract_notes)}</em></p>

  <h2>6. Sign-off</h2>
  <p>Hotel representative: ____________________________ &nbsp;&nbsp; Date: ______________</p>
  <p>Client / agency representative: ____________________________ &nbsp;&nbsp; Date: ______________</p>
</div>
HTML;
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

        $allowed = '<div><h1><h2><h3><p><br><strong><b><em><i><u><ul><ol><li><table><thead><tbody><tr><th><td><span><hr>';

        return strip_tags($html, $allowed);
    }
}
