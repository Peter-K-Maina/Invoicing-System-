@extends('layouts.app')

@section('content')
    <h2 class="mb-4">Add New Client</h2>

    {{-- Validation Error Messages --}}
    @if ($errors->any())
        <div class="bg-red-100 text-red-800 border border-red-300 px-4 py-2 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('clients.store') }}" method="POST" class="card p-4 shadow-sm">
        @csrf
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Company</label>
                <input type="text" name="company" class="form-control">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" rows="2"></textarea>
        </div>

        <button type="submit" class="btn btn-success">Save Client</button>
    </form>
@endsection
