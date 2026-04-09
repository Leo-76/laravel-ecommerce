<?php

namespace MonPackage\Ecommerce\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use MonPackage\Ecommerce\Models\Produit;
use MonPackage\Ecommerce\Models\Categorie;
use MonPackage\Ecommerce\Models\ProduitImage;
use Illuminate\Support\Facades\Storage;

class ProduitAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Produit::with(['categories'])->latest();

        if ($request->filled('q'))         { $query->recherche($request->q); }
        if ($request->filled('categorie')) { $query->whereHas('categories', fn($q) => $q->where('slug', $request->categorie)); }
        if ($request->filled('statut')) {
            match ($request->statut) {
                'actif'    => $query->where('actif', true),
                'inactif'  => $query->where('actif', false),
                'rupture'  => $query->where('stock', 0),
                'promo'    => $query->enPromo(),
                default    => null,
            };
        }

        $produits   = $query->paginate(20)->withQueryString();
        $categories = Categorie::actif()->orderBy('nom')->get();

        return view('ecommerce::admin.produits.index', compact('produits', 'categories'));
    }

    public function create()
    {
        $categories = Categorie::actif()->orderBy('nom')->get();
        return view('ecommerce::admin.produits.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $this->valider($request);

        $imagePrincipale = null;
        if ($request->hasFile('image_principale')) {
            $imagePrincipale = $this->uploadImage($request->file('image_principale'));
        }

        $produit = Produit::create(array_merge($validated, [
            'image_principale' => $imagePrincipale,
            'prix'             => (int)($validated['prix'] * 100),
            'prix_promo'       => isset($validated['prix_promo']) ? (int)($validated['prix_promo'] * 100) : null,
        ]));

        if ($request->filled('categories')) {
            $produit->categories()->sync($request->categories);
        }

        // Images supplémentaires
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $ordre => $fichier) {
                $chemin = $this->uploadImage($fichier);
                $produit->images()->create(['chemin' => $chemin, 'ordre' => $ordre]);
            }
        }

        return redirect()->route('ecommerce.admin.produits.index')
            ->with('succes', "Produit « {$produit->nom} » créé avec succès.");
    }

    public function edit(int $id)
    {
        $produit    = Produit::with(['categories', 'images', 'variations'])->findOrFail($id);
        $categories = Categorie::actif()->orderBy('nom')->get();
        return view('ecommerce::admin.produits.form', compact('produit', 'categories'));
    }

    public function update(Request $request, int $id)
    {
        $produit   = Produit::findOrFail($id);
        $validated = $this->valider($request, $id);

        if ($request->hasFile('image_principale')) {
            if ($produit->image_principale) {
                Storage::disk(config('ecommerce.images.disque'))->delete($produit->image_principale);
            }
            $validated['image_principale'] = $this->uploadImage($request->file('image_principale'));
        }

        $validated['prix']      = (int)($validated['prix'] * 100);
        $validated['prix_promo'] = isset($validated['prix_promo']) ? (int)($validated['prix_promo'] * 100) : null;

        $produit->update($validated);
        $produit->categories()->sync($request->categories ?? []);

        if ($request->hasFile('images')) {
            $maxOrdre = $produit->images()->max('ordre') ?? 0;
            foreach ($request->file('images') as $i => $fichier) {
                $chemin = $this->uploadImage($fichier);
                $produit->images()->create(['chemin' => $chemin, 'ordre' => $maxOrdre + $i + 1]);
            }
        }

        return redirect()->route('ecommerce.admin.produits.index')
            ->with('succes', "Produit « {$produit->nom} » mis à jour.");
    }

    public function destroy(int $id)
    {
        Produit::findOrFail($id)->delete();
        return back()->with('succes', 'Produit supprimé (soft delete).');
    }

    public function toggleActif(int $id)
    {
        $produit = Produit::findOrFail($id);
        $produit->update(['actif' => ! $produit->actif]);
        return response()->json(['actif' => $produit->actif]);
    }

    public function uploadImage(int $id, Request $request)
    {
        $request->validate(['image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120']);
        $produit = Produit::findOrFail($id);
        $chemin  = $this->uploadImage($request->file('image'));
        $image   = $produit->images()->create(['chemin' => $chemin, 'ordre' => $produit->images()->count()]);
        return response()->json(['id' => $image->id, 'url' => Storage::disk(config('ecommerce.images.disque'))->url($chemin)]);
    }

    public function supprimerImage(int $id, int $imageId)
    {
        $image = ProduitImage::where('produit_id', $id)->findOrFail($imageId);
        Storage::disk(config('ecommerce.images.disque'))->delete($image->chemin);
        $image->delete();
        return response()->json(['succes' => true]);
    }

    public function export()
    {
        $produits = Produit::with('categories')->get();
        $csv = "ID,Nom,SKU,Prix,Stock,Actif,Catégories\n";
        foreach ($produits as $p) {
            $cats = $p->categories->pluck('nom')->implode(' | ');
            $csv .= "\"{$p->id}\",\"{$p->nom}\",\"{$p->sku}\",\"" . ($p->prix / 100) . "\",\"{$p->stock}\",\"" . ($p->actif ? 'Oui' : 'Non') . "\",\"{$cats}\"\n";
        }
        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="produits-' . date('Y-m-d') . '.csv"',
        ]);
    }

    // ── Privé ─────────────────────────────────────────────────────────────────

    private function valider(Request $request, ?int $excludeId = null): array
    {
        return $request->validate([
            'nom'              => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255|unique:eco_produits,slug' . ($excludeId ? ",{$excludeId}" : ''),
            'description_courte' => 'nullable|string|max:500',
            'description'      => 'nullable|string',
            'prix'             => 'required|numeric|min:0',
            'prix_promo'       => 'nullable|numeric|min:0|lt:prix',
            'promo_debut'      => 'nullable|date',
            'promo_fin'        => 'nullable|date|after_or_equal:promo_debut',
            'stock'            => 'required|integer|min:0',
            'tva'              => 'integer|in:0,5,10,20',
            'actif'            => 'boolean',
            'en_vedette'       => 'boolean',
            'numerique'        => 'boolean',
            'poids'            => 'nullable|numeric|min:0',
            'sku'              => 'nullable|string|max:100',
            'meta_titre'       => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'image_principale' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'images.*'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'categories'       => 'nullable|array',
            'categories.*'     => 'exists:eco_categories,id',
        ]);
    }

    private function uploadImage(\Illuminate\Http\UploadedFile $fichier): string
    {
        $disque  = config('ecommerce.images.disque', 'public');
        $dossier = config('ecommerce.images.dossier', 'ecommerce/produits');
        return $fichier->store($dossier, $disque);
    }
}
