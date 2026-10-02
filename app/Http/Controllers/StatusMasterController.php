<?php

namespace App\Http\Controllers;

use App\Models\StatusMaster;
use App\Support\Audit;
use App\Support\QuerySort;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StatusMasterController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', StatusMaster::class);

        $query = StatusMaster::query();

        if ($request->filled('q')) {
            $search = $request->string('q')->trim()->toString();
            $query->where('title', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        QuerySort::apply($query, $request, [
            'title' => 'title',
            'status' => 'status',
        ], 'title');

        $statusMasters = $query->paginate(20)->withQueryString();

        return view('status-masters.index', compact('statusMasters'));
    }

    public function create(): View
    {
        $this->authorize('create', StatusMaster::class);

        $existingTitles = StatusMaster::query()->pluck('title')->map(fn ($title) => mb_strtolower($title))->values();

        return view('status-masters.create', compact('existingTitles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', StatusMaster::class);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255', 'unique:status_masters,title'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ], [
            'title.unique' => 'This status title already exists.',
            'title.required' => 'Status title is required.',
        ]);

        $statusMaster = StatusMaster::query()->create($data);
        Audit::log('created', 'status_masters', $statusMaster->title, $statusMaster);

        return redirect()->route('status-masters.index')->with('success', 'Status created.');
    }

    public function edit(StatusMaster $statusMaster): View
    {
        $this->authorize('update', $statusMaster);

        $existingTitles = StatusMaster::query()
            ->where('id', '!=', $statusMaster->id)
            ->pluck('title')
            ->map(fn ($title) => mb_strtolower($title))
            ->values();

        return view('status-masters.edit', compact('statusMaster', 'existingTitles'));
    }

    public function update(Request $request, StatusMaster $statusMaster): RedirectResponse
    {
        $this->authorize('update', $statusMaster);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255', Rule::unique('status_masters', 'title')->ignore($statusMaster->id)],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ], [
            'title.unique' => 'This status title already exists.',
            'title.required' => 'Status title is required.',
        ]);

        $statusMaster->update($data);
        Audit::log('updated', 'status_masters', $statusMaster->title, $statusMaster);

        return redirect()->route('status-masters.index')->with('success', 'Status updated.');
    }

    public function destroy(StatusMaster $statusMaster): RedirectResponse
    {
        $this->authorize('delete', $statusMaster);

        $title = $statusMaster->title;
        $statusMaster->delete();
        Audit::log('deleted', 'status_masters', $title);

        return redirect()->route('status-masters.index')->with('success', 'Status deleted.');
    }
}
