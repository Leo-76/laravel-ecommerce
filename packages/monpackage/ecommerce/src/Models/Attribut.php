<?php

namespace MonPackage\Ecommerce\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribut extends Model
{
    protected $table   = 'eco_attributs';
    protected $guarded = ['id'];
    protected $casts   = ['filtrable' => 'boolean'];

    public function valeurs(): HasMany
    {
        return $this->hasMany(AttributValeur::class, 'attribut_id')->orderBy('ordre');
    }
}
