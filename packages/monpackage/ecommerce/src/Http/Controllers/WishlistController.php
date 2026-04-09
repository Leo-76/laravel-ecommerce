<?php

namespace MonPackage\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use MonPackage\Ecommerce\Models\Produit;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    public function index()
    {
        $produits = auth()->user()
            ->wishlist()
            ->with('images', 'categories')
            ->paginate(12);

        return view('ecommerce::shop.wishlist', compact('produits'));
    }

    public function toggle(int $produitId)
    {
        $produit = Produit::findOrFail($produitId);
        $user    = auth()->user();

        $existe = DB::table('eco_wishlist')
            ->where('user_id', $user->id)
            ->where('produit_id', $produitId)
            ->exists();

        if ($existe) {
            DB::table('eco_wishlist')
                ->where('user_id', $user->id)
                ->where('produit_id', $produitId)
                ->delete();
            $message = 'Retiré de votre wishlist.';
            $dans = false;
        } else {
            DB::table('eco_wishlist')->insert([
                'user_id'    => $user->id,
                'produit_id' => $produitId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $message = '❤️ Ajouté à votre wishlist !';
            $dans = true;
        }

        if (request()->expectsJson()) {
            return response()->json(['dans_wishlist' => $dans, 'message' => $message]);
        }

        return back()->with('succes', $message);
    }
}
