<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('reminders:send-payment-due {--days=3}', function () {
    $days = (int) $this->option('days');
    $count = app(\App\Services\PaymentReminderService::class)->sendDueSoonReminders($days);

    $this->info("Payment reminders sent: {$count}");
})->purpose('Send payment reminder emails for upcoming calendar events');

Schedule::command('reminders:send-payment-due --days=3')->dailyAt('08:00');
