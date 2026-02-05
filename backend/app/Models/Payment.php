<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'prospect_id',
        'student_id',
        'provider',
        'status',
        'item_code',
        'amount',
        'currency',
        'provider_payment_id',
        'provider_reference_id',
        'provider_payload',
        'approved_at',
    ];

    protected $casts = [
        'provider_payload' => 'array',
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function prospect(): BelongsTo
    {
        return $this->belongsTo(Prospect::class, 'prospect_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
