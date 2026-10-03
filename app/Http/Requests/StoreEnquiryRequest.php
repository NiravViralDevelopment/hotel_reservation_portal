<?php

namespace App\Http\Requests;

use App\Support\EnquiryFieldRules;
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

        $this->merge([
            'has_tax' => $this->boolean('has_tax'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = EnquiryFieldRules::base();
        $rules['response_date'] = [
            'nullable',
            'date',
            Rule::when($this->filled('enquiry_date'), ['after_or_equal:enquiry_date']),
        ];

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'group_name.unique' => 'This group name is already used. Enter a different name.',
            'check_out.after' => 'Check-out must be after check-in.',
            'response_date.after_or_equal' => 'Response date cannot be before enquiry date.',
            'tax_percentage.required_if' => 'Enter the tax percentage.',
            'ref.unique' => 'This reference is already used. Enter a different one.',
            'status.required' => 'Please select a status.',
            'status.exists' => 'Select a valid active status.',
        ];
    }
}
