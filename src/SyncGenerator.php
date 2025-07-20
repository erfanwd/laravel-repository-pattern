<?php

namespace Erfanwd\RepositoryPattern;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SyncGenerator
{

    public function generate(): void
    {
        $models = $this->getAllModels();
        foreach ($models as $model){
            $this->generateInterface($model['name']);
            $this->generateRepository($model['name'], $model['name_space']);
            $this->generateServiceProvider($model['name']);
        }

    }


    protected function getAllModels(): array
    {
        $models = [];
        $modelPath = app_path('Models');
        $namespace = 'App\Models';

        if (!File::exists($modelPath)) {
            return $models;
        }

        $files = File::allFiles($modelPath);

        foreach ($files as $file) {
            $relativePath = str_replace($modelPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
            $className = str_replace(['/', '.php'], ['\\', ''], $relativePath);
            $fullClass = $namespace . '\\' . $className;

            if (class_exists($fullClass)) {
                $shortName = class_basename($fullClass);
                $models[] = [
                    'name' => $shortName,
                    'name_space' => $fullClass,
                ];
            }
        }

        return $models;
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


    protected function generateServiceProvider($modelName): void
    {
        $path = app_path('Providers/RepositoriesServiceProvider.php');

        if (!File::exists($path)) {
            $stub = $this->getStub('service-provider');
            File::put($path, $stub);
        }

        $binding = "\$this->app->bind(\\App\\Repositories\\{$modelName}\\{$modelName}RepositoryInterface::class, \\App\\Repositories\\{$modelName}\\{$modelName}Repository::class);";

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
