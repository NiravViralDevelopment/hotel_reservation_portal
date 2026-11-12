<?php

namespace App\Http\Controllers;

use App\Models\BobMonthlySnapshot;
use App\Models\Enquiry;
use App\Support\Audit;
use App\Support\BobColumns;
use App\Support\SimpleXlsxWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BobController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('bob.view');

        $selectedYear = $this->selectedYear($request);
        $previousYear = $selectedYear - 1;
        $columns = BobColumns::forYear($selectedYear);
        $months = $this->months($selectedYear);

        $groups = collect($columns)
            ->groupBy('group')
            ->map(fn ($items, string $group) => [
                'key' => $group,
                'label' => $items->first()['group_label'],
                'span' => $items->count(),
            ])
            ->values();

        $yearOptions = $this->yearOptions($selectedYear);

        return view('bob.index', compact(
            'months',
            'columns',
            'groups',
            'selectedYear',
            'previousYear',
            'yearOptions',
        ));
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorize('bob.view');

        $validated = $request->validate([
            'year' => ['required', 'integer', 'min:1', 'max:9999'],
            'months' => ['required', 'array'],
            'months.*' => ['array'],
            'months.*.bob_nett_actual' => ['nullable', 'numeric'],
            'months.*.stly_bob_nett' => ['nullable', 'numeric'],
            'months.*.room_nights' => ['nullable', 'integer', 'min:0'],
            'months.*.stly_room_nights' => ['nullable', 'integer', 'min:0'],
            'months.*.adr_nett' => ['nullable', 'numeric'],
        ]);

        $selectedYear = (int) $validated['year'];
        $previousYear = $selectedYear - 1;

        foreach (range(1, 12) as $month) {
            $row = $validated['months'][$month] ?? $validated['months'][(string) $month] ?? [];

            BobMonthlySnapshot::query()->updateOrCreate(
                ['year' => $previousYear, 'month' => $month],
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

        Audit::log('updated', 'bob', 'Manual figures for '.$previousYear);

        return redirect()
            ->route('bob.index', ['year' => $selectedYear])
            ->with('success', 'Manual BOB figures for '.$previousYear.' saved.');
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorize('bob.view');

        $selectedYear = $this->selectedYear($request);
        $columns = BobColumns::forYear($selectedYear);
        $headers = array_merge(['Month'], array_column($columns, 'label'));
        $rows = $this->months($selectedYear)->map(function (array $month) {
            $values = [$month['label']];
            foreach ($month['cells'] as $cell) {
                $values[] = $this->exportCell($cell);
            }

            return $values;
        })->all();

        Audit::log('exported', 'bob', 'year='.$selectedYear.' rows='.count($rows));

        return SimpleXlsxWriter::download(
            'bob-'.$selectedYear.'-'.now()->format('Y-m-d').'.xlsx',
            $headers,
            $rows,
            'BOB '.$selectedYear,
        );
    }

    /**
     * @return Collection<int, array{month: int, label: string, bob: float, nights: int, cells: list<array<string, mixed>>}>
     */
    private function months(int $selectedYear): Collection
    {
        $previousYear = $selectedYear - 1;
        $columns = BobColumns::forYear($selectedYear);
        $bookings = $this->bookingTotals($selectedYear);

        $figures = BobMonthlySnapshot::query()
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
     * Confirmed group bookings for the selected year, summed by arrival month.
     *
     * @return Collection<int, object>
     */
    private function bookingTotals(int $year): Collection
    {
        return Enquiry::query()
            ->accessibleBy()
            ->groupBookings()
            ->whereNotNull('check_in')
            ->whereYear('check_in', $year)
            ->selectRaw('MONTH(check_in) as month')
            ->selectRaw('COALESCE(SUM(nett_rev_ex_vat), 0) as bob_nett')
            ->selectRaw('COALESCE(SUM(total_rns), 0) as room_nights')
            ->selectRaw('COALESCE(SUM(bb_revenue), 0) as breakfast_revenue')
            ->selectRaw('COALESCE(SUM(dinner_revenue), 0) as dinner_revenue')
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
    private function yearOptions(int $selectedYear): array
    {
        $calendarYear = (int) now()->year;
        $storedYears = BobMonthlySnapshot::query()
            ->distinct()
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
