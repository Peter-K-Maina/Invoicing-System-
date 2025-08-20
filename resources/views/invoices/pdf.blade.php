<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { 
            font-family: 'Helvetica', sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 2cm;
        }
        .header { 
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 40px;
        }
        .logo {
            width: 120px;
            margin-bottom: 20px;
        }
        .invoice-title {
            color: #2563eb;
            font-size: 36px;
            margin: 0;
        }
        .invoice-number {
            color: #6b7280;
            font-size: 18px;
            margin: 5px 0;
        }
        .status {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
        }
        .status-paid { background: #dcfce7; color: #166534; }
        .status-pending { background: #fef9c3; color: #854d0e; }
        .status-overdue { background: #fee2e2; color: #991b1b; }
        .section {
            margin-bottom: 40px;
        }
        .section-title {
            color: #6b7280;
            text-transform: uppercase;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .details { 
            width: 100%;
        }
        .details td {
            padding: 8px 0;
            line-height: 1.4;
        }
        .amount {
            font-size: 24px;
            color: #2563eb;
            font-weight: bold;
        }
        .footer {
            margin-top: 60px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('images/lipasmart-logo.png') }}" alt="LipaSmart" class="logo" style="width: 120px; height: auto;">
        <table style="width: 100%">
            <tr>
                <td>
                    <h1 class="invoice-title">INVOICE</h1>
                    <p class="invoice-number">#{{ $invoice->invoice_number }}</p>
                </td>
                <td style="text-align: right">
                    <div class="status status-{{ $invoice->status }}">
                        {{ ucfirst($invoice->status) }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table style="width: 100%">
            <tr>
                <td style="width: 50%">
                    <div class="section-title">Bill To</div>
                    <strong>{{ $invoice->client->name }}</strong><br>
                    {{ $invoice->client->company }}<br>
                    {{ $invoice->client->email }}<br>
                    {{ $invoice->client->phone }}
                </td>
                <td style="text-align: right">
                    <div class="section-title">Invoice Details</div>
                    <strong>Date:</strong> {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}<br>
                    <strong>Due Date:</strong> {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}<br>
                    <div class="amount" style="margin-top: 10px">
                        KES {{ number_format($invoice->amount, 2) }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section" style="background: #f9fafb; padding: 20px; border-radius: 8px;">
        <div class="section-title">Description</div>
        <p style="margin: 0">{{ $invoice->description ?? 'Service Rendered' }}</p>
    </div>

    <table class="details" style="margin-top: 40px">
        <tr>
            <td style="width: 70%"></td>
            <td>
                <table style="width: 100%">
                    <tr>
                        <td>Subtotal</td>
                        <td style="text-align: right">KES {{ number_format($invoice->amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"><hr style="border: none; border-top: 1px solid #e5e7eb;"></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold">Total Amount</td>
                        <td style="text-align: right; font-weight: bold">KES {{ number_format($invoice->amount, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="footer">
        <p>Thank you for your business!</p>
        <p style="margin-top: 10px">
            <strong>LipaSmart Invoicing System</strong><br>
            Making payments smarter and easier
        </p>
    </div>
</body>
</html>
