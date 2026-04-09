<?php

namespace MonPackage\Ecommerce\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Panier extends Model
{
    protected $table   = 'eco_paniers';
    protected $guarded = ['id'];
    protected $casts   = ['expire_at' => 'datetime'];

    public function items(): HasMany
    {
        return $this->hasMany(PanierItem::class, 'panier_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('ecommerce.models.user'), 'user_id');
    }

    public function estExpire(): bool
    {
        return $this->expire_at && $this->expire_at->isPast();
    }
}
