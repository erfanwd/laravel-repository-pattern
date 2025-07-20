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

    protected function generateInterface($modelName)
    {
        $path = app_path("Repositories/{$modelName}/{$modelName}RepositoryInterface.php");

        if (File::exists($path)) {
            return;
        }

        if (!File::exists(dirname($path))) {
            File::makeDirectory(dirname($path), 0755, true);
        }

        $stub = $this->getStub('interface');
        $content = str_replace('{{className}}', $modelName, $stub);
        File::put($path, $content);
    }


    protected function generateRepository($modelName, $modelNameSpace)
    {
        $path = app_path("Repositories/{$modelName}/{$modelName}Repository.php");

        if (File::exists($path)) {
            return;
        }

        $stub = $this->getStub('repository');
        $content = str_replace(
            ['{{className}}', '{{modelName}}'],
            [$modelName, $modelNameSpace],
            $stub
        );

        File::put($path, $content);
    }

    protected function generateServiceProvider(): void
    {
        $path = app_path('Providers/RepositoriesServiceProvider.php');

        if (!File::exists($path)) {
            $stub = $this->getStub('service-provider');
            File::put($path, $stub);
        }

        $binding = "\$this->app->bind(\\App\\Repositories\\{$this->class}\\{$this->class}RepositoryInterface::class, \\App\\Repositories\\{$this->class}\\{$this->class}Repository::class);";

        $content = File::get($path);
        if (!str_contains($content, $binding)) {
            $targetLine = '$this->app->bind(BaseRepositoryInterface::class, BaseRepository::class);';

            if (str_contains($content, $targetLine)) {
                $content = str_replace(
                    $targetLine,
                    $targetLine . PHP_EOL . '        ' . $binding,
                    $content
                );
            } else {
                $content = str_replace('// bindings', $binding . PHP_EOL . '        // bindings', $content);
            }

            File::put($path, $content);
        }

        $bootstrapPath = base_path('bootstrap/providers.php');
        $providerClass = 'App\\Providers\\RepositoriesServiceProvider::class,';

        if (File::exists($bootstrapPath)) {
            $providersContent = File::get($bootstrapPath);

            if (!str_contains($providersContent, $providerClass)) {
                $providersContent = str_replace(
                    '];',
                    "    {$providerClass}" . PHP_EOL . '];',
                    $providersContent
                );

                File::put($bootstrapPath, $providersContent);
            }
        }
    }

    protected function getStub(string $type): string
    {
        return File::get(__DIR__ . "/Stubs/{$type}.stub");
    }
}
