<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::query()
            ->with('customer')
            ->latest();

        // filters
        $query->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')));
        $query->when($request->filled('q'), function ($q) use ($request) {
            $term = trim((string) $request->input('q'));

            $q->where(function ($qq) use ($term) {
                $qq->where('subject', 'ilike', "%{$term}%")
                    ->orWhere('message', 'ilike', "%{$term}%")
                    ->orWhereHas('customer', function ($cq) use ($term) {
                        $cq->where('name', 'ilike', "%{$term}%")
                           ->orWhere('email', 'ilike', "%{$term}%")
                           ->orWhere('phone', 'ilike', "%{$term}%");
                    });
            });
        });

        $query->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('from')));
        $query->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('to')));

        $tickets = $query->paginate(20)->withQueryString();

        return view('admin.tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load('customer', 'media');

        return view('admin.tickets.show', compact('ticket'));
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:' . implode(',', [
                Ticket::STATUS_NEW,
                Ticket::STATUS_IN_PROGRESS,
                Ticket::STATUS_DONE,
            ])],
        ]);

        $ticket->status = $validated['status'];

        if ($ticket->manager_answered_at === null) {
            $ticket->manager_answered_at = now();
        }

        $ticket->save();

        return redirect()
            ->route('admin.tickets.show', $ticket)
            ->with('status', 'Status updated.');
    }
}
