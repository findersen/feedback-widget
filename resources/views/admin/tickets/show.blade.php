@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Top bar --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-semibold tracking-tight text-gray-900">
                    Ticket #{{ $ticket->id }}
                </h1>

                @php
                    $statusClasses = match ($ticket->status) {
                        'new' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                        'in_progress' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                        'done' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                        default => 'bg-gray-50 text-gray-700 ring-gray-600/20',
                    };
                    $statusLabel = str_replace('_', ' ', $ticket->status);
                @endphp

                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $statusClasses }}">
                    {{ $statusLabel }}
                </span>
            </div>

            <p class="mt-1 text-sm text-gray-500">
                Created: <span class="font-medium text-gray-700">{{ $ticket->created_at->format('Y-m-d H:i') }}</span>
                @if($ticket->manager_replied_at)
                    • Manager replied: <span class="font-medium text-gray-700">{{ $ticket->manager_replied_at->format('Y-m-d H:i') }}</span>
                @endif
            </p>
        </div>

        <a href="{{ route('admin.tickets.index') }}"
           class="inline-flex items-center justify-center rounded-lg bg-white px-3 py-2 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            ← Back
        </a>
    </div>

    {{-- Flash --}}
    @if(session('status'))
        <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-12 space-y-6">
        {{-- Main --}}
        <div class="lg:col-span-8 space-y-6">
            {{-- Ticket content card --}}
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900">Details</h2>

                    <div class="mt-4">
                        <p class="text-sm font-medium text-gray-500">Subject</p>
                        <p class="mt-1 text-base font-medium text-gray-900">
                            {{ $ticket->subject }}
                        </p>
                    </div>

                    <div class="mt-5">
                        <p class="text-sm font-medium text-gray-500">Message</p>
                        <div class="mt-2 rounded-xl bg-gray-50 ring-1 ring-inset ring-gray-200 p-4">
                            <p class="whitespace-pre-wrap text-sm leading-6 text-gray-800">
                                {{ $ticket->message }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Attachments --}}
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Attachments</h2>
                        <span class="text-sm text-gray-500">
                            {{ $ticket->getMedia('attachments')->count() }} file(s)
                        </span>
                    </div>

                    @php($attachments = $ticket->getMedia('attachments'))

                    @if($attachments->isEmpty())
                        <p class="mt-4 text-sm text-gray-500">No attachments.</p>
                    @else
                        <ul class="mt-4 divide-y divide-gray-100 rounded-xl ring-1 ring-gray-200">
                            @foreach($attachments as $media)
                                <li class="flex items-center justify-between gap-3 p-4">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-medium text-gray-900">
                                            {{ $media->file_name }}
                                        </p>
                                        <p class="mt-1 text-xs text-gray-500">
                                            Uploaded: {{ $media->created_at?->format('Y-m-d H:i') }}
                                            @if($media->size)
                                                • {{ number_format($media->size / 1024, 1) }} KB
                                            @endif
                                        </p>
                                    </div>

                                    <a href="{{ $media->getUrl() }}" target="_blank"
                                       class="inline-flex items-center justify-center rounded-lg bg-white px-3 py-2 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                        Open
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-4 space-y-6">
            {{-- Customer --}}
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900">Customer</h2>

                    <div class="mt-4 space-y-3">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Name</p>
                            <p class="mt-1 text-sm font-medium text-gray-900">{{ $ticket->customer->name }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Email</p>
                            <p class="mt-1 text-sm text-gray-800">
                                {{ $ticket->customer->email ?: '—' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Phone</p>
                            <p class="mt-1 text-sm text-gray-800">
                                {{ $ticket->customer->phone ?: '—' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Update status --}}
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Update status</h2>
                            <p class="mt-1 text-sm text-gray-500">
                                Change ticket status (sets <span class="font-medium">manager_replied_at</span> if implemented).
                            </p>
                        </div>

                        {{-- optional маленький бейдж поточного статусу --}}
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset
                            @class([
                                'bg-gray-50 text-gray-700 ring-gray-200' => $ticket->status === 'new',
                                'bg-amber-50 text-amber-700 ring-amber-200' => $ticket->status === 'in_progress',
                                'bg-emerald-50 text-emerald-700 ring-emerald-200' => $ticket->status === 'done',
                            ])">
                            {{ $ticket->status }}
                        </span>
                    </div>

                    <form method="POST"
                        action="{{ route('admin.tickets.status', $ticket) }}">
                        @csrf
                        @method('PATCH')

                        <div class="w-full sm:max-w-xs">
                            <label for="status" class="block text-sm font-medium text-gray-700">
                                Status
                            </label>

                            <div class="mt-2 flex flex-col gap-4 sm:flex-row sm:items-end">
                                <select id="status" name="status" required
                                        class="block w-full rounded-xl border-0 bg-white px-3 py-3 text-sm text-gray-900
                                            ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                                    <option value="new" @selected($ticket->status==='new')>new</option>
                                    <option value="in_progress" @selected($ticket->status==='in_progress')>in_progress</option>
                                    <option value="done" @selected($ticket->status==='done')>done</option>
                                </select>

                                <button type="submit"
                                    class="inline-flex h-11 items-center justify-center rounded-xl bg-indigo-600 px-5 text-sm font-semibold
                                        text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2
                                        sm:w-auto">
                                    Save
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
