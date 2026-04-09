<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use MonPackage\Ecommerce\Models\Commande;
use MonPackage\Ecommerce\Models\Produit;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'users'             => User::count(),
            'admins'            => User::where('role', 'admin')->orWhere('ecommerce_admin', true)->count(),
            'commandes_total'   => Commande::count(),
            'commandes_attente' => Commande::where('statut', 'en_attente')->count(),
            'produits'          => Produit::actif()->count(),
            'ca_total'          => Commande::where('statut_paiement', 'paye')->sum('total'),
        ];
        $dernieresCommandes = Commande::latest()->limit(10)->get();
        return view('admin.index', compact('stats', 'dernieresCommandes'));
    }

    public function utilisateurs()
    {
        $users = User::withCount('commandes')->latest()->paginate(20);
        return view('admin.utilisateurs', compact('users'));
    }

    public function changerRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:user,admin,super-admin']);
        $user->update(['role' => $request->role]);
        return back()->with('succes', "Rôle de {$user->name} mis à jour.");
    }

    public function toggleEcommerce(User $user)
    {
        $user->update(['ecommerce_admin' => !$user->ecommerce_admin]);
        $s = $user->ecommerce_admin ? 'activé' : 'retiré';
        return back()->with('succes', "Accès admin boutique {$s} pour {$user->name}.");
    }
}
