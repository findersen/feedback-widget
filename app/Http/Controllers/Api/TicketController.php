<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\StoreTicketRequest;
use App\Http\Resources\TicketResource;
use App\Http\Resources\TicketStatisticsResource;
use App\Services\TicketService;
use App\Models\Ticket;

class TicketController extends Controller
{
    public function __construct(
        private readonly TicketService $tickets,
    ) {}

    public function store(StoreTicketRequest $request): TicketResource
    {
        $files = collect($request->file('files', []));
        $ticket = $this->tickets->createFromWidget($request->validated(), $files);

        return new TicketResource($ticket);
    }

    public function statistics(): TicketStatisticsResource
    {
        $stats = [
            'day' => Ticket::query()->forToday()->count(),
            'week' => Ticket::query()->forThisWeek()->count(),
            'month' => Ticket::query()->forThisMonth()->count(),
        ];

        return new TicketStatisticsResource($stats);
    }
}
