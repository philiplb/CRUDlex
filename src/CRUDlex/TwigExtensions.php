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

use Symfony\Component\Intl\Intl;

/**
 * Provides the Twig extensions like filters.
 */
class TwigExtensions
{

    /**
     * Formats the given time value to a timestring defined by the $pattern
     * parameter.
     *
     * If the value is false (like null), an empty string is
     * returned. Else, the value is tried to be parsed as datetime via the
     * given pattern. If that fails, it is tried to be parsed with the pattern
     * 'Y-m-d H:i:s'. If that fails, the value is returned unchanged. Else, it
     * is returned formatted with the given pattern. The effect is to shorten
     * 'Y-m-d H:i:s' to 'Y-m-d' for example.
     *
     * @param string $value
     * the value to be formatted
     * @param string $timezone
     * the timezone of the value
     * @param string $pattern
     * the pattern with which the value is parsed and formatted
     *
     * @return string
     * the formatted value
     */
    protected function formatTime($value, $timezone, $pattern)
    {
        if (!$value) {
            return '';
        }
        $result = \DateTime::createFromFormat($pattern, $value, new \DateTimeZone($timezone));
        if ($result === false) {
            $result = \DateTime::createFromFormat('Y-m-d H:i:s', $value, new \DateTimeZone($timezone));
        }
        if ($result === false) {
            return $value;
        }
        $result->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        return $result->format($pattern);
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
    public function getLanguageName($language)
    {
        return Intl::getLanguageBundle()->getLanguageName($language, null, $language);
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
    public function formatFloat($float)
    {

        if (!$float) {
            return $float;
        }

        $zeroFraction = $float - floor($float) == 0 ? '0' : '';

        // We don't want values like 0.004 converted to 0.00400000000000000008
        if ($float > 0.0001) {
            return $float.($zeroFraction === '0' ? '.'.$zeroFraction : '');
        }

        // We don't want values like 0.00004 converted to its scientific notation 4.0E-5
        return rtrim(sprintf('%.20F', $float), '0').$zeroFraction;
    }

    /**
     * Formats the given value to a date of the format 'Y-m-d'.
     *
     * @param string $value
     * the value, might be of the format 'Y-m-d H:i' or 'Y-m-d'
     * @param boolean $isUTC
     * whether the given value is in UTC
     *
     * @return string
     * the formatted result or an empty string on null value
     */
    public function formatDate($value, $isUTC)
    {
        $timezone = $isUTC ? 'UTC' : date_default_timezone_get();
        return $this->formatTime($value, $timezone, 'Y-m-d');
    }

    /**
     * Formats the given value to a date of the format 'Y-m-d H:i'.
     *
     * @param string $value
     * the value, might be of the format 'Y-m-d H:i'
     * @param boolean $isUTC
     * whether the given value is in UTC
     *
     * @return string
     * the formatted result or an empty string on null value
     */
    public function formatDateTime($value, $isUTC)
    {
        $timezone = $isUTC ? 'UTC' : date_default_timezone_get();
        return $this->formatTime($value, $timezone, 'Y-m-d H:i');
    }

}
