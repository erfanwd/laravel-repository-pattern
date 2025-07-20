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
}