<?php

namespace Sinnbeck\LangCheck\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Sinnbeck\LangCheck\TranslationLocator;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;

abstract class BaseCommand extends Command
{
    protected $baseLocale;
    protected $translationLocator;
    public function __construct(TranslationLocator $translationLocator)
    {
        parent::__construct();
        $this->translationLocator = $translationLocator;
    }

    protected function setup()
    {
        if (!$baseLocale = $this->argument('locale')) {
            $baseLocale = config('langcheck.override_locale') ?: config('app.locale');

        }

        $this->baseLocale = $baseLocale;

        $title = 'Using <info>' . $baseLocale . '</info> as base locale';
        $this->line($title);
        $length = Str::length(strip_tags($title));
        $this->line(str_repeat('-', $length));

        $locales = $this->option('only');

        try {
            $directories = $this->translationLocator->getLocaleDirectories($baseLocale, $locales);

        } catch (DirectoryNotFoundException $exception) {
            $this->error($exception->getMessage());
            return [];
        }

        $dotTranslations = $this->translationLocator->getDotTranslations();

        $this->info('Found ' . count($directories) . ' locale(s)');

        if (empty($dotTranslations[$baseLocale])) {
            $this->error('No translations found for "'. $baseLocale . '"!');
            return [];
        }

        return $dotTranslations;
    }

    protected function getBaseLocale()
    {
        return $this->baseLocale;
    }
}
