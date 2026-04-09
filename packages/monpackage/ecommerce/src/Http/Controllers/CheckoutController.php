<?php

namespace MonPackage\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use MonPackage\Ecommerce\Services\PanierService;
use MonPackage\Ecommerce\Services\PaiementService;
use MonPackage\Ecommerce\Models\Commande;
use MonPackage\Ecommerce\Exceptions\PanierException;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly PanierService  $panier,
        private readonly PaiementService $paiement
    ) {}

    public function livraison()
    {
        if ($this->panier->estVide()) {
            return redirect()->route('ecommerce.panier.index')
                ->with('erreur', 'Votre panier est vide.');
        }

        $resume = $this->panier->resume();
        $adresse = session('eco_adresse_livraison', []);

        return view('ecommerce::checkout.livraison', compact('resume', 'adresse'));
    }

    public function saveLivraison(Request $request)
    {
        $validated = $request->validate([
            'prenom'       => 'required|string|max:100',
            'nom'          => 'required|string|max:100',
            'email'        => 'required|email|max:255',
            'telephone'    => 'nullable|string|max:20',
            'adresse'      => 'required|string|max:255',
            'complement'   => 'nullable|string|max:255',
            'code_postal'  => 'required|string|max:10',
            'ville'        => 'required|string|max:100',
            'pays'         => 'required|string|size:2',
            'meme_facturation' => 'boolean',
        ]);

        session(['eco_adresse_livraison' => $validated]);

        if ($request->boolean('meme_facturation')) {
            session(['eco_adresse_facturation' => $validated]);
        }

        return redirect()->route('ecommerce.commande.paiement');
    }

    public function paiement()
    {
        if (! session('eco_adresse_livraison')) {
            return redirect()->route('ecommerce.commande.livraison');
        }

        $resume    = $this->panier->resume();
        $passerelle = config('ecommerce.paiement.passerelle', 'stripe');
        $clePublique = config("ecommerce.paiement.{$passerelle}.cle_publique");

        return view('ecommerce::checkout.paiement', compact('resume', 'passerelle', 'clePublique'));
    }

    public function processerPaiement(Request $request)
    {
        if ($this->panier->estVide()) {
            return redirect()->route('ecommerce.panier.index');
        }

        $adresseLiv = session('eco_adresse_livraison');
        $adresseFac = session('eco_adresse_facturation', $adresseLiv);

        $resume = $this->panier->resume();

        // Créer la commande
        $commande = Commande::create([
            'user_id'             => auth()->id(),
            'statut'              => Commande::STATUT_EN_ATTENTE,
            'sous_total'          => $resume['sous_total'],
            'remise'              => $resume['remise'],
            'livraison'           => $resume['livraison'],
            'tva'                 => $resume['tva'],
            'total'               => $resume['total'],
            'adresse_livraison'   => $adresseLiv,
            'adresse_facturation' => $adresseFac,
            'methode_paiement'    => config('ecommerce.paiement.passerelle'),
            'coupon_code'         => $resume['coupon']['code'] ?? null,
            'ip_client'           => $request->ip(),
        ]);

        // Créer les items
        foreach ($resume['items'] as $item) {
            $commande->items()->create([
                'produit_id'   => $item['produit_id'],
                'variation_id' => $item['variation_id'],
                'nom_produit'  => $item['nom'],
                'sku_produit'  => $item['sku'],
                'quantite'     => $item['quantite'],
                'prix_unitaire' => $item['prix'],
                'total'        => $item['prix'] * $item['quantite'],
                'options'      => $item['options'],
            ]);
        }

        // Processer le paiement
        try {
            $resultat = $this->paiement->processer($commande, $request->all());

            if ($resultat['succes']) {
                $commande->marquerPaye($resultat['transaction_id']);
                $this->panier->vider();
                session()->forget(['eco_adresse_livraison', 'eco_adresse_facturation']);

                return redirect()->route('ecommerce.commande.confirmation', $commande->reference)
                    ->with('succes', 'Votre commande a été passée avec succès !');
            }

            $commande->update(['statut' => Commande::STATUT_ANNULEE]);
            return back()->with('erreur', $resultat['message'] ?? 'Échec du paiement.');

        } catch (\Exception $e) {
            $commande->update(['statut' => Commande::STATUT_ANNULEE]);
            return back()->with('erreur', 'Une erreur est survenue lors du paiement.');
        }
    }

    public function confirmation(string $reference)
    {
        $commande = Commande::with(['items.produit', 'items.variation'])
            ->where('reference', $reference)
            ->firstOrFail();

        // Sécurité : seul le propriétaire ou un admin peut voir la confirmation
        if (auth()->check() && auth()->id() !== $commande->user_id && ! auth()->user()->hasRole('ecommerce-admin')) {
            abort(403);
        }

        return view('ecommerce::checkout.confirmation', compact('commande'));
    }

    public function webhook(Request $request, string $passerelle)
    {
        return $this->paiement->handleWebhook($passerelle, $request);
    }
}
