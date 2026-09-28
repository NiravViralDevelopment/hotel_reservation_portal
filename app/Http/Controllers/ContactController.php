<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Contact;
use App\Models\TravelAgency;
use App\Support\Audit;
use App\Support\QuerySort;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Contact::class);

        $query = Contact::query()
            ->with(['company', 'travelAgency']);

        QuerySort::apply($query, $request, [
            'name' => 'name',
            'email' => 'email',
            'phone' => 'phone',
            'position' => 'position',
        ], 'name');

        $contacts = $query->paginate(20)->withQueryString();

        return view('contacts.index', compact('contacts'));
    }

    public function create(): View
    {
        $this->authorize('create', Contact::class);

        $companies = Company::query()->orderBy('name')->get(['id', 'name']);
        $travelAgencies = TravelAgency::query()->orderBy('name')->get(['id', 'name', 'code']);

        return view('contacts.create', compact('companies', 'travelAgencies'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Contact::class);

        $data = $request->validate([
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'travel_agency_id' => ['nullable', 'integer', 'exists:travel_agencies,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'position' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $contact = Contact::query()->create($data);
        Audit::log('created', 'contacts', $contact->name, $contact);

        return redirect()->route('contacts.index')->with('success', 'Contact created.');
    }

    public function show(Contact $contact): View
    {
        $this->authorize('view', $contact);

        $contact->load(['company', 'travelAgency']);

        return view('contacts.show', compact('contact'));
    }

    public function edit(Contact $contact): View
    {
        $this->authorize('update', $contact);

        $companies = Company::query()->orderBy('name')->get(['id', 'name']);
        $travelAgencies = TravelAgency::query()->orderBy('name')->get(['id', 'name', 'code']);

        return view('contacts.edit', compact('contact', 'companies', 'travelAgencies'));
    }

    public function update(Request $request, Contact $contact): RedirectResponse
    {
        $this->authorize('update', $contact);

        $data = $request->validate([
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'travel_agency_id' => ['nullable', 'integer', 'exists:travel_agencies,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'position' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $contact->update($data);
        Audit::log('updated', 'contacts', $contact->name, $contact);

        return redirect()->route('contacts.index')->with('success', 'Contact updated.');
    }

    public function destroy(Contact $contact): RedirectResponse
    {
        $this->authorize('delete', $contact);

        $name = $contact->name;
        $contact->delete();
        Audit::log('deleted', 'contacts', $name);

        return redirect()->route('contacts.index')->with('success', 'Contact deleted.');
    }
}
