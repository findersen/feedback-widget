@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Header --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Tickets</h1>
                <p class="text-sm text-gray-500">Manage incoming requests from the widget</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 mb-6">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-900">Filters</h2>
            </div>

            <form method="GET" class="px-6 py-5">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    {{-- Search --}}
                    <div class="md:col-span-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Search</label>
                        <input
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Subject / message / customer"
                            class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        />
                    </div>

                    {{-- Status --}}
                    <div class="md:col-span-3">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                        <select
                            name="status"
                            class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">All statuses</option>
                            <option value="new" @selected(request('status')==='new')>new</option>
                            <option value="in_progress" @selected(request('status')==='in_progress')>in_progress</option>
                            <option value="done" @selected(request('status')==='done')>done</option>
                        </select>
                    </div>

                    {{-- From --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">From</label>
                        <input
                            type="date"
                            name="from"
                            value="{{ request('from') }}"
                            class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        />
                    </div>

                    {{-- To --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">To</label>
                        <input
                            type="date"
                            name="to"
                            value="{{ request('to') }}"
                            class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        />
                    </div>
                </div>

                <div class="mt-5 flex flex-col sm:flex-row sm:items-center gap-3">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Apply filters
                    </button>

                    <a
                        href="{{ route('admin.tickets.index') }}"
                        class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                    >
                        Reset
                    </a>

                    <div class="sm:ml-auto text-xs text-gray-500">
                        Tip: search works by subject, message and customer fields
                    </div>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Subject</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($tickets as $t)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                    {{ $t->id }}
                                </td>

                                <td class="px-6 py-4 text-sm">
                                    @php
                                        $badge = match($t->status) {
                                            'new' => 'bg-amber-50 text-amber-700 ring-amber-200',
                                            'in_progress' => 'bg-blue-50 text-blue-700 ring-blue-200',
                                            'done' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                                            default => 'bg-gray-50 text-gray-700 ring-gray-200',
                                        };
                                    @endphp

                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $badge }}">
                                        {{ $t->status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="font-medium">{{ $t->subject }}</div>
                                    <div class="text-xs text-gray-500 line-clamp-1">
                                        {{ \Illuminate\Support\Str::limit($t->message, 80) }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <div class="font-medium text-gray-900">{{ $t->customer?->name }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $t->customer?->email ?? $t->customer?->phone }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <div class="font-medium text-gray-900">
                                        {{ $t->created_at?->format('Y-m-d H:i') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $t->created_at?->diffForHumans() }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <a
                                        href="{{ route('admin.tickets.show', $t) }}"
                                        class="inline-flex items-center rounded-xl bg-white px-3 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                                    >
                                        Open
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center">
                                    <div class="text-sm font-semibold text-gray-900">No tickets found</div>
                                    <div class="text-sm text-gray-500 mt-1">Try adjusting your filters.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $tickets->withQueryString()->links() }}
            </div>
        </div>

    </div>
</div>
@endsection
