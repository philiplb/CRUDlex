-------------------
CRUDlex\\YamlReader
-------------------

.. php:namespace: CRUDlex

.. php:class:: YamlReader

    Reads Yaml files and caches them if a writable path is given. The cache is used first on the next read.
    It is a simple PHP class internally.

    .. php:attr:: cachePath

        protected

        The path for the cache files.

    .. php:method:: getCacheFile($fileName)

        Gets an absolute path for a cache file.

        :type $fileName: string
        :param $fileName: the file to cache
        :returns: string the absolute path of the cache file

    .. php:method:: readFromCache($fileName)

        Reads the content of the cached file if it exists.

        :type $fileName: string
        :param $fileName: the cache file to read from
        :returns: null|array the cached data structure or null if the cache file was not available

    .. php:method:: writeToCache($fileName, $content)

        Writes the given content to a cached PHP file.

        :type $fileName: string
        :param $fileName: the original filename
        :type $content: array
        :param $content: the content to cache

    .. php:method:: __construct($cachePath)

        YamlReader constructor.

        :type $cachePath: string|null
        :param $cachePath: if given, the path for the cache files which should be a writable directory

    .. php:method:: read($fileName)

        Reads and returns the contents of the given Yaml file. If
        it goes wrong, it throws an exception.

        :type $fileName: string
        :param $fileName: the file to read
        :returns: array the file contents
