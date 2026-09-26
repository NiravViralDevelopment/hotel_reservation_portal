<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Support\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        $settings = Setting::query()->orderBy('group')->orderBy('key')->get();
        $grouped = $settings->groupBy('group');

        return view('settings.edit', compact('grouped', 'settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'settings' => ['required', 'array'],
            'settings.*.group' => ['required', 'string', 'max:50'],
            'settings.*.key' => ['required', 'string', 'max:100'],
            'settings.*.value' => ['nullable', 'string'],
        ]);

        foreach ($validated['settings'] as $row) {
            Setting::set($row['group'], $row['key'], $row['value'] ?? null);
        }

        Audit::log('updated', 'settings', 'Application settings');

        return redirect()->route('settings.edit')->with('success', 'Settings saved.');
    }
}
