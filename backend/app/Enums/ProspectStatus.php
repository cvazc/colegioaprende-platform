<?php

namespace App\Enums;

enum ProspectStatus: string
{
    case PendingPayment = 'pending_payment';
    case PaymentConfirmed = 'payment_confirmed';
    case RegisteredStudent = 'registered_student';
    case Canceled = 'canceled';
}
