@php
  $rows = $rows ?? collect();
  $defaultSort = $defaultSort ?? 'check_in';
  $defaultDir = $defaultDir ?? 'asc';
  $emptyMessage = $emptyMessage ?? 'No confirmed bookings found.';
  $showCancellationReason = $showCancellationReason ?? false;
  $highlightDates = $highlightDates ?? false;
  $viewOnlyActions = $viewOnlyActions ?? false;
  $showCreatedBy = $showCreatedBy ?? false;
  $recordRoute = $showCancellationReason ? 'cancelled-bookings.show' : 'group-bookings.show';
  $bookingColspan = ($showCancellationReason ? 41 : 39) + ($showCreatedBy ? 1 : 0);
  $text = function ($value) {
      return filled($value) ? $value : '—';
  };
  $money = function ($value) {
      if ($value === null || $value === '') {
          return '—';
      }

      return '£'.number_format((float) $value, 2);
  };
  $date = function ($value) {
      return $value?->format('d M Y') ?? '—';
  };
@endphp

<div class="table-scroll-hint"><i class="bi bi-arrows-expand"></i> Scroll horizontally to see all columns</div>
<div class="table-wrapper table-scroll-wide table-scroll-enquiries">
  <table class="table table-hover table-sm mb-0" style="font-size:0.78rem">
    <thead>
      <tr>
        <th>Hotel</th>
        <x-sortable-th column="check_in" label="Date of Arrival" :default="$defaultSort" :default-dir="$defaultDir" />
        <x-sortable-th column="check_out" label="Date of Departure" :default="$defaultSort" :default-dir="$defaultDir" />
        <x-sortable-th column="day" label="Day" :default="$defaultSort" :default-dir="$defaultDir" />
        <x-sortable-th column="nights" label="No. of Nights" :default="$defaultSort" :default-dir="$defaultDir" />
        <x-sortable-th column="block_id" label="Block ID" :default="$defaultSort" :default-dir="$defaultDir" />
        <x-sortable-th column="client" label="Client" :default="$defaultSort" :default-dir="$defaultDir" />
        <th>Agency - Ref</th>
        <th>Contact</th>
        <x-sortable-th column="email" label="Email ID" :default="$defaultSort" :default-dir="$defaultDir" />
        <x-sortable-th column="status" label="Status" :default="$defaultSort" :default-dir="$defaultDir" />
        <th>Contract Sent On</th>
        <th>Contract Recd On</th>
        <th>Saved to Doc</th>
        <th>Payment Term</th>
        <th>Due Date</th>
        <th>Payment Status</th>
        <th>CXL Policy</th>
        <th>CXL Due Date</th>
        @if ($showCancellationReason)
          <th>CXL Date</th>
        @endif
        <th>Commission</th>
        <th>Single RNs</th>
        <th>Single Gross Rate</th>
        <th>Double RNs</th>
        <th>Double Gross Rate</th>
        <th>Triple RNs</th>
        <th>Triple Gross Rate</th>
        <x-sortable-th column="total_rns" label="Total RNs" :default="$defaultSort" :default-dir="$defaultDir" />
        <x-sortable-th column="total_revenue" label="Total Rev" :default="$defaultSort" :default-dir="$defaultDir" />
        <th>BB Revenue (Nett £10)</th>
        <th>Dinner Revenue (Nett £)</th>
        <th>Nett Rev EX VAT &amp; BF</th>
        <th>BB/DBB</th>
        <th>Update</th>
        <th>Rooming</th>
        <th>Invoice Status</th>
        <th>Invoice Number</th>
        <th>Invoice Sent On</th>
        <th>Invoice Amount</th>
        <th>Commission Payable Status</th>
        @if ($showCancellationReason)
          <th>Cancellation Reason</th>
        @endif
        @if ($showCreatedBy)
          <th>Created by</th>
        @endif
        <th class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($rows as $enquiry)
        <tr>
          <td class="text-nowrap fw-semibold">{{ $enquiry->hotel?->name ?: '—' }}</td>
          <td class="text-nowrap">@if ($highlightDates)<x-date-alert :date="$enquiry->check_in" />@else{{ $date($enquiry->check_in) }}@endif</td>
          <td class="text-nowrap">{{ $date($enquiry->check_out) }}</td>
          <td class="text-nowrap">{{ $text($enquiry->day) }}</td>
          <td>{{ $enquiry->nights ?? '—' }}</td>
          <td class="fw-semibold text-nowrap"><a href="{{ route($recordRoute, $enquiry) }}">{{ $text($enquiry->block_id) }}</a></td>
          <td class="text-nowrap">{{ $text($enquiry->client) }}</td>
          <td class="text-nowrap">{{ $text($enquiry->agency_ref) }}</td>
          <td class="text-nowrap">{{ $text($enquiry->contact_name) }}</td>
          <td class="text-nowrap">{{ $text($enquiry->email) }}</td>
          <td><x-badge-status :status="$enquiry->status" /></td>
          <td class="text-nowrap">{{ $date($enquiry->contract_sent_on) }}</td>
          <td class="text-nowrap">{{ $date($enquiry->contract_received_on) }}</td>
          <td class="text-nowrap">{{ $text($enquiry->saved_to_doc) }}</td>
          <td class="text-nowrap">@if ($enquiry->payment_term){{ $enquiry->payment_term }}@if ($enquiry->payment_term_days !== null) ({{ $enquiry->payment_term_days }} {{ (int) $enquiry->payment_term_days === 1 ? 'day' : 'days' }})@endif@else—@endif</td>
          <td class="text-nowrap">@if ($highlightDates)<x-date-alert :date="$enquiry->payment_due_date" />@else{{ $date($enquiry->payment_due_date) }}@endif</td>
          <td class="text-nowrap">{{ $text($enquiry->payment_status) }}</td>
          <td class="text-nowrap">{{ $text($enquiry->cxl_policy) }}</td>
          <td class="text-nowrap">@if ($highlightDates)<x-date-alert :date="$enquiry->cxl_due_date" />@else{{ $date($enquiry->cxl_due_date) }}@endif</td>
          @if ($showCancellationReason)
            <td class="text-nowrap">{{ $date($enquiry->cxl_date) }}</td>
          @endif
          <td class="text-nowrap">{{ $enquiry->has_commission === null ? '—' : ($enquiry->has_commission ? 'Yes' : 'No') }}</td>
          <td>{{ $enquiry->single_rooms ?? '—' }}</td>
          <td class="text-nowrap">{{ $money($enquiry->single_rate) }}</td>
          <td>{{ $enquiry->double_rooms ?? '—' }}</td>
          <td class="text-nowrap">{{ $money($enquiry->double_rate) }}</td>
          <td>{{ $enquiry->triple_rooms ?? '—' }}</td>
          <td class="text-nowrap">{{ $money($enquiry->triple_rate) }}</td>
          <td>{{ $enquiry->total_rns ?? '—' }}</td>
          <td class="text-nowrap fw-semibold">{{ $money($enquiry->total_revenue) }}</td>
          <td class="text-nowrap">{{ $money($enquiry->bb_revenue) }}</td>
          <td class="text-nowrap">{{ $money($enquiry->dinner_revenue) }}</td>
          <td class="text-nowrap">{{ $money($enquiry->nett_rev_ex_vat) }}</td>
          <td class="text-nowrap">{{ $text($enquiry->basis) }}</td>
          <td class="text-nowrap">{{ $text($enquiry->booking_update) }}</td>
          <td class="text-nowrap">{{ $text($enquiry->rooming) }}</td>
          <td class="text-nowrap">{{ $text($enquiry->invoice_status) }}</td>
          <td class="text-nowrap">{{ $text($enquiry->invoice_number) }}</td>
          <td class="text-nowrap">{{ $date($enquiry->invoice_sent_on) }}</td>
          <td class="text-nowrap">{{ $money($enquiry->invoice_amount) }}</td>
          <td class="text-nowrap">{{ $text($enquiry->commission_payable_status) }}</td>
          @if ($showCancellationReason)
            <td style="max-width:220px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $enquiry->cancellation_reason }}">{{ $text($enquiry->cancellation_reason) }}</td>
          @endif
          @if ($showCreatedBy)
            <td class="text-nowrap">{{ $enquiry->assignedTo?->name ?: '—' }}</td>
          @endif
          <td class="text-end text-nowrap">
            <a href="{{ route($recordRoute, $enquiry) }}" class="btn btn-sm btn-outline-secondary" title="View">
              <i class="bi bi-eye"></i>
            </a>
            @unless ($showCancellationReason || $viewOnlyActions)
              <a href="{{ route('group-bookings.contract', $enquiry) }}" class="btn btn-sm btn-outline-primary" title="Hotel contract">
                <i class="bi bi-file-earmark-pdf"></i>
              </a>
              @can('update', $enquiry)
                <a href="{{ route('group-bookings.edit', $enquiry) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                  <i class="bi bi-pencil"></i>
                </a>
              @endcan
            @endunless
          </td>
        </tr>
      @empty
        <tr><td colspan="{{ $bookingColspan }}" class="text-center text-secondary py-4">{{ $emptyMessage }}</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
