<?php

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in($dir = '../../src/CRUDlex')
;

return new Sami($iterator, array(
    'title'                => 'CRUDlex API',
    'build_dir'            => __DIR__.'/0.9.5',
    'default_opened_level' => 2,
));
