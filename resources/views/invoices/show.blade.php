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
                <strong>Total Amount:</strong> KES {{ number_format($invoice->amount, 2) }} <br>
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

                {{-- Only show "Send Invoice" if status is pending --}}
                @if($invoice->status === 'pending')
                    <form action="{{ route('invoices.send', $invoice->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-envelope"></i> Send Invoice
                        </button>
                    </form>
                @endif

                {{-- Demo Payment Button (Sandbox) --}}
                @if($invoice->status !== 'paid')
                    <form action="{{ route('mpesa.demo.pay', $invoice) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-secondary">
                            Demo Payment (Sandbox)
                        </button>
                    </form>
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
