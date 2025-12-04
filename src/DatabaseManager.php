<?php

namespace BehzadHosseinPoor\DatabaseManager;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Js;
use JsonException;
use RuntimeException;

class DatabaseManager
{
    /**
     * The callback that should be used to authenticate Database Manager users.
     *
     * @var Closure
     */
    public static Closure $authUsing;

    /**
     * Set the callback that should be used to authenticate Database Manager users.
     *
     * @param Closure $callback
     * @return static
     */
    public static function auth(Closure $callback): static
    {
        static::$authUsing = $callback;

        return new static;
    }

    /**
     * Determine if the given request can access the Database Manager dashboard.
     *
     * @param Request $request
     * @return bool
     */
    public static function check(Request $request): bool
    {
        return (static::$authUsing ?: function () {
            return app()->environment('local');
        })($request);
    }

    /**
     * Get the CSS for the Database Manager dashboard.
     *
     * @return HtmlString
     */
    public static function css(): HtmlString
    {
        if (($app = @file_get_contents(__DIR__ . '/../dist/app.css')) === false) {
            throw new RuntimeException('Unable to load the Database Manager dashboard CSS.');
        }

        return new HtmlString(<<<HTML
            <style>$app</style>
            HTML
        );
    }

    /**
     * Get the JS for the Database Manager dashboard.
     *
     * @return HtmlString
     * @throws JsonException
     */
    public static function js(): HtmlString
    {
        if (($js = @file_get_contents(__DIR__ . '/../dist/app.js')) === false) {
            throw new RuntimeException('Unable to load the Database Manager dashboard JavaScript.');
        }

        $databaseManager = Js::from(static::scriptVariables());

        return new HtmlString(<<<HTML
            <script type="module">
                window.DatabaseManager = $databaseManager;
                {$js}
            </script>
            HTML
        );
    }

    /**
     * Get the default JavaScript variables for Database Manager.
     *
     * @return array
     */
    public static function scriptVariables(): array
    {
        return [
            'path' => config('database-manager.path'),
            'default_connection' => config('database-manager.default_connection'),
            'connections' => json_encode(config('database-manager.connections')),
        ];
    }
}