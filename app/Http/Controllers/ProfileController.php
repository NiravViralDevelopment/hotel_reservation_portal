<?php

namespace App\Http\Controllers;

use App\Support\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $user = Auth::user();

        return view('profile.edit', compact('user'));
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:30', 'regex:/^[A-Za-z][A-Za-z\s\'.-]*$/'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\s()-]*$/'],
            'job_title' => ['nullable', 'string', 'max:255'],
        ], [
            'name.regex' => 'Numbers are not allowed in name.',
            'phone.regex' => 'Phone can only contain numbers.',
        ]);

        if (! empty($validated['phone'])) {
            $digits = preg_replace('/\D+/', '', $validated['phone']) ?? '';
            if (strlen($digits) < 7 || strlen($digits) > 15) {
                return back()
                    ->withInput()
                    ->withErrors(['phone' => 'Enter a valid phone number.']);
            }
        }

        $user->update($validated);
        Audit::log('updated', 'profile', $user->email, $user);

        return redirect()->route('profile.edit')->with('success', 'Profile updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => $validated['password'],
        ]);

        Audit::log('updated', 'profile', 'Password changed for '.$user->email, $user);

        return redirect()->route('profile.edit')->with('success', 'Password changed successfully.');
    }
}
