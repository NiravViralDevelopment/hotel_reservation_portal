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
            ->withCount(['hotels']);

        if ($request->filled('q')) {
            $search = $request->string('q')->trim()->toString();
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('reg_number', 'like', "%{$search}%")
                    ->orWhere('vat_number', 'like', "%{$search}%")
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
            'status' => 'status',
        ], 'name');

        $companies = $query->paginate(10)->withQueryString();
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

        $data = $this->validatedData($request);
        $data['country'] = 'United Kingdom';

        $company = Company::query()->create($data);
        Audit::log('created', 'companies', $company->name, $company);

        return redirect()->route('companies.index')->with('success', 'Company created.');
    }

    public function show(Company $company): View
    {
        $this->authorize('view', $company);

        $company->load([
            'hotels' => fn ($q) => $q->orderBy('name'),
        ]);

        $confirmedBookings = \App\Models\Enquiry::query()
            ->with('hotel:id,code,name')
            ->whereIn('hotel_id', $company->hotels->pluck('id'))
            ->groupBookings()
            ->latest('check_in')
            ->limit(15)
            ->get();

        return view('companies.show', compact('company', 'confirmedBookings'));
    }

    public function edit(Company $company): View
    {
        $this->authorize('update', $company);

        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company): RedirectResponse
    {
        $this->authorize('update', $company);

        $data = $this->validatedData($request);
        $data['country'] = 'United Kingdom';

        $company->update($data);
        Audit::log('updated', 'companies', $company->name, $company);

        return redirect()->route('companies.index')->with('success', 'Company updated.');
    }

    public function destroy(Company $company): RedirectResponse
    {
        $this->authorize('delete', $company);

        $name = $company->name;
        $company->contracts()->get()->each->delete();
        $company->delete();
        Audit::log('deleted', 'companies', $name);

        return redirect()->route('companies.index')->with('success', 'Company deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'reg_number' => ['required', 'string', 'max:50'],
            'vat_number' => ['required', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'registered_address' => ['required', 'string', 'max:5000'],
            'trading_address' => ['required', 'string', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);
    }
}
