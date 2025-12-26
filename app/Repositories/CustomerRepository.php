<?php

namespace App\Repositories;

use App\Models\Customer;

class CustomerRepository
{
    public function findOrCreateByContacts(?string $email, ?string $phone, string $name): Customer
    {
        $query = Customer::query();

        if ($email) {
            $query->orWhere('email', $email);
        }

        if ($phone) {
            $query->orWhere('phone', $phone);
        }

        $customer = $query->first();

        if ($customer) {
            if ($customer->name !== $name) {
                $customer->update(['name' => $name]);
            }

            return $customer;
        }

        return Customer::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
        ]);
    }
}
