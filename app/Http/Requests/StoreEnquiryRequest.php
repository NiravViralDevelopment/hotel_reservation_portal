<?php

namespace App\Http\Requests;

use App\Support\EnquiryFieldRules;
use Illuminate\Foundation\Http\FormRequest;

class StoreEnquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $nullable = [];
        foreach (['ref', 'day', 'basis', 'cxl_policy', 'remarks', 'status', 'option_date', 'single_from_date', 'single_to_date', 'double_from_date', 'double_to_date', 'triple_from_date', 'triple_to_date'] as $field) {
            if ($this->input($field) === '') {
                $nullable[$field] = null;
            }
        }

        if ($nullable !== []) {
            $this->merge($nullable);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return EnquiryFieldRules::create(null, $this->all());
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'enquiry_date.required' => 'Enquiry date is required.',
            'response_date.required' => 'Response date is required.',
            'response_date.after_or_equal' => 'Response date cannot be before enquiry date.',
            'check_in.required' => 'Arrival date is required.',
            'check_in.after_or_equal' => 'Arrival date cannot be before today.',
            'check_out.required' => 'Departure date is required.',
            'check_out.after' => 'Departure date must be after the arrival date.',
            'nights.required' => 'Nights is required.',
            'nights.min' => 'Nights must be at least 1.',
            'group_name.required' => 'Group name is required.',
            'rooms_per_night.required' => 'Total room per night is required.',
            'email.required' => 'Email ID is required.',
            'email.email' => 'Enter a valid email address.',
            'ref.unique' => 'This reference is already used. Enter a different one.',
            'status.exists' => 'Select a valid active status.',
            'basis.in' => 'Select a valid basis.',
        ] + EnquiryFieldRules::roomPeriodMessages();
    }
}
