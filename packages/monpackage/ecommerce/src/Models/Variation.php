<?php

namespace MonPackage\Ecommerce\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Variation extends Model
{
    protected $table   = 'eco_variations';
    protected $guarded = ['id'];

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

    public function valeurs(): BelongsToMany
    {
        return $this->belongsToMany(
            AttributValeur::class,
            'eco_variation_attributs',
            'variation_id',
            'valeur_id'
        )->with('attribut');
    }

    public function estEnStock(): bool
    {
        return $this->stock > 0;
    }
}
