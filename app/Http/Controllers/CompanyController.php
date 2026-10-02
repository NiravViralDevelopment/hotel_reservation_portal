<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Support\Audit;
use App\Support\QuerySort;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Company::class);

        $query = Company::query()
            ->withCount(['hotels', 'contacts']);

        if ($request->filled('q')) {
            $search = $request->string('q')->trim()->toString();
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('reg_number', 'like', "%{$search}%")
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
            'reg_number' => 'reg_number',
            'city' => 'city',
            'country' => 'country',
            'hotels' => 'hotels_count',
            'contacts' => 'contacts_count',
            'status' => 'status',
        ], 'name');

        $companies = $query->paginate(20)->withQueryString();
        $countries = Company::query()
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->distinct()
            ->orderBy('country')
            ->pluck('country');

        return view('companies.index', compact('companies', 'countries'));
    }

    public function create(): View
    {
        $this->authorize('create', Company::class);

        return view('companies.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Company::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'reg_number' => ['nullable', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
            'notes' => ['nullable', 'string'],
        ]);

        $company = Company::query()->create($data);
        Audit::log('created', 'companies', $company->name, $company);

        return redirect()->route('companies.index')->with('success', 'Company created.');
    }

    public function show(Company $company): View
    {
        $this->authorize('view', $company);

        $company->load([
            'hotels' => fn ($q) => $q->orderBy('name'),
            'contacts' => fn ($q) => $q->orderBy('name'),
            'groupBookings' => fn ($q) => $q->with('hotel:id,code,name')->latest('arrival')->limit(15),
        ]);

        return view('companies.show', compact('company'));
    }

    public function edit(Company $company): View
    {
        $this->authorize('update', $company);

        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company): RedirectResponse
    {
        $this->authorize('update', $company);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'reg_number' => ['nullable', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
            'notes' => ['nullable', 'string'],
        ]);

        $company->update($data);
        Audit::log('updated', 'companies', $company->name, $company);

        return redirect()->route('companies.index')->with('success', 'Company updated.');
    }

    public function destroy(Company $company): RedirectResponse
    {
        $this->authorize('delete', $company);

        $name = $company->name;
        $company->delete();
        Audit::log('deleted', 'companies', $name);

        return redirect()->route('companies.index')->with('success', 'Company deleted.');
    }
}
