<?php

namespace MonPackage\Ecommerce\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use MonPackage\Ecommerce\Models\Produit;

class ProduitApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Produit::actif()->enStock()->with(['categories', 'images']);

        if ($request->filled('q'))         { $query->recherche($request->q); }
        if ($request->filled('categorie')) { $query->whereHas('categories', fn($q) => $q->where('slug', $request->categorie)); }
        if ($request->filled('prix_min'))  { $query->where('prix', '>=', (int)($request->prix_min * 100)); }
        if ($request->filled('prix_max'))  { $query->where('prix', '<=', (int)($request->prix_max * 100)); }
        if ($request->boolean('promo'))    { $query->enPromo(); }
        if ($request->boolean('vedette'))  { $query->enVedette(); }

        match ($request->get('tri', 'nouveautes')) {
            'prix_asc'   => $query->orderBy('prix'),
            'prix_desc'  => $query->orderByDesc('prix'),
            'popularite' => $query->orderByDesc('ventes_total'),
            default      => $query->latest(),
        };

        $produits = $query->paginate($request->get('par_page', 12));

        return response()->json([
            'data' => $produits->items(),
            'meta' => [
                'total'         => $produits->total(),
                'par_page'      => $produits->perPage(),
                'page_actuelle' => $produits->currentPage(),
                'derniere_page' => $produits->lastPage(),
            ],
        ]);
    }

    public function show(string $slug)
    {
        $produit = Produit::actif()
            ->with(['categories', 'images', 'variations.valeurs.attribut', 'avis'])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json($produit->append([
            'prix_formate',
            'prix_effectif',
            'image_principale_url',
            'note_moyenne',
        ]));
    }
}
