{{-- resources/views/invoices/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- Header with Back -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Invoice</h4>
            <p class="text-muted mb-0">#{{ $invoice->invoice_number }}</p>
        </div>
        <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left"></i> Back to Invoices
        </a>
    </div>

    <!-- Invoice Card -->
    <div class="card shadow-lg border-0">
        <!-- Brand Header -->
        <div class="card-header bg-white border-bottom-0 pt-4 px-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <img src="{{ asset('images/lipasmart-logo.png') }}" alt="LipaSmart Logo" height="60">
                    <h3 class="text-primary mt-3 mb-0">INVOICE</h3>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-1 text-muted">Status</p>
                    <span class="badge fs-6 bg-{{ $invoice->status === 'paid' ? 'success-subtle text-success' : ($invoice->status === 'overdue' ? 'danger-subtle text-danger' : 'warning-subtle text-warning') }} px-3 py-2">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <!-- Client and Invoice Details -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-uppercase text-muted mb-3">Bill To</h6>
                    <h5 class="mb-2">{{ $invoice->client->name }}</h5>
                    <p class="mb-0 text-muted">{{ $invoice->client->company }}</p>
                    <p class="mb-0 text-muted">{{ $invoice->client->email }}</p>
                    <p class="mb-0 text-muted">{{ $invoice->client->phone }}</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <h6 class="text-uppercase text-muted mb-3">Invoice Details</h6>
                    <p class="mb-1">
                        <span class="text-muted">Invoice Date:</span> {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}
                    </p>
                    <p class="mb-1">
                        <span class="text-muted">Due Date:</span> {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}
                    </p>
                    <h3 class="mt-3 mb-0 text-primary">KES {{ number_format($invoice->amount, 2) }}</h3>
                </div>
            </div>

            <!-- Description -->
            <div class="bg-light p-4 rounded-3 mb-4">
                <h6 class="text-uppercase text-muted mb-3">Description</h6>
                <p class="mb-0">{{ $invoice->description ?? 'Service Rendered' }}</p>
            </div>

            <!-- Actions -->
            <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center">
                <div class="d-flex gap-3">
                    <a href="{{ route('invoices.download', $invoice->id) }}" class="btn btn-outline-primary">
                        <i class="fa fa-file-pdf me-2"></i> Download PDF
                    </a>

                    {{-- Only show "Send Invoice" if status is pending --}}
                    @if($invoice->status === 'pending')
                        <form action="{{ route('invoices.send', $invoice->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-success">
                                <i class="fa fa-envelope me-2"></i> Send Invoice
                            </button>
                        </form>
                    @endif
                </div>

                {{-- Payment Section --}}
                @if($invoice->status !== 'paid')
                    <div>
                        <form action="{{ route('mpesa.demo.pay', $invoice) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-primary">
                                <i class="fa fa-mobile-alt me-2"></i> Pay with M-Pesa
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            {{-- Show payment form only if status is pending --}}
            @if($invoice->status === 'pending')
                <div class="mt-6 border-top pt-4">
                    <h5 class="mb-3">Pay with M-Pesa</h5>
                    <form action="{{ route('invoices.pay', $invoice->id) }}" method="POST" class="d-flex flex-column gap-3">
                        @csrf
                        <div>
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" name="phone" id="phone"
                                   placeholder="e.g. 2547XXXXXXXX"
                                   class="form-control w-auto" required>
                        </div>
                        <div>
                            <label for="amount" class="form-label">Amount</label>
                            <input type="text" name="amount" id="amount"
                                   value="{{ $invoice->amount }}"
                                   readonly class="form-control w-auto bg-light">
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-mobile-alt"></i> Pay Now
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
