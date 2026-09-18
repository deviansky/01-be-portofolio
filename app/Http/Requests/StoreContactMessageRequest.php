<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // endpoint publik
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'website' => ['nullable', 'string'], // honeypot
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Isi nama kamu.',
            'email.required' => 'Isi email kamu.',
            'email.email' => 'Isi email yang valid, contoh: nama@domain.com.',
            'message.required' => 'Isi pesan kamu.',
            'message.min' => 'Pesan minimal 10 karakter.',
            'message.max' => 'Pesan maksimal 5.000 karakter.',
        ];
    }
}
