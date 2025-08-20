
@extends('layouts.app')

@section('content')
    <h2 class="mb-4">Create Invoice</h2>

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

    <form action="{{ route('invoices.store') }}" method="POST" class="card p-4 shadow-sm">
        @csrf

        {{-- Invoice Number --}}
        <div class="mb-3">
            <label for="invoice_number" class="form-label">Invoice Number</label>
            <input type="text" name="invoice_number" id="invoice_number" class="form-control" value="{{ $invoiceNumber }}" readonly>
        </div>

        {{-- Client --}}
        <div class="mb-3">
            <label class="form-label">Client</label>
            <select name="client_id" class="form-control" required>
                <option value="">Select Client</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Amount --}}
        <div class="mb-3">
            <label class="form-label">Amount</label>
            <input type="number" name="amount" class="form-control" step="0.01" required>
        </div>

        {{-- Description --}}
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="2"></textarea>
        </div>

        {{-- Date --}}
        <div class="mb-3">
            <label for="invoice_date" class="form-label">Date</label>
            <input type="text" name="invoice_date" id="invoice_date" class="form-control datepicker" value="{{ date('d-m-Y') }}" required>
        </div>

        {{-- Status --}}
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-control" required>
                <option value="pending" selected>Pending</option>
                <option value="paid">Paid</option>
                <option value="overdue">Overdue</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Save Invoice</button>
    </form>
@endsection

@section('scripts')
    <script>
        // Initialize flatpickr for date input
        document.addEventListener("DOMContentLoaded", function () {
            flatpickr(".datepicker", {
                dateFormat: "d-m-Y",
                defaultDate: "{{ date('d-m-Y') }}"
            });
        });
    </script>
@endsection

