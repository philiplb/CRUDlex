-----------------------
CRUDlex\\TwigExtensions
-----------------------

.. php:namespace: CRUDlex

.. php:class:: TwigExtensions

    Provides the Twig extensions like filters.

    .. php:method:: formatTime($value, $timezone, $pattern)

        Formats the given time value to a timestring defined by the $pattern
        parameter.

        If the value is false (like null), an empty string is returned. Else, the
        value is tried to be parsed as datetime via the given pattern. If that
        fails, it is tried to be parsed with the pattern
        'Y-m-d H:i:s'. If that fails, the value is returned unchanged. Else, it is
        returned formatted with the given pattern. The effect is to shorten
        'Y-m-d H:i:s' to 'Y-m-d' for example.

        :type $value: string
        :param $value: the value to be formatted
        :type $timezone: string
        :param $timezone: the timezone of the value
        :type $pattern: string
        :param $pattern: the pattern with which the value is parsed and formatted
        :returns: string the formatted value

    .. php:method:: getLanguageName($language)

        Gets a language name in the given language.

        :type $language: string
        :param $language: the language code of the desired language name
        :returns: string the language name in the given language or null if not available

    .. php:method:: formatFloat($float)

        Formats a float to not display in scientific notation.

        :type $float: float
        :param $float: the float to format
        :returns: double|string the formated float

    .. php:method:: formatDate($value, $isUTC)

        Formats the given value to a date of the format 'Y-m-d'.

        :type $value: string
        :param $value: the value, might be of the format 'Y-m-d H:i' or 'Y-m-d'
        :type $isUTC: boolean
        :param $isUTC: whether the given value is in UTC
        :returns: string the formatted result or an empty string on null value

    .. php:method:: formatDateTime($value, $isUTC)

        Formats the given value to a date of the format 'Y-m-d H:i'.

        :type $value: string
        :param $value: the value, might be of the format 'Y-m-d H:i'
        :type $isUTC: boolean
        :param $isUTC: whether the given value is in UTC
        :returns: string the formatted result or an empty string on null value
