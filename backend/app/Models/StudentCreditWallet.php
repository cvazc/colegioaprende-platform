<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentCreditWallet extends Model
{
    protected $table = 'student_credit_wallets';

    protected $fillable = [
        'student_id',
        'balance',
    ];

    protected $casts = [
        'balance' => 'integer',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
