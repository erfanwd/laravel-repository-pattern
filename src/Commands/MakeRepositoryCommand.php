<?php
namespace Erfanwd\RepositoryPattern\Commands;

use Illuminate\Console\Command;
use Erfanwd\RepositoryPattern\Generator;
use Erfanwd\RepositoryPattern\SyncGenerator;

class MakeRepositoryCommand extends Command
{
    protected $signature = 'make:repository {name?} {--sync}';
    protected $description = 'Generate a repository and interface for a given model';

    public function handle()
    {
        $name = $this->argument('name');
        $sync = $this->option('sync');

        if (!$name && !$sync) {
            $this->error("Model name is required.");
            return 1;
        }
        if ($name){
            $generator = new Generator($name);
            $generator->generate();
        }elseif ($sync){
            $generator = new SyncGenerator();
            $generator->generate();
        }



        $this->info("Repository and interface generated for model: {$name}");
    }
}