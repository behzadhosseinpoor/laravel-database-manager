<?php

return [
    'name' => env('DATABASE_MANAGER_NAME'),
    'domain' => env('DATABASE_MANAGER_DOMAIN'),
    'path' => env('DATABASE_MANAGER_PATH', 'database-manager'),
    'middleware' => ['web'],
];
