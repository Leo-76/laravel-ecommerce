<?php

namespace MonPackage\Ecommerce\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use MonPackage\Ecommerce\Traits\ASlug;

class Categorie extends Model
{
    use SoftDeletes, ASlug;

    protected $table   = 'eco_categories';
    protected $guarded = ['id'];
    protected $casts   = ['actif' => 'boolean'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'parent_id');
    }

    public function enfants(): HasMany
    {
        return $this->hasMany(Categorie::class, 'parent_id')->orderBy('ordre');
    }

    public function produits(): BelongsToMany
    {
        return $this->belongsToMany(
            config('ecommerce.models.produit'),
            'eco_produit_categorie',
            'categorie_id',
            'produit_id'
        );
    }

    public function scopeActif($query) { return $query->where('actif', true); }
    public function scopeRacine($query) { return $query->whereNull('parent_id'); }
}
