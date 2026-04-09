<?php

namespace MonPackage\Ecommerce\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommandeHistorique extends Model
{
    protected $table   = 'eco_commande_historique';
    protected $guarded = ['id'];

    public function commande(): BelongsTo
    {
        return $this->belongsTo(Commande::class, 'commande_id');
    }

    public function auteur(): BelongsTo
    {
        return $this->belongsTo(config('ecommerce.models.user'), 'user_id');
    }
}
