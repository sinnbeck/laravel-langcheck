<?php

namespace Sinnbeck\LangCheck\Test;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Sinnbeck\LangCheck\TranslationLocator;

//use Illuminate\Support\Facades\Artisan;

class SuperfluousTest extends TestCase
{
    protected $translationLocator;
    public function setup(): void
    {
        parent::setup();
        $this->translationLocator = resolve(TranslationLocator::class);
    }

    public function test_it_cannot_find_translations_for_locale()
    {
        Config::set('app.locale', 'da');
        $this->artisan('langcheck:super')
        ->expectsOutput('Using da as base locale')
        ->expectsOutput('-----------------------')
        ->expectsOutput('No translations found for "da"!')
        ->assertExitCode(0);
    }

    public function test_it_cannot_find_superfluous_translations()
    {
        Config::set('app.locale', 'en');
        $this->artisan('langcheck:super')
            ->expectsOutput('Using en as base locale')
            ->expectsOutput('-----------------------')
            ->expectsOutput('No superfluous translations found!')
            ->assertExitCode(0);
    }

    public function test_that_it_find_superfluous_translations()
    {
        Config::set('app.locale', 'en');
        $base = $this->app['path.lang'];
        File::shouldReceive('directories')
            ->once()
            ->with($base)
            ->andReturn(['en', 'da']);

        File::shouldReceive('allFiles')
            ->once()
            ->with('en')
            ->andReturn(['en' => 'auth']);

        File::shouldReceive('allFiles')
            ->once()
            ->with('da')
            ->andReturn(['da' => 'auth']);

        File::shouldReceive('exists')
            ->twice()
            ->andReturn(true);

        File::shouldReceive('getRequire')
            ->once()
            ->with(base_path('resources/lang/en/auth.php'))
            ->andReturn(['username' => 'Username']);

        File::shouldReceive('getRequire')
            ->once()
            ->with(base_path('resources/lang/da/auth.php'))
            ->andReturn(['username' => 'Brugernavn', 'password' => 'Adgangskode']);

        $expected = [
            [
                'da',
                'auth.password',
                'Adgangskode'
            ]
        ];

        $this->artisan('langcheck:super')
            ->expectsOutput('Superfluous: da')
            ->expectsTable(['Locale', 'Key', 'Fallback translation (en)'], $expected);
    }
}
