<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyContract;
use App\Support\Audit;
use App\Support\QuerySort;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CompanyContractController extends Controller
{
    public function index(Request $request, Company $company): View
    {
        $this->authorize('view', $company);

        $query = CompanyContract::query()
            ->where('company_id', $company->id)
            ->with('uploadedBy');

        if ($request->filled('q')) {
            $search = $request->string('q')->trim()->toString();
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('original_name', 'like', "%{$search}%");
            });
        }

        QuerySort::apply($query, $request, [
            'title' => 'title',
            'created_at' => 'created_at',
        ], 'created_at', 'desc');

        $contracts = $query->paginate(10)->withQueryString();

        return view('company-contracts.index', compact('company', 'contracts'));
    }

    public function create(Company $company): View
    {
        $this->authorize('update', $company);

        return view('company-contracts.create', compact('company'));
    }

    public function store(Request $request, Company $company): RedirectResponse
    {
        $this->authorize('update', $company);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $file = $request->file('pdf');
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getMimeType() ?: 'application/pdf';
        $size = $file->getSize() ?: 0;

        $directory = public_path('company-contracts/'.$company->id);
        File::ensureDirectoryExists($directory);

        $storedName = Str::uuid()->toString().'.pdf';
        $file->move($directory, $storedName);

        $contract = $company->contracts()->create([
            'title' => $validated['title'],
            'disk' => 'public',
            'path' => 'company-contracts/'.$company->id.'/'.$storedName,
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'size' => $size,
            'uploaded_by' => auth()->id(),
        ]);

        Audit::log('uploaded', 'company-contracts', $contract->title, $contract);

        return redirect()
            ->route('companies.contracts.index', $company)
            ->with('success', 'Contract uploaded.');
    }

    public function destroy(Company $company, CompanyContract $contract): RedirectResponse
    {
        $this->authorize('update', $company);

        $title = $contract->title;
        $contract->delete();
        Audit::log('deleted', 'company-contracts', $title);

        return redirect()
            ->route('companies.contracts.index', $company)
            ->with('success', 'Contract deleted.');
    }

    public function download(Company $company, CompanyContract $contract): BinaryFileResponse
    {
        $this->authorize('view', $company);

        Audit::log('downloaded', 'company-contracts', $contract->title, $contract);

        $filename = $contract->original_name ?: ($contract->title.'.pdf');

        return response()->download($contract->absolutePath(), $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
