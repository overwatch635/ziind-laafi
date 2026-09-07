<?php

namespace App\Http\Controllers\Api;

// NOUVEAU FICHIER — logique reprise depuis les méthodes
// toggleFavorite() et mesFavoris() du PropertyController web.

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    // POST /api/favorites/{id}
    public function toggle(Request $request, $id)
    {
        if ($request->user()->role !== 'client') {
            return response()->json([
                'success' => false,
                'error' => 'Seuls les clients peuvent ajouter des favoris.',
            ], 403);
        }

        $existing = Favorite::where('user_id', $request->user()->id)
            ->where('property_id', $id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'success' => true,
                'message' => 'Retiré des favoris.',
                'favorited' => false,
            ]);
        }

        Favorite::create([
            'user_id' => $request->user()->id,
            'property_id' => $id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ajouté aux favoris.',
            'favorited' => true,
        ], 201);
    }

    // GET /api/mes-favoris
    public function mesFavoris(Request $request)
    {
        if ($request->user()->role !== 'client') {
            return response()->json([
                'success' => false,
                'error' => 'Action non autorisée.',
            ], 403);
        }

        $favoris = Favorite::with('property')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $favoris,
        ]);
    }
}
