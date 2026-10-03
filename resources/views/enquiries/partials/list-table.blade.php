@php
  $rows = $rows ?? collect();
  $defaultSort = $defaultSort ?? 'enquiry_date';
  $defaultDir = $defaultDir ?? 'desc';
  $emptyMessage = $emptyMessage ?? 'No records found.';
@endphp

<div class="table-scroll-hint"><i class="bi bi-arrows-expand"></i> Scroll horizontally to see all columns</div>
<div class="table-wrapper table-scroll-wide table-scroll-enquiries">
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
</div>
