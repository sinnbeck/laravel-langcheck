<?php

namespace Sinnbeck\LangCheck\Commands;

use Sinnbeck\LangCheck\Exceptions\MissingTranslationsException;

class FindMissing extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'langcheck:missing
                           {locale? : Locale to to use as base}
                           {--o|only=* : Only check these locales}
                           {--t|throw : Throw an exception if missing translations are found}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find missing translations';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $dotTranslations = $this->setup();

        if (empty($dotTranslations)) {
            return;
        }

        $missing = $this->translationLocator->findMissing($this->getBaseLocale(), $dotTranslations);

        if (empty($missing)) {
            $this->info('No missing translations found!');
            return;
        }

        if ($this->option('throw')) {
            throw new MissingTranslationsException('Missing translations found!');
        }

        foreach ($missing as $locale => $group) {
            $this->comment(sprintf('Missing: <info>%s</info>', $locale));
            $this->table(['Locale', 'Key', sprintf('Fallback translation (%s)', $this->getBaseLocale())], $group);
        }

    }

}
