@extends('layouts.app')

@section('content')
<div class="container py-4">

    <!-- Header & Add Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Clients</h2>
        <a href="{{ route('clients.create') }}" class="btn btn-primary">+ Add Client</a>
    </div>

    <!-- Clients Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Company</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clients as $client)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $client->name }}</td>
                            <td>{{ $client->email }}</td>
                            <td>{{ $client->phone }}</td>
                            <td>{{ $client->company }}</td>
                            <td class="text-end">
                                <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-outline-primary me-1">Edit</a>
                                <form action="{{ route('clients.destroy', $client) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this client?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
