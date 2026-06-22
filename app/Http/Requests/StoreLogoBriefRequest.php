<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLogoBriefRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public API
    }

    public function rules(): array
    {
        return [
            // Contact Info
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255'],
            'personal_phone'        => ['required', 'string', 'max:50'],
            'company_phone'         => ['nullable', 'string', 'max:50'],

            // Logo & Company
            'logo_name'             => ['required', 'string', 'max:255'],
            'company_slogan'        => ['nullable', 'string', 'max:255'],
            'industry'              => ['nullable', 'string', 'max:255'],
            'business_desc'         => ['required', 'string'],
            'logo_description'      => ['required', 'string'],

            // Competitor References
            'competitors_ref'       => ['required', 'string', 'max:255'],
            'competitors_ref_two'   => ['nullable', 'string', 'max:255'],
            'competitors_ref_three' => ['nullable', 'string', 'max:255'],

            // Design Preferences - Accept arrays
            'logo_type'             => ['nullable', 'string', 'max:100'],
            'logo_fonts'            => ['nullable'],
            'logo_color'            => ['nullable'],
            'primary_color'         => ['nullable', 'string', 'max:20'],
            'secondary_color'       => ['nullable', 'string', 'max:20'],

            // Files
            'files'                 => ['nullable', 'array', 'max:10'],
            'files.*'               => ['file', 'mimes:jpg,jpeg,png,gif,webp,svg', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'            => 'Contact person name is required.',
            'email.required'           => 'Email is required.',
            'email.email'              => 'Please provide a valid email.',
            'personal_phone.required'  => 'Personal phone is required.',
            'logo_name.required'       => 'Logo name is required.',
            'business_desc.required'   => 'Business description is required.',
            'logo_description.required'=> 'Logo description is required.',
            'competitors_ref.required' => 'At least one competitor reference is required.',
            'files.*.mimes'            => 'Only image files (jpg, png, gif, webp, svg) are allowed.',
            'files.*.max'              => 'Each file must be under 5MB.',
        ];
    }
}