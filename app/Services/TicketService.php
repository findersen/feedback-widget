<?php

namespace App\Services;

use App\Models\Ticket;
use App\Repositories\CustomerRepository;
use App\Repositories\TicketRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class TicketService
{
    public function __construct(
        private readonly CustomerRepository $customers,
        private readonly TicketRepository $tickets,
    ) {}

    /**
     * @param array{customer: array{name:string,email?:string|null,phone?:string|null}, subject:string, message:string} $payload
     * @param Collection<int, UploadedFile> $files
     */
    public function createFromWidget(array $payload, Collection $files): Ticket
    {
        $customerData = $payload['customer'];

        $customer = $this->customers->findOrCreateByContacts(
            $customerData['email'] ?? null,
            $customerData['phone'] ?? null,
            $customerData['name'],
        );

        $ticket = $this->tickets->create([
            'customer_id' => $customer->id,
            'subject' => $payload['subject'],
            'message' => $payload['message'],
            'status' => Ticket::STATUS_NEW,
            'manager_replied_at' => null,
        ]);

        if ($files->isNotEmpty()) {
            foreach ($files as $file) {
                $ticket->addMedia($file)->toMediaCollection('attachments');
            }
        }

        return $ticket->load(['customer', 'media']);
    }
}
