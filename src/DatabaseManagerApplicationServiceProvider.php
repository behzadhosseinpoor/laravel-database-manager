<?php
/** @noinspection PhpParamsInspection */
/** @noinspection PhpUnused */
/** @noinspection PhpUndefinedFunctionInspection */

namespace BehzadHosseinPoor\DatabaseManager;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class DatabaseManagerApplicationServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->authorization();
    }

    /**
     * Configure the Database Manager authorization services.
     *
     * @return void
     */
    protected function authorization(): void
    {
        $this->gate();

        DatabaseManager::auth(function ($request) {
            return Gate::check('viewDatabaseManager', [$request->user()]) || app()->environment('local');
        });
    }

    /**
     * Register the Database Manager gate.
     *
     * This gate determines who can access Database Manager in non-local environments.
     *
     * @return void
     */
    protected function gate(): void
    {
        Gate::define('viewDatabaseManager', function ($user) {
            return in_array($user->email, [

            ]);
        });
    }
}