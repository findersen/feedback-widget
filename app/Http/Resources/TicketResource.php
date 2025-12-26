<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'message' => $this->message,
            'status' => $this->status,
            'manager_answered_at' => $this->manager_answered_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),

            'customer' => [
                'id' => $this->customer?->id,
                'name' => $this->customer?->name,
                'email' => $this->customer?->email,
                'phone' => $this->customer?->phone,
            ],

            'files' => $this->getMedia('attachments')->map(fn ($m) => [
                'id' => $m->id,
                'name' => $m->name,
                'file_name' => $m->file_name,
                'size' => $m->size,
                'mime_type' => $m->mime_type,
                'url' => $m->getUrl(),
            ])->values(),
        ];
    }
}
