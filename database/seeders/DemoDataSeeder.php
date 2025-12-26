<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // manager
        $manager = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager',
                'password' => Hash::make('password'),
            ],
        );

        if (! $manager->hasRole('manager')) {
            $manager->assignRole('manager');
        }

        // customers
        $customers = Customer::factory()
            ->count(8)
            ->create();

        // customer tickets
        foreach ($customers as $customer) {
            Ticket::factory()
                ->count(fake()->numberBetween(1, 4))
                ->create(['customer_id' => $customer->id]);
        }
    }
}
