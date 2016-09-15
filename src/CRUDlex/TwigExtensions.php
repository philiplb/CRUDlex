<?php

/*
 * This file is part of the CRUDlex package.
 *
 * (c) Philip Lehmann-Böhm <philip@philiplb.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CRUDlex;

use Pimple\Container;
use Symfony\Component\Intl\Intl;

/**
 * Provides and setups the Twig extensions like filters.
 */
class TwigExtensions {

    /**
     * Registers all extensions.
     *
     * @param Container $app
     * the current application
     */
    public function registerTwigExtensions(Container $app) {
        $self = $this;
        $app->extend('twig', function(\Twig_Environment $twig) use ($self) {
            $twig->addFilter(new \Twig_SimpleFilter('arrayColumn', [$self, 'arrayColumn']));
            $twig->addFilter(new \Twig_SimpleFilter('languageName', [$self, 'getLanguageName']));
            $twig->addFilter(new \Twig_SimpleFilter('float', [$self, 'formatFloat']));
            return $twig;
        });
    }

    /**
     * To have array_column available as Twig filter.
     *
     * @param $array
     * the array
     * @param $key
     * the key
     *
     * @return array
     * the resulting array
     */
    public function arrayColumn($array, $key) {
        return array_column($array, $key);
    }

    /**
     * Gets a language name in the given language.
     *
     * @param string $language
     * the language code of the desired language name
     *
     * @return string
     * the language name in the given language or null if not available
     */
    public function getLanguageName($language) {
        return Intl::getLanguageBundle()->getLanguageName($language, $language, $language);
    }

    /**
     * Formats a float to not display in scientific notation.
     *
     * @param float $float
     * the float to format
     *
     * @return double|string
     * the formated float
     */
    public function formatFloat($float) {

        if (!$float) {
            return $float;
        }

        $zeroFraction = $float - floor($float) == 0 ? '0' : '';

        // We don't want values like 0.004 converted to  0.00400000000000000008
        if ($float > 0.0001) {
            return $float.($zeroFraction === '0' ? '.'.$zeroFraction : '');
        }

        // We don't want values like 0.00004 converted to its scientific notation 4.0E-5
        return rtrim(sprintf('%.20F', $float), '0').$zeroFraction;
    }
}
