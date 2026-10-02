<?php

namespace App\Http\Requests;

use App\Enums\BookingStatus;
use App\Enums\PaymentDisplayStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGroupBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $bookingId = $this->route('group_booking')?->id ?? $this->route('group_booking');

        return [
            'block_id' => ['required', 'string', 'max:255', Rule::unique('group_bookings', 'block_id')->ignore($bookingId)],
            'enquiry_id' => ['nullable', 'integer', 'exists:enquiries,id'],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'hotel_id' => ['nullable', 'integer', Rule::in(\App\Support\HotelAccess::hotelIds())],
            'travel_agency_id' => ['nullable', 'integer', 'exists:travel_agencies,id'],
            'group_name' => ['required', 'string', 'max:255'],
            'client' => ['nullable', 'string', 'max:255'],
            'agency_name' => ['nullable', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'arrival' => ['required', 'date'],
            'departure' => ['required', 'date', 'after:arrival'],
            'nights' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', Rule::in(BookingStatus::values())],
            'payment_term' => ['nullable', 'string', 'max:255'],
            'due_date' => ['nullable', 'date'],
            'payment_status' => ['nullable', 'string', 'max:255'],
            'payment_status_display' => ['nullable', Rule::in(PaymentDisplayStatus::values())],
            'cxl_policy' => ['nullable', 'string', 'max:255'],
            'cxl_due_date' => ['nullable', 'date'],
            'cxl_date' => ['nullable', 'date'],
            'commission' => ['nullable', 'numeric', 'min:0'],
            'revenue' => ['nullable', 'numeric', 'min:0'],
            'rooms' => ['nullable', 'integer', 'min:0'],
            'pax' => ['nullable', 'integer', 'min:0'],
            'internal_notes' => ['nullable', 'string'],
            'update_notes' => ['nullable', 'string'],
            'document_name' => ['nullable', 'string', 'max:255'],
            'document_file' => ['nullable', 'file', 'max:10240'],
        ];
    }
}
