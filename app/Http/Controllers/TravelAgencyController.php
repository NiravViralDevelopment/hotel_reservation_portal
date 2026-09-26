<?php

namespace App\Http\Controllers;

use App\Models\TravelAgency;
use App\Support\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TravelAgencyController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', TravelAgency::class);

        $travelAgencies = TravelAgency::query()
            ->withCount(['contacts', 'enquiries', 'groupBookings'])
            ->orderBy('name')
            ->paginate(20);

        return view('travel-agencies.index', compact('travelAgencies'));
    }

    public function create(): View
    {
        $this->authorize('create', TravelAgency::class);

        return view('travel-agencies.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', TravelAgency::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'unique:travel_agencies,code'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $agency = TravelAgency::query()->create($data);
        Audit::log('created', 'travel_agencies', $agency->code, $agency);

        return redirect()->route('travel-agencies.index')->with('success', 'Travel agency created.');
    }

    public function show(TravelAgency $travelAgency): View
    {
        $this->authorize('view', $travelAgency);

        $travelAgency->load(['contacts', 'enquiries', 'groupBookings']);

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

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'unique:travel_agencies,code,'.$travelAgency->id],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);

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
}
