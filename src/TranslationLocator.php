<?php
namespace Sinnbeck\LangCheck;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;

class TranslationLocator
{
    private $directories = [];
    /**
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param \Illuminate\Filesystem\Filesystem            $filesystem
     */
    public function __construct(Application $app, Filesystem $filesystem)
    {
        $this->app = $app;
        $this->filesystem = $filesystem;
    }
    public function getLocaleDirectories($baseLocale, $only = [])
    {
        $base = $this->app['path.lang'];
        $directories = collect($this->filesystem->directories($base));

        if (!empty($only)) {
            $only = array_merge($only, [$baseLocale]);
            $names = $directories->map('basename');
            $diff = collect($only)->diff($names);
            if (!$diff->isEmpty()) {
                throw new DirectoryNotFoundException('No directories found for: ' . $diff->implode(', '));
            }
            $directories = $directories->filter(function($directory) use ($only) {
                return in_array(basename($directory), $only);
            });
        }


        return $this->directories = $directories;
    }

    public function getDotTranslations()
    {
        $dotTranslations = [];
        foreach ($this->directories as $directory) {
            $locale = basename($directory);
            $dotTranslations[$locale] = [];
            foreach ($this->filesystem->allfiles($directory) as $file) {
                $info = pathinfo($file);
                $filePath = $info['filename'];
                $loadedTranslations = Lang::getLoader()->load($locale, $filePath);
                $basePath = basename($filePath);
                if ($basePath == 'validation') {
                    unset($loadedTranslations['attributes']);

                }
                $dotTranslations[$locale] = array_merge($dotTranslations[$locale], Arr::dot([$basePath => $loadedTranslations]));

            }

        }

        return $dotTranslations;
    }

    public function findMissing($baseLocale, $dotTranslations)
    {
        $missing = [];
        foreach ($dotTranslations[$baseLocale] as $dot => $translation) {
            foreach ($dotTranslations as $locale => $item) {
                if ($locale == $baseLocale || $dot === 'validation.attributes') {
                    continue;
                }

                if (!isset($item[$dot])) {
                    $missing[$locale][] = [$locale, $dot, $translation];
                }
            }
        }
        return $missing;
    }

    public function findSuperfluous($baseLocale, $dotTranslations)
    {
        $superfluous = [];
        foreach ($dotTranslations as $locale => $item) {
            if ($locale == $baseLocale) {
                continue;
            }

            foreach ($item as $dot => $translation) {
                if (!isset($dotTranslations[$baseLocale][$dot])) {
                    $superfluous[$locale][] = [$locale, $dot, $translation];
                }

            }
        }
        return $superfluous;
    }
}
