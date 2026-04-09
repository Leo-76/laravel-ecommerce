<?php

namespace MonPackage\Ecommerce\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PanierItem extends Model
{
    protected $table   = 'eco_panier_items';
    protected $guarded = ['id'];
    protected $casts   = ['options' => 'array'];

    public function panier(): BelongsTo
    {
        return $this->belongsTo(Panier::class, 'panier_id');
    }

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

    public function variation(): BelongsTo
    {
        return $this->belongsTo(Variation::class, 'variation_id');
    }

    public function getTotal(): int
    {
        return $this->prix_unitaire * $this->quantite;
    }
}
