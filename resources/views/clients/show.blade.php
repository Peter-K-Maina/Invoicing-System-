{{-- resources/views/clients/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Client Details</h3>
        <a href="{{ route('clients.index') }}" class="btn btn-secondary">← Back</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">{{ $client->name }}</h5>
            <p class="card-text"><strong>Email:</strong> {{ $client->email }}</p>
            <p class="card-text"><strong>Phone:</strong> {{ $client->phone }}</p>
            <p class="card-text"><strong>Company:</strong> {{ $client->company }}</p>
            <p class="card-text"><strong>Address:</strong> {{ $client->address }}</p>

            <a href="{{ route('clients.edit', $client) }}" class="btn btn-outline-primary">Edit</a>
        </div>
    </div>
</div>
@endsection
