<?php
namespace Erfanwd\RepositoryPattern;

use Illuminate\Support\ServiceProvider;
use Erfanwd\RepositoryPattern\Commands\MakeRepositoryCommand;

class RepositoryGeneratorServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            MakeRepositoryCommand::class,
        ]);
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/Publishable/BaseRepository.php' => app_path('Repositories/BaseRepository.php'),
            __DIR__.'/Publishable/BaseRepositoryInterface.php' => app_path('Repositories/BaseRepositoryInterface.php'),
        ], 'repository-base');
    }
}