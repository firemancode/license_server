<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activation extends Model
{
    protected $fillable = [
        'license_id',
        'domain',
        'ip_address',
        'activated_at',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
    ];

    /**
     * Get the license that owns the activation.
     */
    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }
}
