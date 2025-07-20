<?php
namespace Erfanwd\RepositoryPattern;

use Illuminate\Support\ServiceProvider;

class RepositoryGeneratorServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            MakeRepositoryCommand::class,
        ]);
    }
}