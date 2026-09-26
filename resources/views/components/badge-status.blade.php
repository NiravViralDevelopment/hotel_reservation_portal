@props(['status' => ''])

@php
    $raw = is_object($status) && enum_exists($status::class) ? $status->value : (string) $status;
    $normalized = strtolower(preg_replace('/[\s_\-]+/', '', $raw));
    $map = [
        'confirmed' => 'confirmed',
        'active' => 'active',
        'paid' => 'paid',
        'provisional' => 'pending',
        'pending' => 'pending',
        'partial' => 'partial',
        'unpaid' => 'unpaid',
        'cancelled' => 'cancelled',
        'new' => 'enquiry',
        'followup' => 'pending',
        'quoted' => 'partial',
        'lost' => 'cancelled',
        'inactive' => 'inactive',
        'def' => 'confirmed',
        'open' => 'enquiry',
        'closed' => 'inactive',
    ];
    $cls = $map[$normalized] ?? 'pending';
    $display = ucwords(str_replace(['_', '-'], ' ', $raw));
@endphp

<span {{ $attributes->merge(['class' => 'badge-status badge-'.$cls]) }}>{{ $display }}</span>
