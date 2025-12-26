<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketRateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_rate_limit_by_email_per_day(): void
    {
        $payload = [
            'name' => 'John',
            'email' => 'test@example.com',
            'phone' => '+380501112233',
            'subject' => 'Hi',
            'message' => 'Test',
        ];

        $this->postJson('/api/tickets', $payload)->assertSuccessful();
        $this->postJson('/api/tickets', $payload)->assertStatus(429);
    }
}
