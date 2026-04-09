<?php

namespace MonPackage\Ecommerce\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttributValeur extends Model
{
    protected $table   = 'eco_attribut_valeurs';
    protected $guarded = ['id'];

    public function attribut(): BelongsTo
    {
        return $this->belongsTo(Attribut::class, 'attribut_id');
    }
}
