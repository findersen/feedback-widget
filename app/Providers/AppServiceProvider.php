<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('ticket-submit', function (Request $request) {
            $email = $request->string('email')->lower()->trim()->toString();
            $phone = trim((string) $request->input('phone', ''));

            $limits = [];

            if ($email !== '') {
                $limits[] = Limit::perDay(1)->by('ticket:email:' . $email);
            }

            if ($phone !== '') {
                $limits[] = Limit::perDay(1)->by('ticket:phone:' . $phone);
            }

            if ($limits === []) {
                $limits[] = Limit::perDay(1)->by('ticket:ip:' . $request->ip());
            }

            return $limits;
        });
    }
}
