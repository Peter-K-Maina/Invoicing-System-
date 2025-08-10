{{-- resources/views/invoices/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container py-4">

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>All Invoices</h3>
        <a href="{{ route('invoices.create') }}" class="btn btn-primary">+ New Invoice</a>
    </div>

    <!-- Card with Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Invoice No.</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Due</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $invoice->client->name }}</td>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>KES {{ number_format($invoice->total_amount) }}</td>
                            <td>
                                <span class="badge bg-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'overdue' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($invoice->due_date)->diffForHumans() }}</td>
                            <td>
                                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-outline-primary">View</a>
                                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                <a href="{{ route('invoices.download', $invoice->id) }}" class="btn btn-sm btn-outline-dark">Download PDF</a>
                                <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
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
