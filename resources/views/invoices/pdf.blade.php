<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice PDF</title>
    <style>
        body { font-family: sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .details, .items { width: 100%; margin-bottom: 20px; }
        .details th, .items th, .items td { border: 1px solid #000; padding: 8px; text-align: left; }
        .items th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <div class="header">
        <h2>INVOICE</h2>
        <p>Invoice #: {{ $invoice->invoice_number }}</p>
        <p>Date: {{ $invoice->invoice_date }}</p>
    </div>

    <table class="details">
        <tr>
            <th>Client</th>
            <td>{{ $invoice->client->name }}</td>
        </tr>
        <tr>
            <th>Company</th>
            <td>{{ $invoice->client->company }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $invoice->client->email }}</td>
        </tr>
        <tr>
            <th>Phone</th>
            <td>{{ $invoice->client->phone }}</td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Description</th>
                <th>Amount (KES)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $invoice->description ?? 'Service Rendered' }}</td>
                <td>{{ number_format($invoice->total_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <p><strong>Status:</strong> {{ $invoice->status }}</p>
    <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</p>
</body>
</html>
