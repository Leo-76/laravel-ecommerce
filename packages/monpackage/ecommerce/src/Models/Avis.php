<?php

namespace MonPackage\Ecommerce\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Avis extends Model
{
    protected $table   = 'eco_avis';
    protected $guarded = ['id'];
    protected $casts   = ['approuve' => 'boolean', 'achat_verifie' => 'boolean'];

    public function produit(): BelongsTo
    {
        return $this->belongsTo(config('ecommerce.models.produit'), 'produit_id');
    }

    public function auteur(): BelongsTo
    {
        return $this->belongsTo(config('ecommerce.models.user'), 'user_id');
    }

    public function scopeApprouve($query) { return $query->where('approuve', true); }
}
