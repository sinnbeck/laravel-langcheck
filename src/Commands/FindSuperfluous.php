<?php

namespace Sinnbeck\LangCheck\Commands;

class FindSuperfluous extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'langcheck:super
                           {locale? : Locale to use as base}
                           {--o|only=* : Only check these locales}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find superfluous translations';

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

        $superfluous = $this->translationLocator->findSuperfluous($this->getBaseLocale(), $dotTranslations);

        if (empty($superfluous)) {
            $this->info('No superfluous translations found!');
            return;
        }

        foreach ($superfluous as $locale => $group) {
            $this->comment(sprintf('Superfluous: <info>%s</info>', $locale));
            $this->table(['Locale', 'Key', sprintf('Fallback translation (%s)', $this->getBaseLocale())], $group);
        }

    }
}
