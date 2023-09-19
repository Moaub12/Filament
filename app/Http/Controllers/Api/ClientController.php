<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Adress;
use App\Models\Client;
use App\Models\ClientDetails;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function getClientDetails($clientId)
{
    
    $client = Client::with('clientDetails', 'adress')->find($clientId);

    if (!$client) {
        return response()->json(['message' => 'Client not found'], 404);
    }

    return response()->json($client);

    
}

public function createAddress(Request $request, $clientId)
{
    $request->validate([
        'name' => 'required',
    ]);

    $client = Client::find($clientId);

    if (!$client) {
        return response()->json(['message' => 'Client not found'], 404);
    }

    $address = new Adress($request->all());
    $client->adress()->save($address);

    return response()->json(['message' => 'Address created'], 201);
}



public function updateClientDetails(Request $request, $clientId)
{
    $request->validate([
        'fname' => 'required',
        'lname' => 'required',
        'company_name' => 'required',
        'country' => 'required',
        'city' => 'required',
        'state' => 'required',
        'zip' => 'required',
    ]);

    $client = Client::find($clientId);

    if (!$client) {
        return response()->json(['message' => 'Client not found'], 404);
    }

    $client->clientDetails->update($request->all());

    return response()->json(['message' => 'Client details updated'], 200);
}
}
