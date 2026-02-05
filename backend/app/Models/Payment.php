<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'prospect_id',
        'provider',
        'status',
        'amount',
        'currency',
        'provider_payment_id',
        'provider_payload',
    ];

    protected $casts = [
        'provider_payload' => 'array',
        'amount' => 'decimal:2',
    ];

    public function prospect(): BelongsTo
    {
        return $this->belongsTo(Prospect::class, 'prospect_id');
    }
}
