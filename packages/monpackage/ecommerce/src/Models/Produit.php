<?php

namespace MonPackage\Ecommerce\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use MonPackage\Ecommerce\Traits\ASlug;
use MonPackage\Ecommerce\Traits\APrix;

class Produit extends Model
{
    use SoftDeletes, HasFactory, ASlug, APrix;

    protected $table    = 'eco_produits';
    protected $guarded  = ['id'];

    protected $casts = [
        'actif'       => 'boolean',
        'en_vedette'  => 'boolean',
        'numerique'   => 'boolean',
        'prix'        => 'integer',
        'prix_promo'  => 'integer',
        'promo_debut' => 'date',
        'promo_fin'   => 'date',
    ];

    // ── Relations ──────────────────────────────────────────────────────────────

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            config('ecommerce.models.categorie'),
            'eco_produit_categorie',
            'produit_id',
            'categorie_id'
        );
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProduitImage::class, 'produit_id')->orderBy('ordre');
    }

    public function variations(): HasMany
    {
        return $this->hasMany(Variation::class, 'produit_id');
    }

    public function avis(): HasMany
    {
        return $this->hasMany(Avis::class, 'produit_id')->where('approuve', true);
    }

    // ── Accesseurs ────────────────────────────────────────────────────────────

    public function getPrixEffectifAttribute(): int
    {
        if ($this->prix_promo && $this->estEnPromo()) {
            return $this->prix_promo;
        }
        return $this->prix;
    }

    public function getPrixFormateAttribute(): string
    {
        return $this->formaterPrix($this->prix_effectif);
    }

    public function getPrixOriginalFormateAttribute(): string
    {
        return $this->formaterPrix($this->prix);
    }

    public function getPourcentagePromoAttribute(): ?int
    {
        if (! $this->estEnPromo()) {
            return null;
        }
        return (int) round((($this->prix - $this->prix_promo) / $this->prix) * 100);
    }

    public function getNoteMovenneAttribute(): float
    {
        return round($this->avis()->avg('note') ?? 0, 1);
    }

    public function getImagePrincipaleUrlAttribute(): string
    {
        if ($this->image_principale) {
            return \Storage::disk(config('ecommerce.images.disque', 'public'))
                ->url($this->image_principale);
        }
        return asset('vendor/ecommerce/images/placeholder.svg');
    }

    // ── Méthodes ─────────────────────────────────────────────────────────────

    public function estEnPromo(): bool
    {
        if (! $this->prix_promo) return false;
        $now = now();
        if ($this->promo_debut && $now->lt($this->promo_debut)) return false;
        if ($this->promo_fin   && $now->gt($this->promo_fin))   return false;
        return true;
    }

    public function estEnStock(): bool
    {
        if (! config('ecommerce.stock.activer_gestion')) return true;
        return $this->stock > 0;
    }

    public function aStockFaible(): bool
    {
        return $this->stock <= config('ecommerce.stock.seuil_alerte', 5);
    }

    public function decrementerStock(int $quantite = 1): void
    {
        $this->decrement('stock', $quantite);
        $this->increment('ventes_total', $quantite);

        if ($this->aStockFaible()) {
            event(new \MonPackage\Ecommerce\Events\StockFaible($this));
        }
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    public function scopeEnVedette($query)
    {
        return $query->where('en_vedette', true);
    }

    public function scopeEnStock($query)
    {
        if (config('ecommerce.stock.activer_gestion')) {
            return $query->where('stock', '>', 0);
        }
        return $query;
    }

    public function scopeEnPromo($query)
    {
        return $query->whereNotNull('prix_promo')
            ->where(function ($q) {
                $q->whereNull('promo_debut')->orWhere('promo_debut', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('promo_fin')->orWhere('promo_fin', '>=', now());
            });
    }

    public function scopeRecherche($query, string $terme)
    {
        return $query->where(function ($q) use ($terme) {
            $q->where('nom', 'LIKE', "%{$terme}%")
              ->orWhere('description_courte', 'LIKE', "%{$terme}%")
              ->orWhere('sku', 'LIKE', "%{$terme}%");
        });
    }
}
