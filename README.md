# Laravel LangCheck

Laravel LangCheck is created to help developers find missing translations in the lang directory of Laravel

## Installation
```sh
$ composer require sinnbeck/laravel-langcheck --dev
```

## Configuration
Laravel LangCheck will use the locale defined in laravel by default. It is possible to override with by publishing the config file.
```sh
$ php artisan vendor:publish --provider="Sinnbeck\LangCheck\LangCheckServiceProvider"
```

## Usage
Laravel Lang Check is purely made to be used with artisan and currently has two commands available.

### Find missing
 ```sh
 $ php artisan langcheck:missing
 ```
Will render a table for each locale found with missing translations. The key is setup the same as when using the translations in laravel `folder.key.key`

It is possible to ensure that missing translations are caught, but passing the flag `--throw`, to make the command throw an exception on missing translations.

### Find superfluous
 ```sh
 $ php artisan langcheck:super
 ```
Same as missing except it shows locales with extra translations that are most likely not in use anymore.


## Todo
This package is still in alpha stage and there are several things I plan on adding.
* Implement option to specify base locale
* Implement option to limit checked locales
* Create/remove translations automatically?
