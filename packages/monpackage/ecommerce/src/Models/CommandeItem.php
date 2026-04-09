<?php

namespace MonPackage\Ecommerce\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommandeItem extends Model
{
    protected $table   = 'eco_commande_items';
    protected $guarded = ['id'];
    protected $casts   = ['options' => 'array'];

    public function commande(): BelongsTo
    {
        return $this->belongsTo(Commande::class, 'commande_id');
    }

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

    public function variation(): BelongsTo
    {
        return $this->belongsTo(Variation::class, 'variation_id');
    }
}
