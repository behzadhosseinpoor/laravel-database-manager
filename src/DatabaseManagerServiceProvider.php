<?php
/** @noinspection PhpUnused */

/** @noinspection PhpUndefinedFunctionInspection */

namespace BehzadHosseinPoor\DatabaseManager;

use BehzadHosseinPoor\DatabaseManager\Console\InstallCommand;
use BehzadHosseinPoor\DatabaseManager\Http\Middleware\DatabaseManagerExceptionMiddleware;
use Illuminate\Contracts\Foundation\CachesRoutes;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

class DatabaseManagerServiceProvider extends ServiceProvider
{
    use ServiceBindings;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        if (!defined('DATABASE_MANAGER_PATH')) {
            define('DATABASE_MANAGER_PATH', realpath(__DIR__ . '/../'));
        }

        $this->configure();
        $this->registerServices();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */

    public function boot(): void
    {
        $this->validateConfig();
        $this->normalizeConfig();
        $this->registerRoutes();
        $this->registerResources();
        $this->offerPublishing();
        $this->registerCommands();
    }

    /**
     * Normalize the Database Manager configuration.
     *
     * @return void
     */
    protected function normalizeConfig(): void
    {
        $config = $this->app['config'];

        if (!$config->get('database-manager.name')) {
            $config->set('database-manager.name', $config->get('app.name'));
        }

        if (!$config->get('database-manager.default_connection')) {
            $connections = $config->get('database-manager.connections', []);

            if (!empty($connections)) {
                $config->set('database-manager.default_connection', $connections[0]);
            }
        }
    }

    /**
     * Validate the Database Manager configuration.
     *
     * @return void
     */
    protected function validateConfig(): void
    {
        $config = $this->app['config']->get('database-manager');

        $supportedDrivers = ['mysql'];

        if (empty($config['connections']) || !is_array($config['connections'])) {
            throw new InvalidArgumentException(
                "database-manager.connections must contain at least one connection."
            );
        }

        foreach ($config['connections'] as $connectionName) {
            $connectionConfig = config("database.connections.$connectionName");

            if (!$connectionConfig) {
                throw new InvalidArgumentException(
                    "Connection [$connectionName] does not exist in config/database.php."
                );
            }

            $driver = $connectionConfig['driver'] ?? null;

            if (!in_array($driver, $supportedDrivers, true)) {
                throw new InvalidArgumentException(
                    "Connection [$connectionName] uses unsupported driver [$driver]."
                );
            }
        }

        if (!empty($config['default_connection'])) {
            if (!in_array($config['default_connection'], $config['connections'], true)) {
                throw new InvalidArgumentException(
                    "database-manager.default_connection [{$config['default_connection']}] must exist in database-manager.connections."
                );
            }
        }
    }

    /**
     * Setup the configuration for Database Manager.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/database-manager.php',
            'database-manager'
        );
    }

    /**
     * Setup the resource publishing groups for Database Manager.
     *
     * @return void
     */
    protected function offerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../stubs/DatabaseManagerServiceProvider.stub' => app_path('Providers/DatabaseManagerServiceProvider.php'),
            ], 'database-manager-provider');

            $this->publishes([
                __DIR__ . '/../config/database-manager.php' => config_path('database-manager.php'),
            ], 'database-manager-config');
        }
    }

    /**
     * Register Database Manager's services in the container.
     *
     * @return void
     */
    protected function registerServices(): void
    {
        foreach ($this->serviceBindings as $key => $value) {
            is_numeric($key)
                ? $this->app->singleton($value)
                : $this->app->singleton($key, $value);
        }
    }

    /**
     * Register the Database Manager routes.
     *
     * @return void
     */
    protected function registerRoutes(): void
    {
        if ($this->app instanceof CachesRoutes && $this->app->routesAreCached()) {
            return;
        }

        Route::group([
            'domain' => config('database-manager.domain'),
            'prefix' => config('database-manager.path'),
            'namespace' => 'BehzadHosseinPoor\DatabaseManager\Http\Controllers',
            'middleware' => $this->getMiddlewares(),
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }

    /**
     * Register the Database Manager resources.
     *
     * @return void
     */
    protected function registerResources(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'database-manager');
    }

    /**
     * Register the Database Manager Artisan commands.
     *
     * @return void
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }

    protected function getMiddlewares(): array
    {
        $middlewares = config('database-manager.middleware', ['web']);

        if (is_string($middlewares)) {
            $middlewares = [$middlewares];
        }

        if (!is_array($middlewares)) {
            $middlewares = [];
        }

        $middlewares[] = DatabaseManagerExceptionMiddleware::class;

        return $middlewares;
    }
}
