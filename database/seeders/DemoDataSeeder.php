<?php

namespace Database\Seeders;

use App\Models\BobMonthlySnapshot;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Enquiry;
use App\Models\Hotel;
use App\Models\Setting;
use App\Models\TravelAgency;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = $this->seedUser(
            'admin@hotelgroup.co.uk',
            [
                'name' => 'Richard Whitmore',
                'phone' => '+44 20 7946 0000',
                'job_title' => 'Group Reservations Manager',
                'department' => 'Reservations',
                'status' => 'active',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );
        $admin->syncRoles(['Administrator']);

        $users = [
            ['email' => 's.mitchell@grandbrighton.co.uk', 'name' => 'Sarah Mitchell', 'job_title' => 'Hotel Manager', 'department' => 'Operations'],
            ['email' => 'e.richardson@lakemanor.co.uk', 'name' => 'Emma Richardson', 'job_title' => 'Reservations Coordinator', 'department' => 'Reservations'],
            ['email' => 'l.green@hotelgroup.co.uk', 'name' => 'Laura Green', 'job_title' => 'Finance Manager', 'department' => 'Finance'],
            ['email' => 'c.bennett@yorkminsterinn.co.uk', 'name' => 'Claire Bennett', 'job_title' => 'Reservations Coordinator', 'department' => 'Reservations'],
        ];

        foreach ($users as $row) {
            $user = $this->seedUser(
                $row['email'],
                [
                    'name' => $row['name'],
                    'job_title' => $row['job_title'],
                    'department' => $row['department'],
                    'status' => 'active',
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                ]
            );
            $user->syncRoles(['User']);
        }

        $heritage = Company::query()->updateOrCreate(
            ['reg_number' => '08472931'],
            [
                'name' => 'Heritage Hotel Group Ltd',
                'vat_number' => 'GB847293110',
                'city' => 'London',
                'country' => 'United Kingdom',
                'address' => '45 Berkeley Square, London, W1J 5AZ',
                'registered_address' => '45 Berkeley Square, London, W1J 5AZ',
                'trading_address' => '45 Berkeley Square, London, W1J 5AZ',
                'status' => 'active',
            ]
        );

        $coastal = Company::query()->updateOrCreate(
            ['reg_number' => '09234567'],
            [
                'name' => 'Coastal Properties UK Ltd',
                'vat_number' => 'GB923456789',
                'city' => 'Brighton',
                'country' => 'United Kingdom',
                'registered_address' => '12 Marine Parade, Brighton, BN2 1TL',
                'status' => 'active',
            ]
        );

        $scottish = Company::query()->updateOrCreate(
            ['reg_number' => 'SC456789'],
            [
                'name' => 'Scottish Hospitality Holdings',
                'vat_number' => 'GB456789012',
                'city' => 'Edinburgh',
                'country' => 'United Kingdom',
                'registered_address' => '8 Princes Street, Edinburgh, EH2 2AN',
                'status' => 'active',
            ]
        );

        $hotelsData = [
            ['code' => 'GBH01', 'name' => 'Grand Brighton Hotel', 'city' => 'Brighton', 'rooms' => 186, 'company' => $coastal, 'manager' => 'Sarah Mitchell', 'phone' => '+44 1273 224300', 'email' => 'sarah.mitchell@grandbrighton.co.uk'],
            ['code' => 'ECV01', 'name' => 'Edinburgh Castle View', 'city' => 'Edinburgh', 'rooms' => 142, 'company' => $scottish, 'manager' => 'Andrew Fraser', 'phone' => '+44 131 556 7890', 'email' => 'a.fraser@edinburghcastleview.co.uk'],
            ['code' => 'LDM01', 'name' => 'Lake District Manor', 'city' => 'Windermere', 'rooms' => 98, 'company' => $heritage, 'manager' => 'Emma Richardson', 'phone' => '+44 15394 45678', 'email' => 'e.richardson@lakemanor.co.uk'],
            ['code' => 'BRC01', 'name' => 'Bath Royal Crescent', 'city' => 'Bath', 'rooms' => 76, 'company' => $heritage, 'manager' => 'James Holloway', 'phone' => '+44 1225 463000', 'email' => 'j.holloway@bathcrescent.co.uk'],
            ['code' => 'YMI01', 'name' => 'York Minster Inn', 'city' => 'York', 'rooms' => 84, 'company' => $heritage, 'manager' => 'Claire Bennett', 'phone' => '+44 1904 621000', 'email' => 'c.bennett@yorkminsterinn.co.uk'],
        ];

        $hotels = [];
        foreach ($hotelsData as $h) {
            $hotels[$h['code']] = Hotel::query()->updateOrCreate(
                ['code' => $h['code']],
                [
                    'company_id' => $h['company']->id,
                    'name' => $h['name'],
                    'city' => $h['city'],
                    'country' => 'United Kingdom',
                    'rooms' => $h['rooms'],
                    'manager_name' => $h['manager'],
                    'phone' => $h['phone'],
                    'email' => $h['email'],
                    'status' => 'active',
                ]
            );
        }

        // Assign demo hotel access for non-admin users (Administrators see all hotels via role).
        User::query()->where('email', 's.mitchell@grandbrighton.co.uk')->first()?->hotels()->sync([
            $hotels['GBH01']->id,
        ]);
        User::query()->where('email', 'e.richardson@lakemanor.co.uk')->first()?->hotels()->sync([
            $hotels['LDM01']->id,
            $hotels['BRC01']->id,
        ]);
        User::query()->where('email', 'c.bennett@yorkminsterinn.co.uk')->first()?->hotels()->sync([
            $hotels['YMI01']->id,
            $hotels['ECV01']->id,
        ]);
        User::query()->where('email', 'l.green@hotelgroup.co.uk')->first()?->hotels()->sync([
            $hotels['GBH01']->id,
            $hotels['LDM01']->id,
            $hotels['BRC01']->id,
            $hotels['YMI01']->id,
            $hotels['ECV01']->id,
        ]);

        $agenciesData = [
            ['code' => 'TUI', 'name' => 'TUI UK', 'contact' => 'James Whitfield', 'email' => 'j.whitfield@tui.co.uk', 'phone' => '+44 1733 419999', 'city' => 'Luton'],
            ['code' => 'JET2', 'name' => 'Jet2holidays', 'contact' => 'Michelle Turner', 'email' => 'm.turner@jet2holidays.com', 'phone' => '+44 113 496 0000', 'city' => 'Leeds'],
            ['code' => 'RIV', 'name' => 'Riviera Travel', 'contact' => 'Sarah Connolly', 'email' => 's.connolly@rivieratravel.co.uk', 'phone' => '+44 1582 798 000', 'city' => 'Burton-on-Trent'],
            ['code' => 'TTN', 'name' => 'Titan Travel', 'contact' => 'Robert Hughes', 'email' => 'r.hughes@titantravel.co.uk', 'phone' => '+44 800 988 5823', 'city' => 'Redhill'],
            ['code' => 'LEG', 'name' => 'Leger Holidays', 'contact' => 'David Pemberton', 'email' => 'd.pemberton@leger.co.uk', 'phone' => '+44 1709 787 000', 'city' => 'Rotherham'],
            ['code' => 'SAG', 'name' => 'Saga Holidays', 'contact' => 'Helen Marsh', 'email' => 'h.marsh@saga.co.uk', 'phone' => '+44 808 252 0000', 'city' => 'Folkestone'],
        ];

        $agencies = [];
        foreach ($agenciesData as $a) {
            $agencies[$a['code']] = TravelAgency::query()->updateOrCreate(
                ['code' => $a['code']],
                [
                    'name' => $a['name'],
                    'contact_name' => $a['contact'],
                    'email' => $a['email'],
                    'phone' => $a['phone'],
                    'city' => $a['city'],
                    'country' => 'United Kingdom',
                    'status' => 'active',
                ]
            );
        }

        Contact::query()->updateOrCreate(
            ['email' => 'j.whitfield@tui.co.uk'],
            [
                'company_id' => $heritage->id,
                'travel_agency_id' => $agencies['TUI']->id,
                'name' => 'James Whitfield',
                'phone' => '+44 1733 419999',
                'position' => 'Group Sales Manager',
                'country' => 'United Kingdom',
                'notes' => 'Primary contact for summer programmes',
            ]
        );

        Contact::query()->updateOrCreate(
            ['email' => 'h.marsh@saga.co.uk'],
            [
                'company_id' => $heritage->id,
                'travel_agency_id' => $agencies['SAG']->id,
                'name' => 'Helen Marsh',
                'phone' => '+44 808 252 0000',
                'position' => 'Over 50s Programme Lead',
                'country' => 'United Kingdom',
            ]
        );

        Enquiry::query()->updateOrCreate(
            ['ref' => 'ENQ-2026-0318'],
            [
                'year' => (int) now()->format('Y'),
                'enquiry_date' => now()->toDateString(),
                'day' => now()->format('l'),
                'nights' => 4,
                'group_name' => 'Autumn Colours Tour — 65 pax',
                'travel_agency_id' => $agencies['SAG']->id,
                'hotel_id' => $hotels['ECV01']->id,
                'assigned_to' => $admin->id,
                'rooms_per_night' => 35,
                'basis' => 'HB',
                'total_revenue' => 24500,
                'status' => 'Chesed',
                'email' => 'h.marsh@saga.co.uk',
                'option_date' => now()->addDays(14)->toDateString(),
            ]
        );

        Enquiry::query()->updateOrCreate(
            ['ref' => 'ENQ-2026-0312'],
            [
                'year' => (int) now()->format('Y'),
                'enquiry_date' => now()->subDay()->toDateString(),
                'nights' => 3,
                'group_name' => 'Garden Tour — Cotswolds May 2027',
                'travel_agency_id' => $agencies['TTN']->id,
                'hotel_id' => $hotels['BRC01']->id,
                'assigned_to' => $admin->id,
                'rooms_per_night' => 30,
                'basis' => 'HB',
                'total_revenue' => 15300,
                'status' => 'Quoted',
                'email' => 'r.hughes@titantravel.co.uk',
            ]
        );

        $defaults = [
            ['company', 'name', 'Heritage Hotel Group Ltd'],
            ['company', 'number', '08472931'],
            ['company', 'address', '45 Berkeley Square, London, W1J 5AZ'],
            ['company', 'vat', 'GB 123 4567 89'],
            ['company', 'phone', '+44 20 7946 0958'],
            ['company', 'email', 'info@hotelgroup.co.uk'],
            ['hotel', 'check_in', '15:00'],
            ['hotel', 'check_out', '10:00'],
            ['hotel', 'rooming_deadline_days', '7'],
            ['hotel', 'default_meal_plan', 'HB'],
            ['email', 'from_email', 'bookings@hotelgroup.co.uk'],
            ['email', 'from_name', 'Hotel Group Bookings'],
            ['email', 'send_confirmation', '1'],
            ['email', 'send_reminder', '1'],
            ['general', 'currency', 'GBP'],
            ['general', 'date_format', 'DD/MM/YYYY'],
            ['general', 'timezone', 'Europe/London'],
            ['general', 'fy_start', 'January'],
            ['general', 'dark_mode', '1'],
        ];

        foreach ($defaults as [$group, $key, $value]) {
            Setting::set($group, $key, $value);
        }

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $values = [198400, 215600, 248900, 312400, 389200, 425800, 478200, 512600, 398400, 356200, 278900, 232050];
        foreach ($months as $i => $label) {
            BobMonthlySnapshot::query()->updateOrCreate(
                ['year' => (int) now()->format('Y'), 'month' => $i + 1],
                [
                    'label' => $label,
                    'bob_current' => $values[$i],
                    'bob_previous' => (int) ($values[$i] * 0.92),
                    'bob_variance' => (int) ($values[$i] * 0.08),
                    'stly_bob' => (int) ($values[$i] * 0.9),
                    'adr_current' => 154.65,
                    'adr_previous' => 148.20,
                    'adr_variance' => 6.45,
                    'adr_stly' => 146.00,
                    'room_nights_current' => (int) ($values[$i] / 155),
                    'room_nights_previous' => (int) ($values[$i] / 160),
                    'stly_room_nights' => (int) ($values[$i] / 162),
                    'breakfast_revenue' => (int) ($values[$i] * 0.12),
                    'dinner_revenue' => (int) ($values[$i] * 0.18),
                    'dinner_covers' => (int) ($values[$i] / 45),
                ]
            );
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function seedUser(string $email, array $attributes): User
    {
        $user = User::query()->firstOrNew(['email' => $email]);
        $user->fill($attributes);

        if (blank($user->uuid)) {
            $user->uuid = (string) Str::uuid();
        }

        $user->save();

        return $user;
    }
}
