<?php

namespace App\Http\Controllers;

use App\Models\TravelAgency;
use App\Support\Audit;
use App\Support\QuerySort;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TravelAgencyController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', TravelAgency::class);

        $query = TravelAgency::query()
            ->withCount(['enquiries', 'confirmedBookings']);

        if ($request->filled('q')) {
            $search = $request->string('q')->trim()->toString();
            $query->where(function ($builder) use ($search) {
                $builder->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('contact_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('country')) {
            $query->where('country', $request->string('country'));
        }

        QuerySort::apply($query, $request, [
            'name' => 'name',
            'code' => 'code',
            'city' => 'city',
            'status' => 'status',
            'enquiries' => 'enquiries_count',
            'bookings' => 'group_bookings_count',
        ], 'name');

        $travelAgencies = $query->paginate(10)->withQueryString();
        $countries = TravelAgency::query()
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->distinct()
            ->orderBy('country')
            ->pluck('country');

        return view('travel-agencies.index', compact('travelAgencies', 'countries'));
    }

    public function create(): View
    {
        $this->authorize('create', TravelAgency::class);

        return view('travel-agencies.create');
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $this->authorize('create', TravelAgency::class);

        $data = $this->validatedData($request);

        $agency = TravelAgency::query()->create($data);
        Audit::log('created', 'travel_agencies', $agency->code, $agency);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'id' => $agency->id,
                'name' => $agency->name,
                'code' => $agency->code,
                'message' => 'Travel agency created.',
            ]);
        }

        return redirect()->route('travel-agencies.index')->with('success', 'Travel agency created.');
    }

    public function show(TravelAgency $travelAgency): View
    {
        $this->authorize('view', $travelAgency);

        $travelAgency->load([
            'enquiries' => fn ($q) => $q->openPipeline()->latest('enquiry_date')->limit(10),
            'confirmedBookings' => fn ($q) => $q->latest('check_in')->limit(10),
        ]);

        return view('travel-agencies.show', compact('travelAgency'));
    }

    public function edit(TravelAgency $travelAgency): View
    {
        $this->authorize('update', $travelAgency);

        return view('travel-agencies.edit', compact('travelAgency'));
    }

    public function update(Request $request, TravelAgency $travelAgency): RedirectResponse
    {
        $this->authorize('update', $travelAgency);

        $data = $this->validatedData($request, $travelAgency->id);

        $travelAgency->update($data);
        Audit::log('updated', 'travel_agencies', $travelAgency->code, $travelAgency);

        return redirect()->route('travel-agencies.index')->with('success', 'Travel agency updated.');
    }

    public function destroy(TravelAgency $travelAgency): RedirectResponse
    {
        $this->authorize('delete', $travelAgency);

        $code = $travelAgency->code;
        $travelAgency->delete();
        Audit::log('deleted', 'travel_agencies', $code);

        return redirect()->route('travel-agencies.index')->with('success', 'Travel agency deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, ?int $ignoreId = null): array
    {
        $codeRule = ['required', 'string', 'max:20', 'unique:travel_agencies,code'];
        if ($ignoreId) {
            $codeRule[3] = 'unique:travel_agencies,code,'.$ignoreId;
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => $codeRule,
            'contact_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:15'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);
    }
}
