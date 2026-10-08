@php
  $rows = $rows ?? collect();
  $defaultSort = $defaultSort ?? 'enquiry_date';
  $defaultDir = $defaultDir ?? 'desc';
  $emptyMessage = $emptyMessage ?? 'No records found.';
  $variant = $variant ?? 'full';
  $showCancellationReason = $showCancellationReason ?? false;
  $readOnly = $readOnly ?? false;
  $recordRoute = $recordRoute ?? 'enquiries.show';
  $editRoute = $editRoute ?? 'enquiries.edit';
  $recordQuery = $recordQuery ?? [];
  $money = function ($value) {
      if ($value === null || $value === '') {
          return '—';
      }

      return '£'.number_format((float) $value, 2);
  };
@endphp

<div class="table-scroll-hint"><i class="bi bi-arrows-expand"></i> Scroll horizontally to see all columns</div>
<div class="table-wrapper table-scroll-wide table-scroll-enquiries">
@if ($variant === 'enquiry')
  <table class="table table-hover table-sm mb-0" style="font-size:0.78rem">
    <thead>
      <tr>
        <th>Hotel</th>
        <x-sortable-th column="enquiry_date" label="Enquiry Date" :default="$defaultSort" :default-dir="$defaultDir" />
        <x-sortable-th column="response_date" label="Response Date" :default="$defaultSort" :default-dir="$defaultDir" />
        <x-sortable-th column="check_in" label="Arrival Date" :default="$defaultSort" :default-dir="$defaultDir" />
        <x-sortable-th column="check_out" label="Departure Date" :default="$defaultSort" :default-dir="$defaultDir" />
        <x-sortable-th column="day" label="Day" :default="$defaultSort" :default-dir="$defaultDir" />
        <x-sortable-th column="nights" label="Nights" :default="$defaultSort" :default-dir="$defaultDir" />
        <x-sortable-th column="rooms_per_night" label="Total Room per Night" :default="$defaultSort" :default-dir="$defaultDir" />
        <x-sortable-th column="group_name" label="Group Name" :default="$defaultSort" :default-dir="$defaultDir" />
        <x-sortable-th column="ref" label="Ref No" :default="$defaultSort" :default-dir="$defaultDir" />
        <x-sortable-th column="email" label="Email ID" :default="$defaultSort" :default-dir="$defaultDir" />
        <x-sortable-th column="status" label="Status" :default="$defaultSort" :default-dir="$defaultDir" />
        <th>Single</th>
        <th>Single Rate</th>
        <th>Double</th>
        <th>Double Rate</th>
        <th>Triple</th>
        <th>Triple Rate</th>
        <th>Basis</th>
        <x-sortable-th column="option_date" label="Option Date" :default="$defaultSort" :default-dir="$defaultDir" />
        <th>CXL Policy</th>
        <x-sortable-th column="cxl_due_date" label="CXL Due Date" :default="$defaultSort" :default-dir="$defaultDir" />
        <x-sortable-th column="total_revenue" label="Total Revenue" :default="$defaultSort" :default-dir="$defaultDir" />
        <th>Remarks</th>
        @if ($showCancellationReason)
          <th>CXL Date</th>
          <th>Cancellation Reason</th>
        @endif
        <th class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($rows as $enquiry)
        <tr>
          <td class="text-nowrap fw-semibold">{{ $enquiry->hotel?->name ?: '—' }}</td>
          <td class="text-nowrap">{{ $enquiry->enquiry_date?->format('d M Y') ?? '—' }}</td>
          <td class="text-nowrap">{{ $enquiry->response_date?->format('d M Y') ?? '—' }}</td>
          <td><x-date-alert :date="$enquiry->check_in" :window="14" /></td>
          <td class="text-nowrap">{{ $enquiry->check_out?->format('d M Y') ?? '—' }}</td>
          <td class="text-nowrap">{{ $enquiry->day ?: '—' }}</td>
          <td>{{ $enquiry->nights ?? '—' }}</td>
          <td>{{ $enquiry->rooms_per_night ?? '—' }}</td>
          <td class="fw-semibold text-nowrap"><a href="{{ route($recordRoute, array_merge(['enquiry' => $enquiry], $recordQuery)) }}">{{ $enquiry->group_name ?: '—' }}</a></td>
          <td class="text-nowrap">{{ $enquiry->ref ?: '—' }}</td>
          <td class="text-nowrap">{{ $enquiry->email ?: '—' }}</td>
          <td><x-badge-status :status="$enquiry->status" /></td>
          <td>{{ $enquiry->single_rooms ?? '—' }}</td>
          <td class="text-nowrap">{{ $money($enquiry->single_rate) }}</td>
          <td>{{ $enquiry->double_rooms ?? '—' }}</td>
          <td class="text-nowrap">{{ $money($enquiry->double_rate) }}</td>
          <td>{{ $enquiry->triple_rooms ?? '—' }}</td>
          <td class="text-nowrap">{{ $money($enquiry->triple_rate) }}</td>
          <td class="text-nowrap">{{ $enquiry->basis ?: '—' }}</td>
          <td><x-date-alert :date="$enquiry->option_date" scale="option" /></td>
          <td style="max-width:160px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $enquiry->cxl_policy }}">{{ $enquiry->cxl_policy ?: '—' }}</td>
          <td><x-date-alert :date="$enquiry->cxl_due_date" :window="30" /></td>
          <td class="text-nowrap fw-semibold">{{ $money($enquiry->total_revenue) }}</td>
          <td style="max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $enquiry->remarks }}">{{ $enquiry->remarks ?: '—' }}</td>
          @if ($showCancellationReason)
            <td class="text-nowrap">{{ $enquiry->cxl_date?->format('d M Y') ?? '—' }}</td>
            <td style="max-width:220px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $enquiry->cancellation_reason }}">{{ $enquiry->cancellation_reason ?: '—' }}</td>
          @endif
          <td class="text-end text-nowrap">
            @can('view', $enquiry)
              <a href="{{ route($recordRoute, array_merge(['enquiry' => $enquiry], $recordQuery)) }}" class="btn btn-sm btn-outline-secondary" title="View">
                <i class="bi bi-eye"></i>
              </a>
            @endcan
            @unless ($readOnly)
              @can('update', $enquiry)
                <a href="{{ route('enquiries.show', $enquiry) }}#client-response" class="btn btn-sm btn-outline-secondary" title="Client response for {{ $enquiry->group_name }}">
                  <i class="bi bi-chat-left-text"></i>
                </a>
                <a href="{{ route($editRoute, $enquiry) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                  <i class="bi bi-pencil"></i>
                </a>
              @endcan
              @can('delete', $enquiry)
                <form method="POST" action="{{ route('enquiries.destroy', $enquiry) }}" class="d-inline" data-confirm-title="Delete enquiry" data-confirm="{{ sprintf("Are you sure you want to delete enquiry \"%s\"?\n\nThis will permanently remove it and cannot be undone.", $enquiry->group_name ?? 'this enquiry') }}" data-confirm-button="Delete">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              @endcan
            @endunless
          </td>
        </tr>
      @empty
        <tr><td colspan="{{ $showCancellationReason ? 26 : 24 }}" class="text-center text-secondary py-4">{{ $emptyMessage }}</td></tr>
      @endforelse
    </tbody>
  </table>
@else
  <table class="table table-hover table-sm mb-0" style="font-size:0.78rem">
    <thead>
      <tr>
        <x-sortable-th column="ref" label="Ref" :default="$defaultSort" :default-dir="$defaultDir" />
        <x-sortable-th column="group_name" label="Group" :default="$defaultSort" :default-dir="$defaultDir" />
        <x-sortable-th column="check_in" label="Arrival date" :default="$defaultSort" :default-dir="$defaultDir" />
        <x-sortable-th column="check_out" label="End date" :default="$defaultSort" :default-dir="$defaultDir" />
        <x-sortable-th column="days" label="Days" :default="$defaultSort" :default-dir="$defaultDir" />
        <x-sortable-th column="nights" label="Nights" :default="$defaultSort" :default-dir="$defaultDir" />
        <th>Breakdown</th>
        <th>Client</th>
        <th>Mobile no</th>
        <th>Email ID</th>
        <th>Source</th>
        <th>Service person</th>
        <th>Subject</th>
        <th>Booking msg</th>
        <th>Adults price</th>
        <th>Child price</th>
        <th>Adults extra</th>
        <th>Child extra</th>
        <th>Total no pax</th>
        <th>Agent price</th>
        <th>Our cost</th>
        <th>P. price</th>
        <th>GST policy</th>
        <x-sortable-th column="total_price" label="Total price" :default="$defaultSort" :default-dir="$defaultDir" />
        <th>Net price</th>
        <th>Advance</th>
        <th>Remaining</th>
        <th>Agent comm %</th>
        <th>Agent comm amt</th>
        <th>Payable to agent</th>
        <th>Service total</th>
        <th>Total tax</th>
        <x-sortable-th column="grand_total" label="Grand total" :default="$defaultSort" :default-dir="$defaultDir" />
        <x-sortable-th column="status" label="Status" :default="$defaultSort" :default-dir="$defaultDir" />
        <th class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($rows as $enquiry)
        <tr>
          <td class="fw-semibold text-nowrap"><a href="{{ route('enquiries.show', $enquiry) }}">{{ $enquiry->ref ?: '—' }}</a></td>
          <td class="text-nowrap">{{ $enquiry->group_name }}</td>
          <td class="text-nowrap">{{ $enquiry->check_in?->format('d M Y') ?? '—' }}</td>
          <td class="text-nowrap">{{ $enquiry->check_out?->format('d M Y') ?? '—' }}</td>
          <td>{{ $enquiry->days ?? $enquiry->nights ?? '—' }}</td>
          <td>{{ $enquiry->nights ?? '—' }}</td>
          <td style="max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $enquiry->breakdown }}">{{ $enquiry->breakdown ?: '—' }}</td>
          <td class="text-nowrap">{{ $enquiry->client ?: '—' }}</td>
          <td class="text-nowrap">{{ $enquiry->mobile ?: '—' }}</td>
          <td class="text-nowrap">{{ $enquiry->email ?: '—' }}</td>
          <td class="text-nowrap">{{ $enquiry->source ?: '—' }}</td>
          <td class="text-nowrap">{{ $enquiry->service_person ?: '—' }}</td>
          <td class="text-nowrap">{{ $enquiry->subject ?: '—' }}</td>
          <td style="max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $enquiry->booking_msg }}">{{ $enquiry->booking_msg ?: '—' }}</td>
          <td class="text-nowrap">£{{ number_format((float) ($enquiry->adults_price ?? 0), 2) }}</td>
          <td class="text-nowrap">£{{ number_format((float) ($enquiry->child_price ?? 0), 2) }}</td>
          <td class="text-nowrap">£{{ number_format((float) ($enquiry->adults_extra ?? 0), 2) }}</td>
          <td class="text-nowrap">£{{ number_format((float) ($enquiry->child_extra ?? 0), 2) }}</td>
          <td>{{ $enquiry->total_pax ?? 0 }}</td>
          <td class="text-nowrap">£{{ number_format((float) ($enquiry->agent_price ?? 0), 2) }}</td>
          <td class="text-nowrap">£{{ number_format((float) ($enquiry->our_cost ?? 0), 2) }}</td>
          <td class="text-nowrap">£{{ number_format((float) ($enquiry->package_price ?? 0), 2) }}</td>
          <td class="text-nowrap">{{ $enquiry->gst_policy ?: '—' }}</td>
          <td class="text-nowrap">£{{ number_format((float) ($enquiry->total_price ?? 0), 2) }}</td>
          <td class="text-nowrap">£{{ number_format((float) ($enquiry->net_price ?? 0), 2) }}</td>
          <td class="text-nowrap">£{{ number_format((float) ($enquiry->advance ?? 0), 2) }}</td>
          <td class="text-nowrap">£{{ number_format((float) ($enquiry->remaining ?? 0), 2) }}</td>
          <td>{{ number_format((float) ($enquiry->agent_comm_percent ?? 0), 2) }}%</td>
          <td class="text-nowrap">£{{ number_format((float) ($enquiry->agent_comm_amount ?? 0), 2) }}</td>
          <td class="text-nowrap">£{{ number_format((float) ($enquiry->payable_to_agent ?? 0), 2) }}</td>
          <td class="text-nowrap">£{{ number_format((float) ($enquiry->service_total ?? 0), 2) }}</td>
          <td class="text-nowrap">£{{ number_format((float) ($enquiry->total_tax ?? 0), 2) }}</td>
          <td class="text-nowrap fw-semibold">£{{ number_format((float) ($enquiry->grand_total ?? 0), 2) }}</td>
          <td><x-badge-status :status="$enquiry->status" /></td>
          <td class="text-end text-nowrap">
            @can('view', $enquiry)
              <a href="{{ route('enquiries.show', $enquiry) }}" class="btn btn-sm btn-outline-secondary" title="View">
                <i class="bi bi-eye"></i>
              </a>
            @endcan
            @can('update', $enquiry)
              <a href="{{ route('enquiries.show', $enquiry) }}#client-response" class="btn btn-sm btn-outline-secondary" title="Client response for {{ $enquiry->group_name }}">
                <i class="bi bi-chat-left-text"></i>
              </a>
              <a href="{{ route('enquiries.edit', $enquiry) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                <i class="bi bi-pencil"></i>
              </a>
            @endcan
            @can('delete', $enquiry)
              <form method="POST" action="{{ route('enquiries.destroy', $enquiry) }}" class="d-inline" data-confirm-title="Delete enquiry" data-confirm="{{ sprintf("Are you sure you want to delete enquiry \"%s\"?\n\nThis will permanently remove it and cannot be undone.", $enquiry->group_name ?? 'this enquiry') }}" data-confirm-button="Delete">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            @endcan
          </td>
        </tr>
      @empty
        <tr><td colspan="36" class="text-center text-secondary py-4">{{ $emptyMessage }}</td></tr>
      @endforelse
    </tbody>
  </table>
@endif
</div>
