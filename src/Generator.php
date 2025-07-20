<?php

namespace Erfanwd\RepositoryPattern;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Generator
{
    protected string $name;
    protected string $model;
    protected string $class;

    public function __construct(string $name)
    {
        $this->name = Str::studly($name);
        $this->model = "App\\Models\\{$this->name}";
        $this->class = $this->name;
    }

    public function generate(): void
    {
        $this->generateInterface();
        $this->generateRepository();
        $this->generateServiceProvider();
    }

    protected function generateInterface()
    {
        $path = app_path("Repositories/{$this->class}/{$this->class}RepositoryInterface.php");

        if (!File::exists(dirname($path))) {
            File::makeDirectory(dirname($path), 0755, true);
        }

        $stub = $this->getStub('interface');
        $content = str_replace('{{className}}', $this->class, $stub);
        File::put($path, $content);
    }

    protected function generateRepository()
    {
        $path = app_path("Repositories/{$this->class}/{$this->class}Repository.php");

        $stub = $this->getStub('repository');
        $content = str_replace(
            ['{{className}}', '{{modelName}}'],
            [$this->class, $this->model],
            $stub
        );

        File::put($path, $content);
    }

    protected function generateServiceProvider()
    {
        $path = app_path('Providers/RepositoriesServiceProvider.php');

        if (!File::exists($path)) {
            $stub = $this->getStub('service-provider');
            File::put($path, $stub);
        }

        $binding = "        \$this->app->bind(\\App\\Repositories\\{$this->class}\\{$this->class}RepositoryInterface::class, \\App\\Repositories\\{$this->class}\\{$this->class}Repository::class);";

        $content = File::get($path);
        if (!str_contains($content, $binding)) {
            $content = str_replace('// bindings', $binding . PHP_EOL . '        // bindings', $content);
            File::put($path, $content);
        }
    }

    protected function getStub(string $type): string
    {
        return File::get(__DIR__ . "/Stubs/{$type}.stub");
    }
}
