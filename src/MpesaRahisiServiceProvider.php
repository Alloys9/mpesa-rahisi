<?php

namespace Alloys9\MpesaRahisi;

use Illuminate\Support\ServiceProvider;
use Alloys9\MpesaRahisi\Console\Commands\InstallMpesaRahisiPackage;

class MpesaRahisiServiceProvider extends ServiceProvider
{
    public function register()
    {


    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {

            $this->commands([
                InstallMpesaRahisiPackage::class,
            ]);
        }

    }
}
