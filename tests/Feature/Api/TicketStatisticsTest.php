<?php

namespace Tests\Feature\Api;

use App\Models\Customer;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketStatisticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_statistics_returns_day_week_month_counts(): void
    {
        Carbon::setTestNow(Carbon::parse('2025-12-26 12:00:00'));

        $customer = Customer::factory()->create();

        // 1 today
        Ticket::factory()->create([
            'customer_id' => $customer->id,
            'created_at' => now()->subHour(),
        ]);

        // 2 this week (but not today)
        Ticket::factory()->count(2)->create([
            'customer_id' => $customer->id,
            'created_at' => now()->startOfDay()->subDays(2)->addHours(10),
        ]);

        // 3 this month (but not this week)
        Ticket::factory()->count(3)->create([
            'customer_id' => $customer->id,
            'created_at' => now()->startOfWeek()->subDays(3)->startOfDay()->addHours(9),
        ]);

        // not this month (should not be counted)
        Ticket::factory()->count(4)->create([
            'customer_id' => $customer->id,
            'created_at' => now()->subMonth()->startOfMonth()->addDay(),
        ]);

        $res = $this->getJson('/api/tickets/statistics');

        $res->assertSuccessful()
            ->assertJsonPath('data.day', 1)
            ->assertJsonPath('data.week', 3)
            ->assertJsonPath('data.month', 6);
    }
}
