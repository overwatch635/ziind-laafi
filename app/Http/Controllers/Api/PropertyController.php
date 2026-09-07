<?php

namespace App\Http\Controllers\Api;

// NOUVEAU FICHIER — copié/adapté depuis app/Http/Controllers/PropertyController.php
// Même logique métier que la version web, mais :
//   - les vues (view()) sont remplacées par des réponses JSON
//   - les redirections (redirect()/back()) sont remplacées par des codes HTTP
//   - la sécurité par rôle est conservée à l'identique

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    // GET /api/properties
    public function index(Request $request)
    {
        $properties = Property::where('status', 'publiée')
            ->filter($request->only(['type', 'usage', 'option', 'zone']))
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $properties,
        ]);
    }

    // GET /api/properties/{id}
    public function show($id)
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'error' => 'Bien introuvable.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $property,
        ]);
    }

    // POST /api/properties  (bailleur ou agent connecté)
    public function store(Request $request)
    {
        if (!in_array($request->user()->role, ['bailleur', 'agent'])) {
            return response()->json([
                'success' => false,
                'error' => 'Action non autorisée.',
            ], 403);
        }

        $validated = $request->validate([
            'type' => 'required|string',
            'property_usage' => 'required|string',
            'contract_option' => 'required|string',
            'zone' => 'required|string|max:100',
            'size' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:1',
            'description' => 'nullable|string',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = $request->file('photo')->store('uploads', 'public');

        $status = ($request->user()->role === 'agent') ? 'publiée' : 'en attente';

        $property = Property::create([
            'user_id' => $request->user()->id,
            'type' => $validated['type'],
            'property_usage' => $validated['property_usage'],
            'contract_option' => $validated['contract_option'],
            'zone' => $validated['zone'],
            'size' => $validated['size'],
            'price' => $validated['price'],
            'description' => $validated['description'] ?? null,
            'photo_path' => '/storage/' . $path,
            'status' => $status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Annonce créée avec succès.',
            'data' => $property,
        ], 201);
    }

    // PUT /api/properties/{id}  (propriétaire de l'annonce uniquement)
    public function update(Request $request, $id)
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json(['success' => false, 'error' => 'Bien introuvable.'], 404);
        }

        if ($property->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'error' => 'Action non autorisée.'], 403);
        }

        $validated = $request->validate([
            'type' => 'required|string',
            'property_usage' => 'required|string',
            'contract_option' => 'required|string',
            'zone' => 'required|string',
            'size' => 'required|numeric',
            'price' => 'required|numeric',
            'description' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('properties', 'public');
            $property->photo_path = $path;
        }

        $property->update([
            'type' => $validated['type'],
            'property_usage' => $validated['property_usage'],
            'contract_option' => $validated['contract_option'],
            'zone' => $validated['zone'],
            'size' => $validated['size'],
            'price' => $validated['price'],
            'description' => $validated['description'],
            'status' => 'en attente', // repasse en validation après modification
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Annonce modifiée, en attente de validation.',
            'data' => $property,
        ]);
    }

    // DELETE /api/properties/{id}  (propriétaire de l'annonce uniquement)
    public function destroy(Request $request, $id)
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json(['success' => false, 'error' => 'Bien introuvable.'], 404);
        }

        if ($property->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'error' => 'Action non autorisée.'], 403);
        }

        $property->delete();

        return response()->json([
            'success' => true,
            'message' => 'Annonce supprimée avec succès.',
        ]);
    }
}
