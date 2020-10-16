<?php

namespace Sinnbeck\LangCheck;

use Illuminate\Support\ServiceProvider;
use Sinnbeck\LangCheck\Commands\FindMissing;
use Sinnbeck\LangCheck\Commands\FindSuperfluous;

class LangCheckServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config.php' => config_path('langcheck.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                FindMissing::class,
                FindSuperfluous::class,
            ]);
        }

    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config.php', 'langcheck'
        );
    }
}
