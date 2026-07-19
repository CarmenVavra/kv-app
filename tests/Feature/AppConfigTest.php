<?php

it('runs on the Europe/Vienna timezone from APP_TIMEZONE, not the Laravel default of UTC', function () {
    // Regression guard: config/app.php ships from the Laravel skeleton with a hardcoded 'UTC',
    // not wired to env('APP_TIMEZONE'). .env already set APP_TIMEZONE=Europe/Vienna, but without
    // this wiring it was silently ignored - every now()/Carbon call, and the scheduled
    // "montags 07:00" weekly summary, ran on UTC instead of Vienna local time.
    expect(config('app.timezone'))->toBe('Europe/Vienna');
});
