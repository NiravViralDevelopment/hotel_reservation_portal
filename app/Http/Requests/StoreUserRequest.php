<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'password' => ['required', 'confirmed', Password::defaults()],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
            'hotels' => ['nullable', 'array'],
            'hotels.*' => ['integer', Rule::in(\App\Models\Hotel::query()->active()->pluck('id')->map(fn ($id) => (int) $id)->all())],
            'signature' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Enter a valid email address.',
            'email.unique' => 'This email is already used.',
            'status.required' => 'Status is required.',
            'password.required' => 'Password is required.',
            'password.confirmed' => 'Password confirmation does not match.',
            'signature.mimes' => 'Signature must be a JPG, PNG, WEBP, or GIF image.',
            'signature.max' => 'Signature must be 2 MB or smaller.',
        ];
    }
}
