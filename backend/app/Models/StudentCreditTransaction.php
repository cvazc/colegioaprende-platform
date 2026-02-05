<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentCreditTransaction extends Model
{
    protected $table = 'student_credit_transactions';

    protected $fillable = [
        'student_id',
        'payment_id',
        'delta',
        'reason',
        'meta',
    ];

    protected $casts = [
        'delta' => 'integer',
        'meta' => 'array',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }
}
