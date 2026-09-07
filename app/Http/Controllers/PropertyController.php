<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;

class PropertyController extends Controller
{
    // [GET] /api/v1/properties - Liste de tous les biens
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Property::all(),
            'message' => 'Liste des biens récupérée avec succès.'
        ], 200);
    }

    // [POST] /api/v1/properties - Créer un nouveau bien
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $property = Property::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $property,
            'message' => 'Bien créé avec succès.'
        ], 201);
    }

    // [GET] /api/v1/properties/{id} - Détails d'un bien spécifique
    public function show($id)
    {
        $property = Property::find($id);
        if (!$property) {
            return response()->json(['success' => false, 'message' => 'Bien introuvable.'], 404);
        }
        return response()->json(['success' => true, 'data' => $property, 'message' => 'Détails du bien.'], 200);
    }

    // [PUT/PATCH] /api/v1/properties/{id} - Modifier un bien
    public function update(Request $request, $id)
    {
        $property = Property::find($id);
        if (!$property) {
            return response()->json(['success' => false, 'message' => 'Bien introuvable.'], 404);
        }
        $property->update($request->all());
        return response()->json(['success' => true, 'data' => $property, 'message' => 'Bien mis à jour.'], 200);
    }

    // [DELETE] /api/v1/properties/{id} - Supprimer un bien
    public function destroy($id)
    {
        $property = Property::find($id);
        if (!$property) {
            return response()->json(['success' => false, 'message' => 'Bien introuvable.'], 404);
        }
        $property->delete();
        return response()->json(['success' => true, 'data' => null, 'message' => 'Bien supprimé.'], 200);
    }
}