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
     * From and to dates for each room type, kept inside the stay.
     *
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public static function roomPeriodRules(array $input = []): array
    {
        $rules = [];

        foreach (['single', 'double', 'triple'] as $type) {
            $to = ['nullable', 'date', 'after_or_equal:check_in', 'before_or_equal:check_out'];
            if (! empty($input[$type.'_from_date'])) {
                $to[] = 'after_or_equal:'.$type.'_from_date';
            }

            $rules[$type.'_from_date'] = ['nullable', 'date', 'after_or_equal:check_in', 'before_or_equal:check_out'];
            $rules[$type.'_to_date'] = $to;
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public static function roomPeriodMessages(): array
    {
        $messages = [];

        foreach (['single' => 'Single', 'double' => 'Double', 'triple' => 'Triple'] as $type => $label) {
            $messages[$type.'_from_date.required_with'] = 'Enter the '.$label.' from date.';
            $messages[$type.'_from_date.after_or_equal'] = $label.' from date must be on or after the arrival date.';
            $messages[$type.'_from_date.before_or_equal'] = $label.' from date must be on or before the departure date.';
            $messages[$type.'_to_date.required_with'] = 'Enter the '.$label.' to date.';
            $messages[$type.'_to_date.after_or_equal'] = $label.' to date must be on or after the from date, and not before the arrival date.';
            $messages[$type.'_to_date.before_or_equal'] = $label.' to date must be on or before the departure date.';
        }

        return $messages;
    }

    /**
     * Create-enquiry fields only.
     *
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public static function create(?int $ignoreEnquiryId = null, array $input = []): array
    {
        $groupName = trim((string) ($input['group_name'] ?? ''));
        $refValue = trim((string) ($input['ref'] ?? ''));

        $groupNameRules = ['required', 'string', 'max:255'];
        $refRules = ['required', 'string', 'max:255'];

        if ($groupName !== '' && $refValue !== '') {
            $groupUnique = Rule::unique('enquiries', 'group_name')
                ->where(fn ($query) => $query->where('ref', $refValue));
            $refUnique = Rule::unique('enquiries', 'ref')
                ->where(fn ($query) => $query->where('group_name', $groupName));

            if ($ignoreEnquiryId) {
                $groupUnique->ignore($ignoreEnquiryId);
                $refUnique->ignore($ignoreEnquiryId);
            }

            $groupNameRules[] = $groupUnique;
            $refRules[] = $refUnique;
        }

        return array_merge([
            'enquiry_date' => ['required', 'date'],
            'response_date' => ['required', 'date', 'after_or_equal:enquiry_date'],
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'day' => ['nullable', 'string', 'max:20'],
            'nights' => ['required', 'integer', 'min:1'],
            'group_name' => $groupNameRules,
            'ref' => $refRules,
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
            'cxl_due_date' => ['nullable', 'date'],
            'email' => ['required', 'email', 'max:255'],
            'remarks' => ['nullable', 'string'],
            'status' => [
                'nullable',
                'string',
                'max:255',
                Rule::exists('status_masters', 'title')->where(fn ($query) => $query->where('status', 'active')),
            ],
        ], self::roomPeriodRules($input), self::dailyRoomRules());
    }

    /**
     * @return array<string, mixed>
     */
    public static function base(?int $ignoreEnquiryId = null, ?int $currentHotelId = null): array
    {
        $ref = ['nullable', 'string', 'max:255', 'unique:enquiries,ref'];

        if ($ignoreEnquiryId) {
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
            'group_name' => ['required', 'string', 'max:255'],
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

    /**
     * @return array<string, mixed>
     */
    public static function dailyRoomRules(): array
    {
        $room = ['nullable', 'integer', 'min:0'];
        $rate = ['nullable', 'numeric', 'min:0'];

        return [
            'daily_rooms' => ['nullable', 'array', 'max:400'],
            'daily_rooms.*.single_rooms' => $room,
            'daily_rooms.*.single_rate' => $rate,
            'daily_rooms.*.double_rooms' => $room,
            'daily_rooms.*.double_rate' => $rate,
            'daily_rooms.*.triple_rooms' => $room,
            'daily_rooms.*.triple_rate' => $rate,
        ];
    }
}
