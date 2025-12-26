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

            // хоча б одне з двох:
            'customer.email' => ['nullable', 'email', 'max:255', 'required_without:customer.phone'],
            'customer.phone' => ['nullable', 'string', 'regex:/^\+[1-9]\d{1,14}$/', 'required_without:customer.email'],

            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],

            'files' => ['nullable', 'array', 'max:10'],
            'files.*' => ['file', 'max:10240'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $customer = $this->input('customer');
        $customer = is_array($customer) ? $customer : [];

        // підтримка плоских полів з форми віджета (name/email/phone)
        $customer['name']  = $customer['name']  ?? $this->input('name');
        $customer['email'] = $customer['email'] ?? $this->input('email');
        $customer['phone'] = $customer['phone'] ?? $this->input('phone');

        if (isset($customer['email']) && is_string($customer['email'])) {
            $customer['email'] = mb_strtolower(trim($customer['email']));
        }

        if (isset($customer['phone']) && is_string($customer['phone'])) {
            $customer['phone'] = preg_replace('/\s+/', '', trim($customer['phone']));
        }

        $this->merge(['customer' => $customer]);
    }

    public function messages(): array
    {
        return [
            'customer.phone.regex' => 'Phone must be in E.164 format, e.g. +380XXXXXXXXX.',
            'customer.email.required_without' => 'Phone or email is required.',
            'customer.phone.required_without' => 'Phone or email is required.',
        ];
    }
}
