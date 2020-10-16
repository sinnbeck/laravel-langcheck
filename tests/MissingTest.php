<?php

namespace Sinnbeck\LangCheck\Test;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Sinnbeck\LangCheck\TranslationLocator;
use Sinnbeck\LangCheck\Exceptions\MissingTranslationsException;

class MissingTest extends TestCase
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
        $this->artisan('langcheck:missing')
            ->expectsOutput('Using da as base locale')
            ->expectsOutput('-----------------------')
            ->expectsOutput('No translations found for "da"!')
            ->assertExitCode(0);
    }

    public function test_it_cannot_find_missing_translations()
    {
        Config::set('app.locale', 'en');
        $this->artisan('langcheck:missing')
            ->expectsOutput('Using en as base locale')
            ->expectsOutput('-----------------------')
            ->expectsOutput('No missing translations found!')
            ->assertExitCode(0);
    }

    public function test_that_it_find_missing_translations()
    {
        $this->setUpMissing();

        $expected = [
            [
                'da',
                'auth.username',
                'Username',
            ],
        ];

        $this->artisan('langcheck:missing')
            ->expectsOutput('Missing: da')
            ->expectsTable(['Locale', 'Key', 'Fallback translation (en)'], $expected);

    }

    public function test_that_it_trows_exception()
    {
        $this->setUpMissing();
        $this->expectException(MissingTranslationsException::class);
        $this->artisan('langcheck:missing -t');
    }

    protected function setUpMissing()
    {
        Config::set('app.locale', 'en');
        $base = $this->app['path.lang'];
        File::shouldReceive('directories')
            ->once()
            ->with($base)
            ->andReturn([
                'en',
                'da',
            ]);

        File::shouldReceive('allFiles')
            ->once()
            ->with('en')
            ->andReturn(['en' => 'auth']);

        File::shouldReceive('exists')
            ->once()
            ->andReturn(true);

        File::shouldReceive('getRequire')
            ->once()
            ->with(base_path('resources/lang/en/auth.php'))
            ->andReturn(['username' => 'Username']);

        File::shouldReceive('allFiles')
            ->once()
            ->with('da')
            ->andReturn([]);
    }
}
