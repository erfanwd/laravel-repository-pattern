<?php
namespace Erfanwd\RepositoryPattern\Commands;

use Illuminate\Console\Command;
use Erfanwd\RepositoryPattern\Generator;

class MakeRepositoryCommand extends Command
{
    protected $signature = 'make:repository {name}';
    protected $description = 'Generate a repository and interface for a given model';

    public function handle()
    {
        $name = $this->argument('name');

        $generator = new Generator($name);
        $generator->generate();

        $this->info("Repository and interface generated for model: {$name}");
    }
}