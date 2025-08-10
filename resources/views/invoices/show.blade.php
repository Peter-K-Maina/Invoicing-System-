{{-- resources/views/invoices/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container py-5">

    <!-- Header with Back -->
    <div class="d-flex justify-content-between mb-4">
        <h4>Invoice: #{{ $invoice->invoice_number }}</h4>
        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">← Back</a>
    </div>

    <!-- Invoice Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            Invoice #{{ $invoice->invoice_number }}
        </div>

        <div class="card-body">
            <h5 class="card-title">{{ $invoice->client->name }}</h5>
            <p class="card-text">
                <strong>Company:</strong> {{ $invoice->client->company }} <br>
                <strong>Invoice Date:</strong> {{ \Carbon\Carbon::parse($invoice->invoice_date)->toFormattedDateString() }} <br>
                <strong>Due Date:</strong> {{ \Carbon\Carbon::parse($invoice->due_date)->toFormattedDateString() }} <br>
                <strong>Total Amount:</strong> KES {{ number_format($invoice->total_amount) }} <br>
                <strong>Status:</strong>
                <span class="badge bg-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'overdue' ? 'danger' : 'warning') }}">
                    {{ ucfirst($invoice->status) }}
                </span>
            </p>

            <!-- Actions -->
            <div class="mt-4 d-flex flex-wrap gap-3">
                <a href="{{ route('invoices.download', $invoice->id) }}" class="btn btn-outline-dark">
                    <i class="fa fa-file-pdf"></i> Download PDF
                </a>

                <form method="POST" action="{{ route('mpesa.stkpush') }}" class="d-flex align-items-center gap-2">
                    @csrf
                    <input type="hidden" name="amount" value="{{ $invoice->total_amount }}">
                    <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                    <input type="text" name="phone" placeholder="2547..." class="form-control w-auto" required>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-mobile-alt"></i> Pay with M-Pesa
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
