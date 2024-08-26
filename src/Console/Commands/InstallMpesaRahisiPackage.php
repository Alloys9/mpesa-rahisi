<?php

namespace Alloys9\MpesaRahisi\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InstallMpesaRahisiPackage extends Command
{
    protected $signature = 'mpesa-rahisi:install';
    protected $description = 'Install the M-Pesa Rahisi package';

    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $this->info('Installing M-Pesa Rahisi package...');

        $this->mergeConfig();
        $this->copyControllers();
        $this->copyMigrations();
        $this->copyModels();
        $this->copyViews();
        $this->mergeRoutes();
        $this->mergeCsrfMiddleware();

        $this->info('M-Pesa Rahisi package installed successfully.');
    }

    protected function mergeConfig()
    {
        $this->info('Merging configuration files...');
        $this->mergeFile(base_path('config/app.php'), __DIR__ . '/../../config/app.php');
    }

    protected function copyControllers()
    {
        $this->info('Copying controllers...');
        $this->copyDirectory(__DIR__ . '/../../Controllers', app_path('Http/Controllers'));
    }

    protected function copyMigrations()
    {
        $this->info('Copying migrations...');
        $this->copyDirectory(__DIR__ . '/../../database/migrations', database_path('migrations'));
    }

    protected function copyModels()
    {
        $this->info('Copying models...');
        $this->copyDirectory(__DIR__ . '/../../Models', app_path('Models'));
    }

    protected function copyViews()
    {
        $this->info('Copying views...');
        $this->copyDirectory(__DIR__ . '/../../resources/views', resource_path('views'));
    }

    protected function mergeRoutes()
    {
        $this->info('Merging route files...');
        $this->mergeFile(base_path('routes/web.php'), __DIR__ . '/../../routes/web.php');
    }

    protected function mergeCsrfMiddleware()
    {
        $this->info('Merging CSRF middleware...');
        $this->mergeFile(base_path('vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/VerifyCsrfToken.php'), __DIR__ . '/../../vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/VerifyCsrfToken.php');
    }

    protected function copyDirectory($src, $dest)
    {
        $this->files->copyDirectory($src, $dest);
    }

    protected function mergeFile($targetFile, $sourceFile)
    {
        $sourceContent = $this->files->get($sourceFile);
        $targetContent = $this->files->get($targetFile);

        // Split the content into lines
        $sourceLines = explode("\n", $sourceContent);
        $targetLines = explode("\n", $targetContent);

        // Add only the lines that are not already present in the target file
        foreach ($sourceLines as $line) {
            if (!in_array($line, $targetLines)) {
                $targetContent .= "\n" . $line;
            }
        }

        // Write the merged content back to the target file
        $this->files->put($targetFile, $targetContent);
    }

}
