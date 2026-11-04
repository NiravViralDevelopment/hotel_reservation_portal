<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\GroupBookingAttachment;
use App\Support\Audit;
use App\Support\HotelAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GroupBookingAttachmentController extends Controller
{
    public function index(Enquiry $enquiry): View
    {
        $this->ensureBooking($enquiry);

        $enquiry->load('hotel');

        $attachments = $enquiry->attachments()
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('group-bookings.attachments.index', compact('enquiry', 'attachments'));
    }

    public function create(Enquiry $enquiry): View
    {
        $this->ensureBooking($enquiry);

        $enquiry->load('hotel');

        return view('group-bookings.attachments.create', compact('enquiry'));
    }

    public function store(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $this->ensureBooking($enquiry);

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'feedback' => ['nullable', 'string', 'max:5000'],
            'date' => ['nullable', 'date'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ]);

        $attributes = [
            'title' => $this->blankToNull($validated['title'] ?? null),
            'feedback' => $this->blankToNull($validated['feedback'] ?? null),
            'attachment_date' => $validated['date'] ?? null,
            'uploaded_by' => auth()->id(),
        ];

        $file = $request->file('attachment');
        if ($file instanceof UploadedFile) {
            $attributes = array_merge($attributes, $this->storedFileAttributes($enquiry, $file));
        }

        $attachment = $enquiry->attachments()->create($attributes);

        Audit::log('created', 'group-booking-attachments', $attachment->title ?: $enquiry->block_id, $attachment);

        return redirect()
            ->route('group-bookings.attachments.index', $enquiry)
            ->with('success', 'Attachment saved.');
    }

    public function edit(Enquiry $enquiry, GroupBookingAttachment $attachment): View
    {
        $this->ensureBooking($enquiry);
        $this->ensureAttachment($enquiry, $attachment);

        $enquiry->load('hotel');

        return view('group-bookings.attachments.edit', compact('enquiry', 'attachment'));
    }

    public function update(Request $request, Enquiry $enquiry, GroupBookingAttachment $attachment): RedirectResponse
    {
        $this->ensureBooking($enquiry);
        $this->ensureAttachment($enquiry, $attachment);

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'feedback' => ['nullable', 'string', 'max:5000'],
            'date' => ['nullable', 'date'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ]);

        $attributes = [
            'title' => $this->blankToNull($validated['title'] ?? null),
            'feedback' => $this->blankToNull($validated['feedback'] ?? null),
            'attachment_date' => $validated['date'] ?? null,
        ];

        $file = $request->file('attachment');
        if ($file instanceof UploadedFile) {
            $stored = $this->storedFileAttributes($enquiry, $file);
            $attachment->deleteStoredFile();
            $attributes = array_merge($attributes, $stored);
        }

        $attachment->update($attributes);

        Audit::log('updated', 'group-booking-attachments', $attachment->title ?: $enquiry->block_id, $attachment);

        return redirect()
            ->route('group-bookings.attachments.index', $enquiry)
            ->with('success', 'Attachment updated.');
    }

    public function download(Enquiry $enquiry, GroupBookingAttachment $attachment): StreamedResponse
    {
        $this->ensureBooking($enquiry);
        $this->ensureAttachment($enquiry, $attachment);
        abort_unless($attachment->hasFile(), 404);

        $disk = $attachment->disk ?: 'local';
        $path = (string) $attachment->path;
        abort_unless(Storage::disk($disk)->exists($path), 404);

        $filename = str_replace(['"', "\r", "\n"], '', $attachment->original_name ?: 'attachment');

        return Storage::disk($disk)->response($path, $filename, [
            'Content-Type' => $attachment->mime_type ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }

    public function destroy(Enquiry $enquiry, GroupBookingAttachment $attachment): RedirectResponse
    {
        $this->ensureBooking($enquiry);
        $this->ensureAttachment($enquiry, $attachment);

        $title = $attachment->title;
        $attachment->delete();

        Audit::log('deleted', 'group-booking-attachments', $title ?: $enquiry->block_id, $enquiry);

        return redirect()
            ->route('group-bookings.attachments.index', $enquiry)
            ->with('success', 'Attachment deleted.');
    }

    private function ensureBooking(Enquiry $enquiry): void
    {
        abort_unless(auth()->user()?->can('bookings.view'), 403);
        abort_unless($enquiry->is_confirm && ! $enquiry->is_cancel, 404);
        HotelAccess::ensure(null, $enquiry->hotel_id);
    }

    private function ensureAttachment(Enquiry $enquiry, GroupBookingAttachment $attachment): void
    {
        abort_unless((int) $attachment->enquiry_id === (int) $enquiry->id, 404);
    }

    /**
     * @return array{disk: string, path: string, original_name: string, mime_type: ?string, size: int}
     */
    private function storedFileAttributes(Enquiry $enquiry, UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin');
        $extension = preg_replace('/[^a-z0-9]+/', '', $extension) ?: 'bin';
        $storedName = Str::uuid()->toString().'.'.$extension;
        $directory = public_path('group-booking-attachments/'.$enquiry->id);
        File::ensureDirectoryExists($directory);
        $file->move($directory, $storedName);

        return [
            'disk' => 'public',
            'path' => 'group-booking-attachments/'.$enquiry->id.'/'.$storedName,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize() ?: 0,
        ];
    }

    private function blankToNull(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
