
<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\RefreshSubscriptionStatus;

/*
|--------------------------------------------------------------------------
| Console Commands
|--------------------------------------------------------------------------
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Subscription Scheduler
|--------------------------------------------------------------------------
|
| Automatically update expired trial and paid subscriptions every day.
|
*/

Schedule::call(function () {
    app(RefreshSubscriptionStatus::class)->execute();
})
    ->dailyAt('00:10')
    ->name('refresh-subscription-status')
    ->withoutOverlapping();