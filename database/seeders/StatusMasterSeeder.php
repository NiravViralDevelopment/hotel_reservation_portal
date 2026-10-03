<?php

namespace Database\Seeders;

use App\Models\StatusMaster;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StatusMasterSeeder extends Seeder
{
    /**
     * Enquiry pipeline statuses only.
     * confirmed / cancelled are booking-action values on enquiries, not status master rows.
     *
     * @var list<string>
     */
    private array $titles = [
        'Quoted',
        'Lost',
        'Chesed',
    ];

    public function run(): void
    {
        $now = now();

        foreach ($this->titles as $title) {
            $existing = StatusMaster::query()
                ->whereRaw('LOWER(title) = ?', [mb_strtolower($title)])
                ->first();

            if ($existing) {
                $existing->fill([
                    'title' => $title,
                    'status' => 'active',
                ]);
                if (blank($existing->uuid)) {
                    $existing->uuid = (string) Str::uuid();
                }
                $existing->save();
            } else {
                StatusMaster::query()->create([
                    'title' => $title,
                    'status' => 'active',
                ]);
            }
        }

        // Remap legacy pipeline statuses only — do not touch confirmed/cancelled enquiries.
        $remap = [
            'quoted' => 'Quoted',
            'lost' => 'Lost',
            'follow_up' => 'Chesed',
            'follow up' => 'Chesed',
            'chesed' => 'Chesed',
            'new' => 'Chesed',
        ];

        if (DB::getSchemaBuilder()->hasTable('enquiries')) {
            foreach ($remap as $from => $to) {
                DB::table('enquiries')
                    ->whereRaw('LOWER(status) = ?', [$from])
                    ->update([
                        'status' => $to,
                        'updated_at' => $now,
                    ]);
            }
        }

        // Keep only Quoted / Lost / Chesed in status masters.
        StatusMaster::query()
            ->whereNotIn('title', $this->titles)
            ->delete();
    }
}
