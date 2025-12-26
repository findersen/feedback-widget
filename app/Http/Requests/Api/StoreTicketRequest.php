<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // public widget
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer' => ['required', 'array'],
            'customer.name' => ['required', 'string', 'max:255'],
            'customer.email' => ['nullable', 'email', 'max:255'],
            'customer.phone' => ['nullable', 'string', 'regex:/^\+[1-9]\d{1,14}$/'],

            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],

            'files' => ['nullable', 'array', 'max:10'],
            'files.*' => ['file', 'max:10240'], // 10MB
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $phone = $this->input('phone');
            $email = $this->input('email');

            if (!$phone && !$email) {
                $validator->errors()->add('phone', 'Phone or email is required.');
                $validator->errors()->add('email', 'Phone or email is required.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'customer.phone.regex' => 'Phone must be in E.164 format, e.g. +380XXXXXXXXX.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $email = $this->input('email');
        $phone = $this->input('phone');

        $this->merge([
            'email' => is_string($email) ? mb_strtolower(trim($email)) : $email,
            'phone' => is_string($phone) ? preg_replace('/\s+/', '', trim($phone)) : $phone,
        ]);
    }
}
