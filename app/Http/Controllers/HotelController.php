<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Hotel;
use App\Support\Audit;
use App\Support\HotelAccess;
use App\Support\QuerySort;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HotelController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Hotel::class);

        $query = Hotel::query()
            ->accessibleBy()
            ->with(['company'])
            ->withCount(['users', 'enquiries', 'groupBookings']);

        if ($request->filled('q')) {
            $search = $request->string('q')->trim()->toString();
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%")
                    ->orWhere('manager_name', 'like', "%{$search}%")
                    ->orWhereHas('company', fn ($cq) => $cq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->integer('company_id'));
        }

        if ($request->filled('city')) {
            $query->where('city', $request->string('city'));
        }

        QuerySort::apply($query, $request, [
            'name' => 'name',
            'code' => 'code',
            'city' => 'city',
            'country' => 'country',
            'rooms' => 'rooms',
            'manager' => 'manager_name',
            'status' => 'status',
        ], 'name');

        $hotels = $query->paginate(10)->withQueryString();

        $companies = Company::query()
            ->whereHas('hotels', fn ($q) => $q->accessibleBy())
            ->orderBy('name')
            ->get(['id', 'name']);

        $cities = Hotel::query()
            ->accessibleBy()
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        return view('hotels.index', compact('hotels', 'companies', 'cities'));
    }

    public function create(): View
    {
        $this->authorize('create', Hotel::class);

        $companies = Company::query()->orderBy('name')->get(['id', 'name']);
        $existingPairs = $this->existingCodeNamePairs();

        return view('hotels.create', compact('companies', 'existingPairs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Hotel::class);

        $data = $request->validate([
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'code' => ['required', 'string', 'max:20'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('hotels', 'name')->where(fn ($q) => $q->where('code', $request->string('code')->trim()->toString())),
            ],
            'city' => ['required', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'rooms' => ['required', 'integer', 'min:0', 'max:99999'],
            'manager_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:15'],
            'email' => ['required', 'email', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ], [
            'company_id.required' => 'Please select a company.',
            'rooms.required' => 'Please enter the number of rooms.',
            'rooms.integer' => 'Rooms must be a whole number.',
            'city.required' => 'Please enter the city.',
            'code.required' => 'Please enter the hotel code.',
            'name.required' => 'Please enter the hotel name.',
            'name.unique' => 'This code and name combination already exists.',
            'phone.required' => 'Please enter the phone number.',
            'email.required' => 'Please enter the email address.',
        ]);

        $data = $this->normalizeHotelData($data);

        $hotel = Hotel::query()->create($data);
        Audit::log('created', 'hotels', $hotel->code, $hotel);

        return redirect()->route('hotels.index')->with('success', 'Hotel created.');
    }

    public function show(Hotel $hotel): View
    {
        $this->authorize('view', $hotel);
        HotelAccess::ensure(null, $hotel->id);

        $hotel->load(['company', 'users', 'groupBookings' => fn ($q) => $q->accessibleBy()->latest('arrival')->limit(20)]);

        return view('hotels.show', compact('hotel'));
    }

    public function edit(Hotel $hotel): View
    {
        $this->authorize('update', $hotel);
        HotelAccess::ensure(null, $hotel->id);

        $companies = Company::query()->orderBy('name')->get(['id', 'name']);
        $existingPairs = $this->existingCodeNamePairs($hotel->id);

        return view('hotels.edit', compact('hotel', 'companies', 'existingPairs'));
    }

    public function update(Request $request, Hotel $hotel): RedirectResponse
    {
        $this->authorize('update', $hotel);
        HotelAccess::ensure(null, $hotel->id);

        $data = $request->validate([
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'code' => ['required', 'string', 'max:20'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('hotels', 'name')
                    ->ignore($hotel->id)
                    ->where(fn ($q) => $q->where('code', $request->string('code')->trim()->toString())),
            ],
            'city' => ['required', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'rooms' => ['required', 'integer', 'min:0', 'max:99999'],
            'manager_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:15'],
            'email' => ['required', 'email', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ], [
            'company_id.required' => 'Please select a company.',
            'rooms.required' => 'Please enter the number of rooms.',
            'rooms.integer' => 'Rooms must be a whole number.',
            'city.required' => 'Please enter the city.',
            'code.required' => 'Please enter the hotel code.',
            'name.required' => 'Please enter the hotel name.',
            'name.unique' => 'This code and name combination already exists.',
            'phone.required' => 'Please enter the phone number.',
            'email.required' => 'Please enter the email address.',
        ]);

        $data = $this->normalizeHotelData($data);

        $hotel->update($data);
        Audit::log('updated', 'hotels', $hotel->code, $hotel);

        return redirect()->route('hotels.index')->with('success', 'Hotel updated.');
    }

    public function destroy(Hotel $hotel): RedirectResponse
    {
        $this->authorize('delete', $hotel);
        HotelAccess::ensure(null, $hotel->id);

        if (! $hotel->canBeDeleted()) {
            return redirect()
                ->route('hotels.index')
                ->with('error', 'This hotel is in use and cannot be deleted.');
        }

        $code = $hotel->code;
        $name = $hotel->name;

        $hotel->users()->detach();
        $hotel->delete();

        Audit::log('deleted', 'hotels', $code);

        return redirect()->route('hotels.index')->with('success', $name.' deleted.');
    }

    /**
     * @return list<array{code: string, name: string}>
     */
    private function existingCodeNamePairs(?int $ignoreHotelId = null): array
    {
        $query = Hotel::query()->select(['code', 'name']);

        if ($ignoreHotelId !== null) {
            $query->where('id', '!=', $ignoreHotelId);
        }

        return $query
            ->get()
            ->map(fn (Hotel $hotel) => [
                'code' => mb_strtolower(trim((string) $hotel->code)),
                'name' => mb_strtolower(trim((string) $hotel->name)),
            ])
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizeHotelData(array $data): array
    {
        $data['code'] = trim((string) ($data['code'] ?? ''));
        $data['name'] = trim((string) ($data['name'] ?? ''));
        $data['rooms'] = (int) ($data['rooms'] ?? 0);
        $data['country'] = 'United Kingdom';

        foreach (['manager_name', 'notes'] as $field) {
            if (array_key_exists($field, $data) && $data[$field] === '') {
                $data[$field] = null;
            }
        }

        return $data;
    }
}
