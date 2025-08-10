@extends('layouts.app')

@section('content')
<div class="container py-5">

    <!-- Welcome Banner -->
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold text-primary">Welcome to Your Invoicing System</h1>
        <p class="lead text-muted">Manage your clients and invoices all in one place.</p>

        <div class="row justify-content-center gap-3 mt-4">
            <div class="col-md-4">
                <a href="{{ route('clients.index') }}" class="btn btn-outline-primary w-100 py-3 shadow-sm">
                    <i class="fa fa-users me-2"></i> Manage Clients
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('invoices.index') }}" class="btn btn-outline-success w-100 py-3 shadow-sm">
                    <i class="fa fa-file-invoice-dollar me-2"></i> Manage Invoices
                </a>
            </div>
        </div>
    </div>

    <!-- Dashboard Cards -->
    <div class="row text-white">
        <div class="col-md-3 mb-4">
            <div class="card bg-primary shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Total Clients</h5>
                    <h3>{{ $clientCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-success shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Total Invoices</h5>
                    <h3>{{ $invoiceCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-warning shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Revenue (Paid)</h5>
                    <h3>KES {{ number_format($revenue) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-danger shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Overdue Invoices</h5>
                    <h3>{{ $overdueCount }}</h3>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
