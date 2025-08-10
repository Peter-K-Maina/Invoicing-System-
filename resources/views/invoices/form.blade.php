<form method="POST" action="{{ isset($invoice) ? route('invoices.update', $invoice) : route('invoices.store') }}">
    @csrf
    @if(isset($invoice))
        @method('PUT')
    @endif

    <div class="mb-3">
        <label for="client_id" class="form-label">Client</label>
        <select name="client_id" id="client_id" class="form-select" required>
            <option value="">-- Select Client --</option>
            @foreach($clients as $client)
                <option value="{{ $client->id }}" {{ (isset($invoice) && $invoice->client_id == $client->id) ? 'selected' : '' }}>
                    {{ $client->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="invoice_number" class="form-label">Invoice Number</label>
        <input type="text" name="invoice_number" id="invoice_number" class="form-control" value="{{ old('invoice_number', $invoice->invoice_number ?? '') }}" required>
    </div>

    <div class="mb-3">
        <label for="invoice_date" class="form-label">Invoice Date</label>
        <input type="date" name="invoice_date" id="invoice_date" class="form-control" value="{{ old('invoice_date', isset($invoice) ? $invoice->invoice_date->format('Y-m-d') : '') }}" required>
    </div>

    <div class="mb-3">
        <label for="due_date" class="form-label">Due Date</label>
        <input type="date" name="due_date" id="due_date" class="form-control" value="{{ old('due_date', isset($invoice) ? $invoice->due_date->format('Y-m-d') : '') }}">
    </div>

    <div class="mb-3">
        <label for="amount" class="form-label">Amount</label>
        <input type="number" step="0.01" name="amount" id="amount" class="form-control" value="{{ old('amount', $invoice->amount ?? '') }}" required>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" id="description" rows="4" class="form-control">{{ old('description', $invoice->description ?? '') }}</textarea>
    </div>

    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select name="status" id="status" class="form-select" required>
            <option value="unpaid" {{ (isset($invoice) && $invoice->status == 'unpaid') ? 'selected' : '' }}>Unpaid</option>
            <option value="paid" {{ (isset($invoice) && $invoice->status == 'paid') ? 'selected' : '' }}>Paid</option>
            <option value="overdue" {{ (isset($invoice) && $invoice->status == 'overdue') ? 'selected' : '' }}>Overdue</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">
        {{ isset($invoice) ? 'Update Invoice' : 'Create Invoice' }}
    </button>
</form>
