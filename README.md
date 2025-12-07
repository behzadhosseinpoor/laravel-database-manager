<p align="center"><img width="96" height="96" src="/art/logo.png" alt="Laravel Database Manager"></p>

<p align="center">
<a href="https://packagist.org/packages/behzadhosseinpoor/laravel-database-manager"><img src="https://img.shields.io/packagist/dt/behzadhosseinpoor/laravel-database-manager" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/behzadhosseinpoor/laravel-database-manager"><img src="https://img.shields.io/packagist/v/behzadhosseinpoor/laravel-database-manager" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/behzadhosseinpoor/laravel-database-manager"><img src="https://img.shields.io/packagist/l/behzadhosseinpoor/laravel-database-manager" alt="License"></a>
</p>

## Introduction

**Laravel Database Manager** provides a beautiful, modern, and developer‑friendly UI for exploring and managing your
application's databases directly from the browser.

It supports multiple database connections (MySQL) and ships with a polished SPA interface built with Vue 3, Tailwind,
and
Laravel’s powerful backend.

This package makes it easy to:

- Browse table data with sorting & pagination
- Inspect table structure (columns & indexes)
- Run SQL queries safely with a smooth collapsible editor
- Switch between multiple connections
- View high‑level database metadata

Everything is handled through a simple configuration file — fully version‑controllable and team‑friendly.

<p align="center">
<img src="/art/screenshot.png" alt="Laravel Database Manager">
</p>

---

## Installation

You may install Database Manager using Composer:

```bash
composer require behzadhosseinpoor/laravel-database-manager
```

After installation, publish the package’s configuration, assets, and layout using:

```bash
php artisan database-manager:install
```

### Configuration

After installation, the main configuration file will be located at:

```
config/database-manager.php
```

Inside this file, you may customize:

- The default database connection
- The list of allowed connections
- The route path and middleware
- Authorization settings for accessing the dashboard

Each option includes clear documentation inside the file.

```
<?php

return [
    'default_connection' => env('DATABASE_MANAGER_DEFAULT_CONNECTION'),
    'name' => env('DATABASE_MANAGER_NAME'),
    'domain' => env('DATABASE_MANAGER_DOMAIN'),
    'path' => env('DATABASE_MANAGER_PATH', 'database-manager'),
    'middleware' => [
        'web'
    ],
    'connections' => [
        'mysql'
    ]
];
```

## License

Laravel Database Manager is open‑source software licensed under the [MIT license](LICENSE.md).
