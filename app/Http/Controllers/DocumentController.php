<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\GroupBooking;
use App\Support\Audit;
use App\Support\HotelAccess;
use App\Support\QuerySort;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Document::class);

        $query = Document::query()
            ->with(['groupBooking', 'uploadedBy'])
            ->where(function ($query) {
                $query->whereNull('group_booking_id')
                    ->orWhereHas('groupBooking', fn ($q) => $q->accessibleBy());
            });

        QuerySort::apply($query, $request, [
            'name' => 'name',
            'category' => 'category',
            'created_at' => 'created_at',
        ], 'created_at', 'desc');

        $documents = $query->paginate(25)->withQueryString();

        return view('documents.index', compact('documents'));
    }

    public function create(): View
    {
        $this->authorize('create', Document::class);

        $groupBookings = GroupBooking::query()
            ->accessibleBy()
            ->active()
            ->orderByDesc('arrival')
            ->limit(100)
            ->get(['id', 'block_id', 'group_name']);

        return view('documents.create', compact('groupBookings'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Document::class);

        $accessibleBookingIds = GroupBooking::query()->accessibleBy()->pluck('id')->all();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:50'],
            'group_booking_id' => ['nullable', 'integer', Rule::in($accessibleBookingIds)],
            'file' => ['required', 'file', 'max:10240'],
        ]);

        if (! empty($validated['group_booking_id'])) {
            $booking = GroupBooking::query()->findOrFail($validated['group_booking_id']);
            HotelAccess::ensure(null, $booking->hotel_id);
        }

        $file = $request->file('file');
        $path = $file->store('documents/'.$validated['category'], 'local');

        $document = Document::query()->create([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'group_booking_id' => $validated['group_booking_id'] ?? null,
            'uploaded_by' => auth()->id(),
            'disk' => 'local',
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        Audit::log('uploaded', 'documents', $document->name, $document);

        return redirect()->route('documents.index')->with('success', 'Document uploaded.');
    }

    public function show(Document $document): View
    {
        $this->authorize('view', $document);

        $document->load(['groupBooking', 'uploadedBy']);

        return view('documents.show', compact('document'));
    }

    public function edit(Document $document): View
    {
        $this->authorize('update', $document);

        $groupBookings = GroupBooking::query()
            ->accessibleBy()
            ->orderByDesc('arrival')
            ->limit(100)
            ->get(['id', 'block_id', 'group_name']);

        return view('documents.edit', compact('document', 'groupBookings'));
    }

    public function update(Request $request, Document $document): RedirectResponse
    {
        $this->authorize('update', $document);

        $accessibleBookingIds = GroupBooking::query()->accessibleBy()->pluck('id')->all();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:50'],
            'group_booking_id' => ['nullable', 'integer', Rule::in($accessibleBookingIds)],
        ]);

        if (! empty($validated['group_booking_id'])) {
            $booking = GroupBooking::query()->findOrFail($validated['group_booking_id']);
            HotelAccess::ensure(null, $booking->hotel_id);
        }

        $document->update($validated);
        Audit::log('updated', 'documents', $document->name, $document);

        return redirect()->route('documents.index')->with('success', 'Document updated.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        $this->authorize('delete', $document);

        Storage::disk($document->disk)->delete($document->path);
        $name = $document->name;
        $document->delete();
        Audit::log('deleted', 'documents', $name);

        return redirect()->route('documents.index')->with('success', 'Document deleted.');
    }

    public function download(Document $document): StreamedResponse
    {
        $this->authorize('download', $document);

        Audit::log('downloaded', 'documents', $document->name, $document);

        return Storage::disk($document->disk)->download($document->path, $document->name);
    }
}
