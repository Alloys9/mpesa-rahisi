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

        $this->replaceConfig();
        $this->copyControllers();
        $this->copyMigrations();
        $this->copyModels();
        $this->copyViews();
        $this->appendRoutes();
        $this->replaceCsrfMiddleware();

        $this->info('M-Pesa Rahisi package installed successfully.');
    }

    protected function replaceConfig()
    {
        $this->info('Replacing configuration files...');
        $this->replaceFile(base_path('config/app.php'), __DIR__ . '/../../config/app.php');
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

    protected function appendRoutes()
    {
        $this->info('Appending route files...');
        $this->appendToFile(base_path('routes/web.php'), __DIR__ . '/../../routes/web.php');
    }

    protected function replaceCsrfMiddleware()
    {
        $this->info('Replacing CSRF middleware...');

        $targetFile = base_path('vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/VerifyCsrfToken.php');
        $sourceFile = __DIR__ . '/../../vendor/VerifyCsrfToken.php';

        if (!$this->files->exists($targetFile)) {
            $this->info("Creating $targetFile as it does not exist...");
            $this->files->ensureDirectoryExists(dirname($targetFile));
            $this->files->put($targetFile, $this->files->get($sourceFile));
        } else {
            $sourceContent = $this->files->get($sourceFile);
            $targetContent = $this->files->get($targetFile);

            if (strpos($targetContent, $sourceContent) === false) {
                $this->info("Replacing content in $targetFile...");
                $this->files->put($targetFile, $sourceContent);
            } else {
                $this->info("No replacement needed, content already present in $targetFile.");
            }
        }
    }


    protected function copyDirectory($src, $dest)
    {
        if (!$this->files->exists($src)) {
            $this->error("Source directory $src does not exist.");
            return;
        }

        $this->files->copyDirectory($src, $dest);
    }

    protected function replaceFile($targetFile, $sourceFile)
    {
        if (!$this->files->exists($sourceFile)) {
            $this->error("Source file $sourceFile does not exist.");
            return;
        }

        $this->files->copy($sourceFile, $targetFile);
    }

    protected function appendToFile($targetFile, $sourceFile)
    {
        if (!$this->files->exists($sourceFile)) {
            $this->error("Source file $sourceFile does not exist.");
            return;
        }

        $sourceContent = $this->files->get($sourceFile);
        $this->files->append($targetFile, "\n" . $sourceContent);
    }
}
