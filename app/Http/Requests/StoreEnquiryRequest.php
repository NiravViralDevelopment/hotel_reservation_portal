<?php

namespace App\Http\Requests;

use App\Enums\EnquiryStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEnquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('ref') === '') {
            $this->merge(['ref' => null]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'ref' => ['nullable', 'string', 'max:255', 'unique:enquiries,ref'],
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'enquiry_date' => ['nullable', 'date'],
            'response_date' => ['nullable', 'date'],
            'group_name' => ['required', 'string', 'max:255', Rule::unique('enquiries', 'group_name')],
            'travel_agency_id' => ['nullable', 'integer', 'exists:travel_agencies,id'],
            'hotel_id' => ['nullable', 'integer', Rule::in(\App\Support\HotelAccess::hotelIds())],
            'nights' => ['nullable', 'integer', 'min:1'],
            'rooms_per_night' => ['nullable', 'integer', 'min:0'],
            'single_rooms' => ['nullable', 'integer', 'min:0'],
            'single_rate' => ['nullable', 'numeric', 'min:0'],
            'double_rooms' => ['nullable', 'integer', 'min:0'],
            'double_rate' => ['nullable', 'numeric', 'min:0'],
            'triple_rooms' => ['nullable', 'integer', 'min:0'],
            'triple_rate' => ['nullable', 'numeric', 'min:0'],
            'total_revenue' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', Rule::in(EnquiryStatus::values())],
            'email' => ['nullable', 'email', 'max:255'],
            'remarks' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'group_name.unique' => 'This group name is already used. Enter a different name.',
        ];
    }
}
