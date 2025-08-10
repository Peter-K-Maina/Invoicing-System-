<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('client')->latest()->get();
        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $latestInvoice = Invoice::latest()->first();
        $nextNumber = $latestInvoice ? $latestInvoice->id + 1 : 1;
        $invoiceNumber = 'INV-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT); // e.g., INV-00001

        $clients = Client::all();
        return view('invoices.create', compact('clients', 'invoiceNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_number' => 'required|unique:invoices',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:unpaid,paid,overdue',
        ]);

        Invoice::create([
            'client_id' => $request->client_id,
            'user_id' => auth()->id(),
            'invoice_number' => $request->invoice_number,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'amount' => $request->amount,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $clients = Client::all();
        return view('invoices.edit', compact('invoice', 'clients'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_number' => 'required|unique:invoices,invoice_number,' . $invoice->id,
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:unpaid,paid,overdue',
        ]);

        $invoice->update([
            'client_id' => $request->client_id,
            'invoice_number' => $request->invoice_number,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'amount' => $request->amount,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted.');
    }

    public function download($id)
    {
        $invoice = Invoice::with('client')->findOrFail($id);
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        return $pdf->download('invoice_' . $invoice->invoice_number . '.pdf');
    }
}
