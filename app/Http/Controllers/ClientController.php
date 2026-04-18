<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $clients = Client::select('id', 'name', 'email', 'phone', 'tax_number')
            ->where('is_active', true)
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
                });
            })
            ->orderBy('name')
            ->get();

        return response()->json($clients);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'nullable|email|max:255',
            'phone'       => 'nullable|string|max:50',
            'address'     => 'nullable|string|max:500',
            'city'        => 'nullable|string|max:100',
            'state'       => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country'     => 'nullable|string|max:100',
            'tax_number'  => 'nullable|string|max:100',
        ]);

        $validated['is_active'] = true;

        $client = Client::create($validated);

        return response()->json([
            'message' => 'Client created successfully',
            'client'  => $client,
        ], 201);
    }

    public function show(Client $client): JsonResponse
    {
        return response()->json($client->load('invoices'));
    }

    public function update(Request $request, Client $client): JsonResponse
    {
        $validated = $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'email'       => 'nullable|email|max:255',
            'phone'       => 'nullable|string|max:50',
            'address'     => 'nullable|string|max:500',
            'city'        => 'nullable|string|max:100',
            'state'       => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country'     => 'nullable|string|max:100',
            'tax_number'  => 'nullable|string|max:100',
            'is_active'   => 'sometimes|boolean',
        ]);

        $client->update($validated);

        return response()->json([
            'message' => 'Client updated successfully',
            'client'  => $client->fresh(),
        ]);
    }

    public function destroy(Client $client): JsonResponse
    {
        $client->delete(); // soft delete via SoftDeletes trait

        return response()->json([
            'message' => 'Client deleted successfully',
        ]);
    }
}