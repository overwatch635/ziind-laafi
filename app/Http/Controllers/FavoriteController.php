<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    // [GET] /api/v1/favorites - Liste des favoris de l'utilisateur
    public function index()
    {
        $favorites = Favorite::where('user_id', auth()->id())->get();
        return response()->json([
            'success' => true,
            'data' => $favorites,
            'message' => 'Vos favoris ont été récupérés.'
        ], 200);
    }

    // [POST] /api/v1/favorites - Ajouter un bien aux favoris
    public function store(Request $request)
    {
        $request->validate(['property_id' => 'required|exists:properties,id']);

        $favorite = Favorite::create([
            'user_id' => auth()->id(),
            'property_id' => $request->property_id
        ]);

        return response()->json([
            'success' => true,
            'data' => $favorite,
            'message' => 'Bien ajouté aux favoris.'
        ], 201);
    }

    // [DELETE] /api/v1/favorites/{id} - Retirer un favori
    public function destroy($id)
    {
        $favorite = Favorite::where('user_id', auth()->id())->find($id);
        if (!$favorite) {
            return response()->json(['success' => false, 'message' => 'Favori introuvable.'], 404);
        }
        $favorite->delete();
        return response()->json(['success' => true, 'data' => null, 'message' => 'Retiré des favoris.'], 200);
    }
}