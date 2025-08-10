@extends('layouts.app')

@section('content')
    <h2 class="mb-4">Edit Invoice</h2>

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

    <form action="{{ route('invoices.update', $invoice->id) }}" method="POST" class="card p-4 shadow-sm">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Client</label>
            <select name="client_id" class="form-control" required>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ $invoice->client_id == $client->id ? 'selected' : '' }}>
                        {{ $client->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Amount</label>
            <input type="number" name="amount" class="form-control" step="0.01" value="{{ $invoice->amount }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="2">{{ $invoice->description }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update Invoice</button>
    </form>
@endsection
