<?php

namespace MonPackage\Ecommerce\Services;

use Illuminate\Session\SessionManager;
use MonPackage\Ecommerce\Models\Produit;
use MonPackage\Ecommerce\Models\Variation;
use MonPackage\Ecommerce\Models\Coupon;
use MonPackage\Ecommerce\Exceptions\PanierException;

class PanierService
{
    private string $cle;

    public function __construct(
        private readonly SessionManager $session,
        private readonly mixed $config
    ) {
        $this->cle = $config->get('ecommerce.panier.prefix_cle', 'ecommerce_panier_')
            . ($session->getId() ?? 'guest');
    }

    // ── Contenu du panier ─────────────────────────────────────────────────────

    public function items(): array
    {
        return $this->session->get($this->cle, []);
    }

    public function count(): int
    {
        return array_sum(array_column($this->items(), 'quantite'));
    }

    public function estVide(): bool
    {
        return empty($this->items());
    }

    // ── Ajouter un produit ────────────────────────────────────────────────────

    public function ajouter(int $produitId, int $quantite = 1, ?int $variationId = null, array $options = []): array
    {
        $produit = Produit::actif()->findOrFail($produitId);

        if (! $produit->estEnStock()) {
            throw new PanierException("Le produit « {$produit->nom} » est en rupture de stock.");
        }

        if ($produit->stock < $quantite) {
            throw new PanierException("Stock insuffisant. Seulement {$produit->stock} disponible(s).");
        }

        $cle = $this->cléItem($produitId, $variationId);
        $items = $this->items();

        if (isset($items[$cle])) {
            $nouvelleQte = $items[$cle]['quantite'] + $quantite;
            if ($produit->stock < $nouvelleQte) {
                throw new PanierException("Stock insuffisant pour cette quantité.");
            }
            $items[$cle]['quantite'] = $nouvelleQte;
        } else {
            $prix = $produit->prix_effectif;
            if ($variationId) {
                $variation = Variation::findOrFail($variationId);
                $prix = $variation->prix ?? $prix;
            }

            $items[$cle] = [
                'produit_id'   => $produitId,
                'variation_id' => $variationId,
                'nom'          => $produit->nom,
                'slug'         => $produit->slug,
                'sku'          => $produit->sku,
                'quantite'     => $quantite,
                'prix'         => $prix,
                'image'        => $produit->image_principale_url,
                'options'      => $options,
                'ajoute_at'    => now()->toIso8601String(),
            ];
        }

        $this->session->put($this->cle, $items);

        return $this->resume();
    }

    // ── Modifier la quantité ──────────────────────────────────────────────────

    public function modifierQuantite(string $cle, int $quantite): array
    {
        $items = $this->items();

        if (! isset($items[$cle])) {
            throw new PanierException("Article introuvable dans le panier.");
        }

        if ($quantite <= 0) {
            return $this->supprimer($cle);
        }

        $produit = Produit::find($items[$cle]['produit_id']);
        if ($produit && $produit->stock < $quantite) {
            throw new PanierException("Stock insuffisant. Seulement {$produit->stock} disponible(s).");
        }

        $items[$cle]['quantite'] = $quantite;
        $this->session->put($this->cle, $items);

        return $this->resume();
    }

    // ── Supprimer un article ──────────────────────────────────────────────────

    public function supprimer(string $cle): array
    {
        $items = $this->items();
        unset($items[$cle]);
        $this->session->put($this->cle, $items);

        return $this->resume();
    }

    // ── Vider le panier ───────────────────────────────────────────────────────

    public function vider(): void
    {
        $this->session->forget($this->cle);
        $this->session->forget($this->cle . '_coupon');
    }

    // ── Coupons ───────────────────────────────────────────────────────────────

    public function appliquerCoupon(string $code): array
    {
        $coupon = Coupon::actif()->where('code', strtoupper($code))->first();

        if (! $coupon) {
            throw new PanierException("Le coupon « {$code} » est invalide ou expiré.");
        }

        if ($coupon->minimum_commande && $this->sousTotal() < $coupon->minimum_commande) {
            $min = number_format($coupon->minimum_commande / 100, 2, ',', ' ');
            throw new PanierException("Ce coupon nécessite un minimum d'achat de {$min} €.");
        }

        $this->session->put($this->cle . '_coupon', $coupon->toArray());

        return $this->resume();
    }

    public function retirerCoupon(): array
    {
        $this->session->forget($this->cle . '_coupon');
        return $this->resume();
    }

    public function couponActif(): ?array
    {
        return $this->session->get($this->cle . '_coupon');
    }

    // ── Calculs ───────────────────────────────────────────────────────────────

    public function sousTotal(): int
    {
        return array_reduce($this->items(), function (int $total, array $item) {
            return $total + ($item['prix'] * $item['quantite']);
        }, 0);
    }

    public function remise(): int
    {
        $coupon = $this->couponActif();
        if (! $coupon) return 0;

        return match ($coupon['type']) {
            'pourcentage' => (int) round($this->sousTotal() * ($coupon['valeur'] / 100)),
            'fixe'        => min($coupon['valeur'], $this->sousTotal()),
            'livraison'   => 0,
            default       => 0,
        };
    }

    public function fraisLivraison(): int
    {
        $coupon = $this->couponActif();
        if ($coupon && $coupon['type'] === 'livraison') return 0;

        $seuil = config('ecommerce.livraison.gratuite_a_partir_de');
        if ($seuil && ($this->sousTotal() - $this->remise()) >= $seuil) return 0;

        return config('ecommerce.livraison.forfait_defaut', 490);
    }

    public function tva(): int
    {
        if (! config('ecommerce.tva.incluse_prix')) {
            $taux = config('ecommerce.tva.taux_defaut', 20) / 100;
            return (int) round(($this->sousTotal() - $this->remise()) * $taux);
        }
        return 0;
    }

    public function total(): int
    {
        return $this->sousTotal() - $this->remise() + $this->fraisLivraison() + $this->tva();
    }

    public function resume(): array
    {
        return [
            'items'          => $this->items(),
            'nombre_articles' => $this->count(),
            'sous_total'     => $this->sousTotal(),
            'remise'         => $this->remise(),
            'livraison'      => $this->fraisLivraison(),
            'tva'            => $this->tva(),
            'total'          => $this->total(),
            'coupon'         => $this->couponActif(),
            'devise'         => config('ecommerce.boutique.symbole', '€'),
        ];
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function cléItem(int $produitId, ?int $variationId): string
    {
        return "p{$produitId}" . ($variationId ? "_v{$variationId}" : '');
    }
}
