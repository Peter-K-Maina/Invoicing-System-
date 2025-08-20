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
            <label class="form-label">Invoice Number</label>
            <input type="text" name="invoice_number" class="form-control"
                   value="{{ old('invoice_number', $invoice->invoice_number) }}" required>
        </div>

        <div class="mb-3">
    <label class="form-label">Invoice Date</label>
    <input type="text" name="invoice_date" class="form-control"
           value="{{ old('invoice_date', (is_a($invoice->invoice_date, 'Illuminate\Support\Carbon') ? $invoice->invoice_date->format('d-m-Y') : \Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y'))) }}"
           required>
    <small class="text-muted">Format: DD-MM-YYYY</small>
</div>

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

        {{-- Status Dropdown --}}
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-control" required>
                <option value="pending" {{ $invoice->status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ $invoice->status == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="overdue" {{ $invoice->status == 'overdue' ? 'selected' : '' }}>Overdue</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update Invoice</button>
    </form>
    @endsection
