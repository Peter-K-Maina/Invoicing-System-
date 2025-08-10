<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Invoice;

class HomeController extends Controller
{
    public function index()
    {
        $clientCount = Client::count();
        $invoiceCount = Invoice::count();
        $revenue = Invoice::where('status', 'Paid')->sum('total_amount');
        $overdueCount = Invoice::where('status', 'Overdue')->count();

        return view('home', compact('clientCount', 'invoiceCount', 'revenue', 'overdueCount'));
    }
}
