@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Ticket #{{ $ticket->id }}</h1>

    @if(session('status'))
        <div>{{ session('status') }}</div>
    @endif

    <p><b>Status:</b> {{ $ticket->status }}</p>
    <p><b>Subject:</b> {{ $ticket->subject }}</p>
    <p><b>Message:</b><br>{{ $ticket->message }}</p>

    <hr>
    <h3>Customer</h3>
    <p>{{ $ticket->customer->name }}</p>
    <p>{{ $ticket->customer->email }}</p>
    <p>{{ $ticket->customer->phone }}</p>

    <hr>
    <h3>Attachments</h3>
    <ul>
        @foreach($ticket->getMedia('attachments') as $media)
            <li><a href="{{ $media->getUrl() }}" target="_blank">{{ $media->file_name }}</a></li>
        @endforeach
    </ul>

    <hr>
    <h3>Update status</h3>
    <form method="POST" action="{{ route('admin.tickets.status', $ticket) }}">
        @csrf
        @method('PATCH')

        <select name="status" required>
            <option value="new" @selected($ticket->status==='new')>new</option>
            <option value="in_progress" @selected($ticket->status==='in_progress')>in_progress</option>
            <option value="done" @selected($ticket->status==='done')>done</option>
        </select>

        <button type="submit">Save</button>
    </form>
</div>
@endsection
