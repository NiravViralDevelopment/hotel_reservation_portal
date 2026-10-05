<?php

namespace App\Support;

use Illuminate\Validation\Rule;

class EnquiryFieldRules
{
    /**
     * Shared enquiry form rules (create/update).
     *
     * @return array<string, mixed>
     */
    public static function commercial(): array
    {
        return [
            'days' => ['nullable', 'integer', 'min:0'],
            'breakdown' => ['nullable', 'string'],
            'client' => ['nullable', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:30'],
            'source' => ['nullable', 'string', 'max:255'],
            'service_person' => ['nullable', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'booking_msg' => ['nullable', 'string'],
            'adults_price' => ['nullable', 'numeric', 'min:0'],
            'child_price' => ['nullable', 'numeric', 'min:0'],
            'adults_extra' => ['nullable', 'numeric', 'min:0'],
            'child_extra' => ['nullable', 'numeric', 'min:0'],
            'total_pax' => ['nullable', 'integer', 'min:0'],
            'agent_price' => ['nullable', 'numeric', 'min:0'],
            'our_cost' => ['nullable', 'numeric', 'min:0'],
            'package_price' => ['nullable', 'numeric', 'min:0'],
            'gst_policy' => ['nullable', 'string', 'max:255'],
            'total_price' => ['nullable', 'numeric', 'min:0'],
            'net_price' => ['nullable', 'numeric', 'min:0'],
            'advance' => ['nullable', 'numeric', 'min:0'],
            'remaining' => ['nullable', 'numeric', 'min:0'],
            'agent_comm_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'agent_comm_amount' => ['nullable', 'numeric', 'min:0'],
            'payable_to_agent' => ['nullable', 'numeric', 'min:0'],
            'service_total' => ['nullable', 'numeric', 'min:0'],
            'total_tax' => ['nullable', 'numeric', 'min:0'],
            'grand_total' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * Create-enquiry fields only.
     *
     * @return array<string, mixed>
     */
    public static function create(?int $ignoreEnquiryId = null): array
    {
        $groupName = ['required', 'string', 'max:255', Rule::unique('enquiries', 'group_name')];
        $ref = ['nullable', 'string', 'max:255', 'unique:enquiries,ref'];

        if ($ignoreEnquiryId) {
            $groupName = ['required', 'string', 'max:255', Rule::unique('enquiries', 'group_name')->ignore($ignoreEnquiryId)];
            $ref = ['nullable', 'string', 'max:255', 'unique:enquiries,ref,'.$ignoreEnquiryId];
        }

        return [
            'enquiry_date' => ['required', 'date'],
            'response_date' => ['required', 'date', 'after_or_equal:enquiry_date'],
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'day' => ['nullable', 'string', 'max:20'],
            'nights' => ['required', 'integer', 'min:1'],
            'group_name' => $groupName,
            'ref' => $ref,
            'rooms_per_night' => ['required', 'integer', 'min:0'],
            'single_rooms' => ['nullable', 'integer', 'min:0'],
            'single_rate' => ['nullable', 'numeric', 'min:0'],
            'double_rooms' => ['nullable', 'integer', 'min:0'],
            'double_rate' => ['nullable', 'numeric', 'min:0'],
            'triple_rooms' => ['nullable', 'integer', 'min:0'],
            'triple_rate' => ['nullable', 'numeric', 'min:0'],
            'basis' => ['nullable', 'string', Rule::in(['BB', 'DBB', 'HB', 'FB', 'RO'])],
            'total_revenue' => ['nullable', 'numeric', 'min:0'],
            'cxl_policy' => ['nullable', 'string', 'max:255'],
            'option_date' => ['nullable', 'date'],
            'email' => ['required', 'email', 'max:255'],
            'remarks' => ['nullable', 'string'],
            'status' => [
                'nullable',
                'string',
                'max:255',
                Rule::exists('status_masters', 'title')->where(fn ($query) => $query->where('status', 'active')),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function base(?int $ignoreEnquiryId = null, ?int $currentHotelId = null): array
    {
        $groupName = ['required', 'string', 'max:255', Rule::unique('enquiries', 'group_name')];
        $ref = ['nullable', 'string', 'max:255', 'unique:enquiries,ref'];

        if ($ignoreEnquiryId) {
            $groupName = ['required', 'string', 'max:255', Rule::unique('enquiries', 'group_name')->ignore($ignoreEnquiryId)];
            $ref = ['nullable', 'string', 'max:255', 'unique:enquiries,ref,'.$ignoreEnquiryId];
        }

        return array_merge([
            'ref' => $ref,
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'enquiry_date' => ['nullable', 'date'],
            'response_date' => ['nullable', 'date'],
            'check_in' => ['nullable', 'date'],
            'check_in_day' => ['nullable', 'string', 'max:20'],
            'check_out' => ['nullable', 'date', 'after:check_in'],
            'group_name' => $groupName,
            'travel_agency_id' => ['nullable', 'integer', 'exists:travel_agencies,id'],
            'hotel_id' => ['nullable', 'integer', Rule::in(HotelAccess::selectableHotelIds(null, $currentHotelId))],
            'nights' => ['nullable', 'integer', 'min:1'],
            'rooms_per_night' => ['nullable', 'integer', 'min:0'],
            'single_rooms' => ['nullable', 'integer', 'min:0'],
            'single_rate' => ['nullable', 'numeric', 'min:0'],
            'double_rooms' => ['nullable', 'integer', 'min:0'],
            'double_rate' => ['nullable', 'numeric', 'min:0'],
            'triple_rooms' => ['nullable', 'integer', 'min:0'],
            'triple_rate' => ['nullable', 'numeric', 'min:0'],
            'total_revenue' => ['nullable', 'numeric', 'min:0'],
            'has_tax' => ['sometimes', 'boolean'],
            'tax_percentage' => ['nullable', 'numeric', 'min:0', 'max:100', 'required_if:has_tax,1,true'],
            'tax_revenue' => ['nullable', 'numeric', 'min:0'],
            'status' => [
                'required',
                'string',
                'max:255',
                Rule::exists('status_masters', 'title')->where(fn ($query) => $query->where('status', 'active')),
            ],
            'email' => ['nullable', 'email', 'max:255'],
            'remarks' => ['nullable', 'string'],
        ], self::commercial());
    }
}
