<?php

namespace BehzadHosseinPoor\DatabaseManager\Console;

use Illuminate\Console\Command;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'database-manager:install')]
class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database-manager:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all of the Database Manager resources';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->components->info('Installing Database Manager resources.');

        collect([
            'Service Provider' => fn() => $this->callSilent('vendor:publish', ['--tag' => 'database-manager-provider']) == 0,
            'Configuration' => fn() => $this->callSilent('vendor:publish', ['--tag' => 'database-manager-config']) == 0,
        ])->each(fn($task, $description) => $this->components->task($description, $task));

        $this->registerDatabaseManagerServiceProvider();

        $this->components->info('Database Manager scaffolding installed successfully.');
    }

    /**
     * Register the Database Manager service provider in the application configuration file.
     *
     * @return void
     */
    protected function registerDatabaseManagerServiceProvider(): void
    {
        $namespace = Str::replaceLast('\\', '', $this->laravel->getNamespace());

        if (file_exists($this->laravel->bootstrapPath('providers.php'))) {
            ServiceProvider::addProviderToBootstrapFile("$namespace\\Providers\\DatabaseManagerServiceProvider");
        } else {
            $appConfig = file_get_contents(config_path('app.php'));

            if (Str::contains($appConfig, $namespace . '\\Providers\\DatabaseManagerServiceProvider::class')) {
                return;
            }

            file_put_contents(config_path('app.php'), str_replace(
                "$namespace\\Providers\EventServiceProvider::class," . PHP_EOL,
                "$namespace\\Providers\EventServiceProvider::class," . PHP_EOL . "        $namespace\Providers\DatabaseManagerServiceProvider::class," . PHP_EOL,
                $appConfig
            ));
        }

        file_put_contents(app_path('Providers/DatabaseManagerServiceProvider.php'), str_replace(
            "namespace App\Providers;",
            "namespace $namespace\Providers;",
            file_get_contents(app_path('Providers/DatabaseManagerServiceProvider.php'))
        ));
    }
}