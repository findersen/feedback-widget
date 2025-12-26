@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1>Tickets</h1>

    <form method="GET" class="mb-3">
        <input name="q" value="{{ request('q') }}" placeholder="Search (subject/message/customer)" />
        <select name="status">
            <option value="">-- status --</option>
            <option value="new" @selected(request('status')==='new')>new</option>
            <option value="in_progress" @selected(request('status')==='in_progress')>in_progress</option>
            <option value="done" @selected(request('status')==='done')>done</option>
        </select>
        <input type="date" name="from" value="{{ request('from') }}" />
        <input type="date" name="to" value="{{ request('to') }}" />
        <button type="submit">Filter</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>#</th><th>Status</th><th>Subject</th><th>Customer</th><th>Created</th><th></th>
            </tr>
        </thead>
        <tbody>
        @foreach($tickets as $t)
            <tr>
                <td>{{ $t->id }}</td>
                <td>{{ $t->status }}</td>
                <td>{{ $t->subject }}</td>
                <td>{{ $t->customer?->name }} ({{ $t->customer?->email ?? $t->customer?->phone }})</td>
                <td>{{ $t->created_at }}</td>
                <td><a href="{{ route('admin.tickets.show', $t) }}">Open</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $tickets->links() }}
</div>
@endsection
