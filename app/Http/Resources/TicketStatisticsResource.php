<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin array{day:int,week:int,month:int} */
class TicketStatisticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'day' => (int) ($this['day'] ?? 0),
            'week' => (int) ($this['week'] ?? 0),
            'month' => (int) ($this['month'] ?? 0),
        ];
    }
}
