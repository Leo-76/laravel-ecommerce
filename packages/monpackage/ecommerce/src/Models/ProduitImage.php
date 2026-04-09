<?php

namespace MonPackage\Ecommerce\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProduitImage extends Model
{
    protected $table   = 'eco_produit_images';
    protected $guarded = ['id'];

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk(config('ecommerce.images.disque', 'public'))->url($this->chemin);
    }
}
