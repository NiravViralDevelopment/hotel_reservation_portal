<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Hotel;
use App\Models\User;
use App\Support\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HotelController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Hotel::class);

        $hotels = Hotel::query()
            ->with(['company', 'managerUser'])
            ->orderBy('name')
            ->paginate(20);

        return view('hotels.index', compact('hotels'));
    }

    public function create(): View
    {
        $this->authorize('create', Hotel::class);

        $companies = Company::query()->orderBy('name')->get(['id', 'name']);
        $managers = User::query()->where('status', 'active')->orderBy('name')->get(['id', 'name']);

        return view('hotels.create', compact('companies', 'managers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Hotel::class);

        $data = $request->validate([
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'code' => ['required', 'string', 'max:20', 'unique:hotels,code'],
            'name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'rooms' => ['nullable', 'integer', 'min:0'],
            'manager_name' => ['nullable', 'string', 'max:255'],
            'manager_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'notes' => ['nullable', 'string'],
        ]);

        $hotel = Hotel::query()->create($data);
        Audit::log('created', 'hotels', $hotel->code, $hotel);

        return redirect()->route('hotels.index')->with('success', 'Hotel created.');
    }

    public function show(Hotel $hotel): View
    {
        $this->authorize('view', $hotel);

        $hotel->load(['company', 'managerUser', 'users', 'groupBookings']);

        return view('hotels.show', compact('hotel'));
    }

    public function edit(Hotel $hotel): View
    {
        $this->authorize('update', $hotel);

        $companies = Company::query()->orderBy('name')->get(['id', 'name']);
        $managers = User::query()->where('status', 'active')->orderBy('name')->get(['id', 'name']);

        return view('hotels.edit', compact('hotel', 'companies', 'managers'));
    }

    public function update(Request $request, Hotel $hotel): RedirectResponse
    {
        $this->authorize('update', $hotel);

        $data = $request->validate([
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'code' => ['required', 'string', 'max:20', 'unique:hotels,code,'.$hotel->id],
            'name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'rooms' => ['nullable', 'integer', 'min:0'],
            'manager_name' => ['nullable', 'string', 'max:255'],
            'manager_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'notes' => ['nullable', 'string'],
        ]);

        $hotel->update($data);
        Audit::log('updated', 'hotels', $hotel->code, $hotel);

        return redirect()->route('hotels.index')->with('success', 'Hotel updated.');
    }

    public function destroy(Hotel $hotel): RedirectResponse
    {
        $this->authorize('delete', $hotel);

        $code = $hotel->code;
        $hotel->delete();
        Audit::log('deleted', 'hotels', $code);

        return redirect()->route('hotels.index')->with('success', 'Hotel deleted.');
    }
}
