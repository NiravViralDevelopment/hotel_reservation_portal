<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Hotel;
use App\Models\User;
use App\Support\Audit;
use App\Support\QuerySort;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Models\Role;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $query = User::query()
            ->with(['roles', 'hotels']);

        if ($request->filled('q')) {
            $search = $request->string('q')->trim()->toString();
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('role')) {
            $query->role($request->string('role')->toString());
        }

        if ($request->filled('hotel_id')) {
            $hotelId = $request->integer('hotel_id');
            $query->whereHas('hotels', fn ($q) => $q->where('hotels.id', $hotelId));
        }

        QuerySort::apply($query, $request, [
            'name' => 'name',
            'email' => 'email',
            'phone' => 'phone',
            'status' => 'status',
        ], 'name');

        $users = $query->paginate(10)->withQueryString();
        $roles = Role::query()->orderBy('name')->get(['id', 'name']);
        $hotels = Hotel::optionsForAssignment();

        return view('users.index', compact('users', 'roles', 'hotels'));
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        $roles = Role::query()->orderBy('name')->get();
        $hotels = Hotel::optionsForAssignment();
        $existingEmails = User::query()->pluck('email')->map(fn ($email) => mb_strtolower($email))->values();

        return view('users.create', compact('roles', 'hotels', 'existingEmails'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $signature = $request->file('signature');
        $data = $request->safe()->except(['roles', 'hotels', 'password_confirmation', 'department', 'signature']);
        $user = User::query()->create($data);

        if ($request->filled('roles')) {
            $user->syncRoles($request->input('roles'));
        } else {
            $user->syncRoles([]);
        }

        $user->hotels()->sync($request->input('hotels', []));

        if ($signature instanceof UploadedFile) {
            $this->storeUserSignature($user, $signature);
        }

        Audit::log('created', 'users', $user->email, $user);

        return redirect()->route('users.index')->with('success', 'User created.');
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        $roles = Role::query()->orderBy('name')->get();
        $user->load(['roles', 'hotels']);
        $hotels = Hotel::optionsForAssignment($user->hotels->pluck('id')->all());
        $existingEmails = User::query()
            ->where('id', '!=', $user->id)
            ->pluck('email')
            ->map(fn ($email) => mb_strtolower($email))
            ->values();

        return view('users.edit', compact('user', 'roles', 'hotels', 'existingEmails'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $signature = $request->file('signature');
        $removeSignature = $request->boolean('remove_signature');
        $data = $request->safe()->except(['roles', 'hotels', 'password', 'password_confirmation', 'department', 'signature', 'remove_signature']);

        if ($request->filled('password')) {
            $data['password'] = $request->validated('password');
        }

        $user->update($data);

        if ($request->has('roles')) {
            $user->syncRoles($request->input('roles', []));
        }

        $user->hotels()->sync($request->input('hotels', []));

        if ($signature instanceof UploadedFile) {
            $this->storeUserSignature($user, $signature);
        } elseif ($removeSignature && $user->hasSignature()) {
            $user->deleteStoredSignature();
            $user->clearSignatureAttributes();
            $user->save();
        }

        Audit::log('updated', 'users', $user->email, $user);

        return redirect()->route('users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account.');
        }

        $user->load(['roles', 'hotels']);

        if (! $user->canBeDeleted()) {
            return redirect()->route('users.index')->with(
                'error',
                'This user has roles or hotel access assigned. Remove those first, or deactivate the account instead.'
            );
        }

        $email = $user->email;
        $user->delete();

        Audit::log('deleted', 'users', $email);

        return redirect()->route('users.index')->with('success', 'User deleted.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'You cannot deactivate your own account.');
        }

        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        Audit::log($newStatus === 'active' ? 'activated' : 'deactivated', 'users', $user->email, $user);

        return redirect()->route('users.index')->with(
            'success',
            $newStatus === 'active' ? 'User activated.' : 'User deactivated. They can no longer sign in.'
        );
    }

    public function viewSignature(User $user): StreamedResponse
    {
        abort_unless(auth()->id() === $user->id || auth()->user()?->can('view', $user), 403);
        abort_unless($user->hasSignature(), 404);

        $disk = $user->signature_disk ?: 'local';
        $path = (string) $user->signature_path;

        abort_unless(Storage::disk($disk)->exists($path), 404);

        return Storage::disk($disk)->response($path, $user->signature_original_name ?: 'signature', [
            'Content-Type' => $user->signature_mime_type ?: 'image/png',
            'Content-Disposition' => 'inline',
        ]);
    }

    private function storeUserSignature(User $user, UploadedFile $file): void
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: '');
        abort_unless(in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true), 422);

        if ($user->hasSignature()) {
            $user->deleteStoredSignature();
        }

        $path = $file->storeAs(
            'user-signatures/'.$user->id,
            Str::uuid()->toString().'.'.$extension,
            'local'
        );

        $user->forceFill([
            'signature_disk' => 'local',
            'signature_path' => $path,
            'signature_original_name' => $file->getClientOriginalName(),
            'signature_mime_type' => $file->getMimeType(),
            'signature_size' => $file->getSize() ?: 0,
        ])->save();
    }
}
