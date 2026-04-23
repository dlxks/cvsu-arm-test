<?php

it('defaults the application timezone to Philippine time', function () {
    expect(config('app.timezone'))->toBe('Asia/Manila')
        ->and(now()->getTimezone()->getName())->toBe('Asia/Manila');
});
