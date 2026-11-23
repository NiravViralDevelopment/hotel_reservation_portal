<?php

namespace App\Http\Controllers;

use App\Models\BobMonthlySnapshot;
use App\Models\Enquiry;
use App\Models\Hotel;
use App\Support\Audit;
use App\Support\BobColumns;
use App\Support\HotelAccess;
use App\Support\SimpleXlsxWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BobController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('bob.view');

        $hotels = Hotel::optionsForSelect($request->input('hotel_id'));
        $selectedHotel = $this->resolveHotel($request, $hotels);
        $selectedYear = $this->selectedYear($request);
        $previousYear = $selectedYear - 1;
        $columns = BobColumns::forYear($selectedYear);
        $showHotelFilter = HotelAccess::canAccessAllHotels() || $hotels->count() > 1;

        $months = collect();
        $totals = [];
        if ($selectedHotel !== null) {
            $months = $this->months($selectedYear, $selectedHotel->id);
            $totals = $this->columnTotals($months);
        }

        $groups = collect($columns)
            ->groupBy('group')
            ->map(fn ($items, string $group) => [
                'key' => $group,
                'label' => $items->first()['group_label'],
                'span' => $items->count(),
            ])
            ->values();

        $yearOptions = $this->yearOptions($selectedYear, $selectedHotel?->id);

        return view('bob.index', compact(
            'months',
            'totals',
            'columns',
            'groups',
            'selectedYear',
            'previousYear',
            'yearOptions',
            'hotels',
            'selectedHotel',
            'showHotelFilter',
        ));
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorize('bob.view');

        $validated = $request->validate([
            'year' => ['required', 'integer', 'min:1', 'max:9999'],
            'hotel_id' => ['required', 'integer', Rule::in(HotelAccess::selectableHotelIds())],
            'months' => ['required', 'array'],
            'months.*' => ['array'],
            'months.*.bob_nett_actual' => ['nullable', 'numeric'],
            'months.*.stly_bob_nett' => ['nullable', 'numeric'],
            'months.*.room_nights' => ['nullable', 'integer', 'min:0'],
            'months.*.stly_room_nights' => ['nullable', 'integer', 'min:0'],
            'months.*.adr_nett' => ['nullable', 'numeric'],
        ]);

        $hotelId = (int) $validated['hotel_id'];
        HotelAccess::ensure(null, $hotelId);

        $selectedYear = (int) $validated['year'];
        $previousYear = $selectedYear - 1;

        foreach (range(1, 12) as $month) {
            $row = $validated['months'][$month] ?? $validated['months'][(string) $month] ?? [];

            BobMonthlySnapshot::query()->updateOrCreate(
                [
                    'hotel_id' => $hotelId,
                    'year' => $previousYear,
                    'month' => $month,
                ],
                [
                    'label' => Carbon::create($previousYear, $month, 1)->format('M'),
                    'bob_nett_actual' => $this->nullableDecimal($row['bob_nett_actual'] ?? null),
                    'stly_bob_nett' => $this->nullableDecimal($row['stly_bob_nett'] ?? null),
                    'room_nights' => $this->nullableInteger($row['room_nights'] ?? null),
                    'stly_room_nights' => $this->nullableInteger($row['stly_room_nights'] ?? null),
                    'adr_nett' => $this->nullableDecimal($row['adr_nett'] ?? null),
                ]
            );
        }

        Audit::log('updated', 'bob', 'hotel='.$hotelId.' Manual figures for '.$previousYear);

        return redirect()
            ->route('bob.index', ['year' => $selectedYear, 'hotel_id' => $hotelId])
            ->with('success', 'Manual BOB figures for '.$previousYear.' saved.');
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorize('bob.view');

        $hotels = Hotel::optionsForSelect($request->input('hotel_id'));
        $selectedHotel = $this->resolveHotel($request, $hotels);
        abort_unless($selectedHotel !== null, 422, 'Select a hotel before exporting BOB data.');

        $selectedYear = $this->selectedYear($request);
        $columns = BobColumns::forYear($selectedYear);
        $headers = array_merge(['Month'], array_column($columns, 'label'));
        $months = $this->months($selectedYear, $selectedHotel->id);
        $rows = $months->map(function (array $month) {
            $values = [$month['label']];
            foreach ($month['cells'] as $cell) {
                $values[] = $this->exportCell($cell);
            }

            return $values;
        })->all();

        $total = ['Total'];
        foreach ($this->columnTotals($months) as $cell) {
            $total[] = $this->exportCell($cell);
        }
        $rows[] = $total;

        Audit::log('exported', 'bob', 'hotel='.$selectedHotel->id.' year='.$selectedYear.' rows='.count($rows));

        $hotelSlug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $selectedHotel->code ?: $selectedHotel->name) ?: 'hotel');

        return SimpleXlsxWriter::download(
            'bob-'.$hotelSlug.'-'.$selectedYear.'-'.now()->format('Y-m-d').'.xlsx',
            $headers,
            $rows,
            'BOB '.$selectedYear,
        );
    }

    /**
     * @param  Collection<int, Hotel>  $hotels
     */
    private function resolveHotel(Request $request, Collection $hotels): ?Hotel
    {
        if ($request->filled('hotel_id')) {
            $hotelId = $request->integer('hotel_id');
            HotelAccess::ensure(null, $hotelId);

            return $hotels->firstWhere('id', $hotelId)
                ?? Hotel::query()->find($hotelId);
        }

        $current = HotelAccess::currentHotel();
        if ($current !== null && $hotels->contains('id', $current->id)) {
            return $current;
        }

        if (! HotelAccess::canAccessAllHotels() && $hotels->count() === 1) {
            return $hotels->first();
        }

        return null;
    }

    /**
     * @return Collection<int, array{month: int, label: string, bob: float, nights: int, cells: list<array<string, mixed>>}>
     */
    private function months(int $selectedYear, int $hotelId): Collection
    {
        $previousYear = $selectedYear - 1;
        $columns = BobColumns::forYear($selectedYear);
        $bookings = $this->bookingTotals($selectedYear, $hotelId);

        $figures = BobMonthlySnapshot::query()
            ->where('hotel_id', $hotelId)
            ->whereIn('year', [$selectedYear, $previousYear])
            ->get()
            ->groupBy('year')
            ->map(fn ($rows) => $rows->keyBy(fn ($row) => (int) $row->month));

        return collect(range(1, 12))->map(function (int $month) use ($columns, $figures, $bookings, $selectedYear, $previousYear) {
            return [
                'month' => $month,
                'label' => Carbon::create($selectedYear, $month, 1)->format('F'),
                'bob' => BobColumns::bookingBob($bookings->get($month)),
                'nights' => BobColumns::bookingNights($bookings->get($month)),
                'cells' => BobColumns::cells(
                    $columns,
                    $bookings->get($month),
                    $figures->get($selectedYear)?->get($month),
                    $figures->get($previousYear)?->get($month),
                ),
            ];
        });
    }

    /**
     * @param  Collection<int, array{cells: list<array<string, mixed>>}>  $months
     * @return list<array{key: string, label: string, group: string, format: string, value: int|float}>
     */
    private function columnTotals(Collection $months): array
    {
        $cells = $months->first()['cells'] ?? [];
        $totals = [];

        foreach ($cells as $index => $cell) {
            $sum = 0.0;
            foreach ($months as $month) {
                $value = $month['cells'][$index]['value'] ?? null;
                if ($value === null || $value === '') {
                    continue;
                }
                $sum += (float) $value;
            }

            $format = (string) ($cell['format'] ?? 'money');
            $totals[] = [
                'key' => (string) $cell['key'],
                'label' => (string) ($cell['label'] ?? ''),
                'group' => (string) ($cell['group'] ?? ''),
                'format' => $format,
                'value' => $format === 'number' ? (int) round($sum) : round($sum, 2),
            ];
        }

        return $totals;
    }

    /**
     * @param  array<string, mixed>  $cell
     */
    private function exportCell(array $cell): string|int|float|null
    {
        if (($cell['format'] ?? '') === 'dash') {
            return '-';
        }

        $value = $cell['value'] ?? null;
        if ($value === null || $value === '') {
            return null;
        }

        if (($cell['format'] ?? '') === 'number') {
            return (int) $value;
        }

        return round((float) $value, 2);
    }

    /**
     * Confirmed group bookings for the selected year and hotel, summed by arrival month.
     *
     * @return Collection<int, object>
     */
    private function bookingTotals(int $year, int $hotelId): Collection
    {
        HotelAccess::ensure(null, $hotelId);

        return Enquiry::query()
            ->accessibleBy()
            ->groupBookings()
            ->where('hotel_id', $hotelId)
            ->whereNotNull('check_in')
            ->whereYear('check_in', $year)
            ->selectRaw('MONTH(check_in) as month')
            ->selectRaw('COALESCE(SUM(nett_rev_ex_vat), 0) as bob_nett')
            ->selectRaw('COALESCE(SUM(total_rns), 0) as room_nights')
            ->selectRaw('COALESCE(SUM(bb_revenue), 0) as breakfast_revenue')
            ->selectRaw('COALESCE(SUM(dinner_revenue), 0) as dinner_revenue')
            ->selectRaw('COALESCE(SUM(total_dinner_covers), 0) as dinner_covers')
            ->groupBy(DB::raw('MONTH(check_in)'))
            ->get()
            ->keyBy(fn ($row) => (int) $row->month);
    }

    private function selectedYear(Request $request): int
    {
        $calendarYear = (int) now()->year;
        $year = $request->integer('year');

        if ($year < 1 || $year > 9999) {
            return $calendarYear;
        }

        return $year;
    }

    /**
     * @return list<int>
     */
    private function yearOptions(int $selectedYear, ?int $hotelId): array
    {
        $calendarYear = (int) now()->year;
        $query = BobMonthlySnapshot::query()->distinct();
        if ($hotelId !== null) {
            $query->where('hotel_id', $hotelId);
        }

        $storedYears = $query
            ->pluck('year')
            ->map(fn ($year) => (int) $year);

        $min = min($calendarYear - 5, $selectedYear, $storedYears->min() ?? $calendarYear);
        $max = max($calendarYear + 1, $selectedYear, $storedYears->max() ?? $calendarYear);

        return range($max, $min);
    }

    private function nullableDecimal(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return number_format((float) $value, 2, '.', '');
    }

    private function nullableInteger(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }
}
