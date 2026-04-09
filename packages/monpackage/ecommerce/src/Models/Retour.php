<?php

namespace MonPackage\Ecommerce\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Retour extends Model
{
    protected $table   = 'eco_retours';
    protected $guarded = ['id'];
    protected $casts   = ['rembourse_at' => 'datetime'];

    public function commande(): BelongsTo
    {
        return $this->belongsTo(Commande::class, 'commande_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(config('ecommerce.models.user'), 'user_id');
    }

    public function getStatutLibelleAttribute(): string
    {
        return match ($this->statut) {
            'demande'    => 'Demande reçue',
            'approuve'   => 'Approuvé',
            'refuse'     => 'Refusé',
            'rembourse'  => 'Remboursé',
            default      => ucfirst($this->statut),
        };
    }
}
