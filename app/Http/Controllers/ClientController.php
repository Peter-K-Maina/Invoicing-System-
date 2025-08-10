<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
{
    $clients = Client::all();
    return view('clients.index', compact('clients'));
}

public function create()
{
    return view('clients.create');
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'email' => 'required|email',
        'phone' => 'nullable',
        'company' => 'nullable',
        'address' => 'nullable',
    ]);

    Client::create([
    'name' => $request->name,
    'email' => $request->email,
    'phone' => $request->phone,
    'company' => $request->company,
    'address' => $request->address,
    'user_id' => auth()->id(),
]);

    return redirect()->route('clients.index')->with('success', 'Client added successfully');
}

public function edit(Client $client)
{
    return view('clients.edit', compact('client'));
}

public function update(Request $request, Client $client)
{
    $request->validate([
        'name' => 'required',
        'email' => 'required|email',
    ]);

    $client->update($request->all());

    return redirect()->route('clients.index')->with('success', 'Client updated successfully');
}

public function destroy(Client $client)
{
    $client->delete();
    return redirect()->route('clients.index')->with('success', 'Client deleted successfully');
}
}
