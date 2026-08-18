<?php

use Illuminate\Support\Facades\Schedule;

// ─── Payout Auto-Release ─────────────────────────────────────────────────────
// Runs every hour to check and release eligible seller payouts.
// In production: ensure `php artisan schedule:run` is in a cron:
//   * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
Schedule::command('payouts:release')->hourly()->withoutOverlapping();
