<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Support\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Company::class);

        $companies = Company::query()
            ->withCount(['hotels', 'contacts'])
            ->orderBy('name')
            ->paginate(20);

        return view('companies.index', compact('companies'));
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

        $company->load(['hotels', 'contacts']);

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
