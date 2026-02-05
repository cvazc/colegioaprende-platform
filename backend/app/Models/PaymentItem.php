<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentItem extends Model
{
    protected $table = 'payment_items';

    protected $fillable = [
        'code',
        'name',
        'course_type',
        'enrollment_type',
        'amount',
        'currency',
        'credit_qty',
        'auto_register',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'credit_qty' => 'integer',
        'auto_register' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
