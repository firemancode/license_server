<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class License extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'product_id',
        'license_key',
        'status',
        'expires_at',
        'max_activations',
        'notes',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user that owns the license.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product that this license belongs to.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get all activations for this license.
     */
    public function activations(): HasMany
    {
        return $this->hasMany(Activation::class);
    }

    /**
     * Check if license is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && now()->isAfter($this->expires_at);
    }

    /**
     * Check if license can be activated (not expired and active status).
     */
    public function canBeActivated(): bool
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    /**
     * Get the maximum number of activations allowed for this license.
     */
    public function getMaxActivations(): int
    {
        // This could be configurable per product or license type
        return 1;
    }

    /**
     * Check if license has reached activation limit.
     */
    public function hasReachedActivationLimit(): bool
    {
        return $this->activations()->count() >= $this->getMaxActivations();
    }

    /**
     * Auto-disable license if expired.
     */
    public function autoDisableIfExpired(): bool
    {
        if ($this->isExpired() && $this->status === 'active') {
            $this->update(['status' => 'expired']);
            return true;
        }
        return false;
    }
}
