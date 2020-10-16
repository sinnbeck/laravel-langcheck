<?php

namespace Sinnbeck\LangCheck\Test;

use Sinnbeck\LangCheck\LangCheckServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    public function setup(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [LangCheckServiceProvider::class];
    }
}
