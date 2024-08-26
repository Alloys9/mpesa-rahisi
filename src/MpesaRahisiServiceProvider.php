<?php

namespace Alloys9\MpesaRahisi;

use Illuminate\Support\ServiceProvider;
use Alloys9\MpesaRahisi\Console\Commands\InstallMpesaRahisiPackage;

class MpesaRahisiServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register the command
        $this->commands([
            InstallMpesaRahisiPackage::class,
        ]);
    }

    public function boot()
    {

    }
}
