<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_can_be_created(): void
    {
        $payload = [
            'name' => 'John',
            'email' => 'test@example.com',
            'phone' => '+380501112233',
            'subject' => 'Hi',
            'message' => 'Test message',
        ];

        $res = $this->postJson('/api/tickets', $payload);

        $res->assertSuccessful()
            ->assertJsonPath('data.subject', 'Hi')
            ->assertJsonPath('data.customer.name', 'John');

        $this->assertDatabaseHas('customers', [
            'email' => 'test@example.com',
        ]);

        $this->assertDatabaseHas('tickets', [
            'subject' => 'Hi',
            'status' => 'new',
        ]);
    }

    public function test_ticket_requires_email_or_phone(): void
    {
        $payload = [
            'name' => 'John',
            'email' => null,
            'phone' => null,
            'subject' => 'Hi',
            'message' => 'Test message',
        ];

        $this->postJson('/api/tickets', $payload)
            ->assertStatus(422);
    }
}
