<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\VisitRequest;
use Illuminate\Http\Request;
use App\Models\Favorite;

class PropertyController extends Controller
{
    // Affichage du catalogue avec filtres appliqués (EF-B1, EF-E1)
    public function index(Request $request)
    {
        $properties = Property::where('status', 'publiée')
            ->filter($request->only(['type', 'usage', 'option', 'zone']))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('properties.index', compact('properties'));
    }
    

    // Affichage du formulaire de dépôt
// create() — vérifier d'abord si connecté
public function create()
{
    if (!auth()->check()) {
        return redirect()->route('auth.page')->with('error', 'Connectez-vous d\'abord.');
    }
    if (!in_array(auth()->user()->role, ['bailleur', 'agent'])) {
        abort(403, 'Action non autorisée.');
    }
    return view('properties.create');
}

    // Traitement sécurisé du dépôt (EF-C1, ENF-6, ENF-11)
    public function store(Request $request)
    {

        $validated = $request->validate([
            'type' => 'required|string',
            'property_usage' => 'required|string',
            'contract_option' => 'required|string',
            'zone' => 'required|string|max:100',
            'size' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:1',
            'description' => 'nullable|string',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Stockage physique de l'image (ENF-11)
        $path = $request->file('photo')->store('uploads', 'public');

        $status = (auth()->user()->role === 'agent') ? 'publiée' : 'en attente';

        Property::create([
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'property_usage' => $validated['property_usage'],
            'contract_option' => $validated['contract_option'],
            'zone' => $validated['zone'],
            'size' => $validated['size'],
            'price' => $validated['price'],
            'description' => $validated['description'],
            'photo_path' => '/storage/' . $path,
            'status' => $status // Utilisation de la variable $status
        ]);

        $message = (auth()->user()->role === 'agent') 
    ? 'Annonce publiée avec succès !' 
    : 'Votre annonce a été soumise et est en attente de validation par un agent.';

return redirect()->route('bailleur.index')->with('success', $message);
    }
    public function mesAnnonces() {
    // Sécurité : Seul un bailleur connecté peut y accéder
    if (!auth()->check() || !in_array(auth()->user()->role, ['bailleur', 'agent'])) {
        abort(403, 'Accès réservé aux bailleurs et aux agents.');
    }

    // Récupérer uniquement les propriétés créées par ce bailleur (si tu as une colonne user_id)
    // IMPORTANT : Si tu n'as pas encore ajouté user_id dans ta table properties, dis-le moi !
    $properties = Property::where('user_id', auth()->id())
                          ->orderBy('created_at', 'desc')
                          ->get();

    return view('bailleur.index', compact('properties'));
}
    public function show($id)
{
    $property = Property::findOrFail($id);
    return view('properties.show', compact('property'));
}
public function showAuthPage() {
        return view('auth');}
public function visit(Request $request, $id) {
    // Sécurité : Seul un client doit pouvoir faire une demande
    if (!auth()->check() || auth()->user()->role !== 'client') {
        return back()->with('error', 'Seuls les clients connectés peuvent effectuer une demande de visite.');
    }

    $request->validate([
        'visit_date' => 'required|date|after:today',
        'message' => 'nullable|string|max:500',
    ]);

    VisitRequest::create([
        'user_id' => auth()->id(),
        'property_id' => $id,
        'visit_date' => $request->visit_date,
        'message' => $request->message,
        'status' => 'en attente',
    ]);

    return back()->with('success', 'Votre demande de visite a bien été transmise à l\'agent responsable !');
}
public function mesVisites() {
    // Sécurité : Seul un client connecté peut y accéder
    if (!auth()->check() || auth()->user()->role !== 'client') {
        return redirect()->route('auth.page')->with('error', 'Veuillez vous connecter en tant que client.');
    }

    // Récupérer les demandes du client avec les infos de la propriété liée
    $visites = VisitRequest::with('property')
                ->where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->get();

    return view('client.visites', compact('visites'));}
    public function retirer($id)
{
    $property = Property::findOrFail($id);

    // Sécurité essentielle : Vérifier que c'est bien le propriétaire qui supprime l'annonce
    if ($property->user_id !== auth()->id()) {
        abort(403, 'Action non autorisée.');
    }

    $property->delete();

    return back()->with('success', 'Votre annonce a été supprimée avec succès !');
}
public function edit($id)
{
    $property = Property::findOrFail($id);

    // Sécurité : Vérifier que c'est bien le bailleur propriétaire qui tente de modifier
    if ($property->user_id !== auth()->id()) {
        abort(403, 'Action non autorisée.');
    }

    return view('properties.edit', compact('property'));
}

public function update(Request $request, $id)
{
    $property = Property::findOrFail($id);

    if ($property->user_id !== auth()->id()) {
        abort(403, 'Action non autorisée.');
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

    // Gestion de la nouvelle photo si elle est téléversée
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
        // L'annonce repasse "en attente" après modification selon les règles de validation
        'status' => 'en attente', 
    ]);

    return redirect()->route('bailleur.index')->with('success', 'Votre annonce a été modifiée avec succès et est en attente de validation !');
}
public function agentDashboard() {
    if (auth()->user()->role !== 'agent') { abort(403); }
    
    $pendingProperties = Property::where('status', 'en attente')->orderBy('created_at', 'desc')->get();
    
    $pendingVisits = VisitRequest::with(['user', 'property'])
                            ->where('status', 'en attente')
                            ->orderBy('created_at', 'desc')
                            ->get();

    // AJOUT : clients affectés à cet agent
    $clients = \App\Models\User::where('agent_id', auth()->id())
                               ->where('role', 'client')
                               ->orderBy('name')
                               ->get();

    return view('agent.dashboard', compact('pendingProperties', 'pendingVisits', 'clients'));
}

    public function validate($id) {
        if (auth()->user()->role !== 'agent') { abort(403); }
        
        $property = Property::findOrFail($id);
        $property->update(['status' => 'publiée']); // Elle devient publique !
        
        return back()->with('success', 'L\'annonce a été validée et est maintenant publique.');
    }
    public function retirerannonce($id) {
        if (auth()->user()->role !== 'agent') { abort(403); }
        
        $property = Property::findOrFail($id);
        $property->update(['status' => 'retirée']);
        
        return back()->with('success', 'L\'annonce a été refusée.');
    }
    public function validateVisit($id)
{
    if (auth()->user()->role !== 'agent') { abort(403); }
    
    $visit = VisitRequest::findOrFail($id);
    $visit->update(['status' => 'validée']);
    
    return back()->with('success', 'Visite validée avec succès.');
}

public function rejectVisit($id)
{
    if (auth()->user()->role !== 'agent') { abort(403); }
    
    $visit = VisitRequest::findOrFail($id);
    $visit->update(['status' => 'refusée']);
    
    return back()->with('success', 'Visite refusée.');
}

public function toggleFavorite($id)
{
    if (!auth()->check() || auth()->user()->role !== 'client') {
        return back()->with('error', 'Seuls les clients peuvent ajouter des favoris.');
    }

    $existing = Favorite::where('user_id', auth()->id())
                        ->where('property_id', $id)
                        ->first();

    if ($existing) {
        $existing->delete();
        return back()->with('success', 'Retiré de vos favoris.');
    } else {
        Favorite::create([
            'user_id' => auth()->id(),
            'property_id' => $id,
        ]);
        return back()->with('success', 'Ajouté à vos favoris !');
    }
}

public function mesFavoris()
{
    if (!auth()->check() || auth()->user()->role !== 'client') {
        return redirect()->route('auth.page');
    }

    $favoris = Favorite::with('property')
                ->where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->get();

    return view('client.favoris', compact('favoris'));
}
}