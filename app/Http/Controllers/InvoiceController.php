<?php 

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use App\Mail\InvoiceMail;
use App\Services\MpesaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
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
        $invoiceNumber = 'INV-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        $clients = Client::all();
        return view('invoices.create', compact('clients', 'invoiceNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_number' => 'required|unique:invoices',
            'invoice_date' => 'required|date_format:d-m-Y',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,paid,overdue',
        ]);

        Invoice::create([
            'client_id' => $request->client_id,
            'user_id' => auth()->id(),
            'invoice_number' => $request->invoice_number,
            'invoice_date' => \Carbon\Carbon::createFromFormat('d-m-Y', $request->invoice_date)->format('Y-m-d'),
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
            'invoice_date' => 'required|date_format:d-m-Y',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,paid,overdue',
        ]);

        

        $invoice->update([
            'client_id' => $request->client_id,
            'invoice_number' => $request->invoice_number,
            'invoice_date' => \Carbon\Carbon::createFromFormat('d-m-Y', $request->invoice_date)->format('Y-m-d'),
            'amount' => $request->amount,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully.');
    }

    public function send(Invoice $invoice)
    {
        try {
            Mail::to($invoice->client->email)->send(new InvoiceMail($invoice));

            $invoice->status = 'pending'; 
            $invoice->save();

            return redirect()
                ->route('invoices.show', $invoice->id)
                ->with('success', 'Invoice sent successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('invoices.show', $invoice->id)
                ->with('error', 'Failed to send invoice: ' . $e->getMessage());
        }
    }

    // Pay() method to use MpesaService
    public function pay(Request $request, Invoice $invoice, MpesaService $mpesa)
    {
        try {
            // Ensure valid whole number amount
            $amount = (int) ceil($invoice->amount);

            if ($amount < 1) {
                return back()->with('error', 'Invalid amount. Must be at least 1 KES.');
            }

            $mpesa->stkPush(
                $request->input('phone'),
                $amount,
                $invoice->id
            );

            return back()->with('success', 'STK Push sent. Check your phone.');
        } catch (\Exception $e) {
            Log::error('Invoice payment error: ' . $e->getMessage());
            return back()->with('error', 'Payment request failed. Please try again later.');
        }
    }


    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted.');
    }

    public function download($id)
    {
        try {
            $invoice = Invoice::with('client')->findOrFail($id);
            
            // Check if the logo file exists
            $logoPath = public_path('images/lipasmart-logo.png');
            if (!file_exists($logoPath)) {
                Log::error('Invoice PDF Generation: Logo file not found at ' . $logoPath);
            }

            // Load the PDF with error handling
            $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
            
            Log::info('Invoice PDF Generation: Successfully generated PDF for invoice #' . $invoice->invoice_number);
            
            return $pdf->download('invoice_' . $invoice->invoice_number . '.pdf');
        } catch (\Exception $e) {
            Log::error('Invoice PDF Generation Failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate PDF. Please try again later.');
        }
    }
}
