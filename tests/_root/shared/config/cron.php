<?php

return [
    'path' => env('CRON_PATH'),
    'lock_path' => env('CRON_LOCK_PATH'),
    'max_lock_age' => (int) env('CRON_MAX_LOCK_AGE', 86400),
];
