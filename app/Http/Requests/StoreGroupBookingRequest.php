<?php

namespace App\Http\Requests;

use App\Enums\BookingStatus;
use App\Enums\PaymentDisplayStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGroupBookingRequest extends FormRequest
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
        return [
            'block_id' => ['required', 'string', 'max:255', 'unique:group_bookings,block_id'],
            'enquiry_id' => ['nullable', 'integer', 'exists:enquiries,id'],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'hotel_id' => ['nullable', 'integer', 'exists:hotels,id'],
            'travel_agency_id' => ['nullable', 'integer', 'exists:travel_agencies,id'],
            'contact_id' => ['nullable', 'integer', 'exists:contacts,id'],
            'group_name' => ['required', 'string', 'max:255'],
            'client' => ['nullable', 'string', 'max:255'],
            'agency_name' => ['nullable', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'arrival' => ['required', 'date'],
            'departure' => ['required', 'date', 'after:arrival'],
            'nights' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', Rule::in(BookingStatus::values())],
            'payment_status_display' => ['nullable', Rule::in(PaymentDisplayStatus::values())],
            'revenue' => ['nullable', 'numeric', 'min:0'],
            'rooms' => ['nullable', 'integer', 'min:0'],
            'pax' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
