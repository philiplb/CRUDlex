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

use Symfony\Component\Yaml\Yaml;

/**
 * Reads Yaml files and caches them if a writable path is given. The cache is used first on the next read.
 * It is a simple PHP class internally.
 */
class YamlReader {

    /**
     * The path for the cache files.
     * @var string
     */
    protected $cachePath;

    /**
     * Gets an absolute path for a cache file.
     *
     * @param string $fileName
     * the file to cache
     * @return string
     * the absolute path of the cache file
     */
    protected function getCacheFile($fileName) {
        return $this->cachePath.'/'.basename($fileName).'CRUDlexCache.php';
    }

    /**
     * Reads the content of the cached file if it exists.
     *
     * @param string $fileName
     * the cache file to read from
     * @return null|array
     * the cached data structure or null if the cache file was not available
     */
    protected function readFromCache($fileName) {
        $cacheFile = $this->getCacheFile($fileName);
        if (file_exists($cacheFile) && is_readable($cacheFile)) {
            include($cacheFile);
            if (isset($crudlexCacheContent)) {
                return $crudlexCacheContent;
            }
        }
        return null;
    }

    /**
     * Writes the given content to a cached PHP file.
     *
     * @param string $fileName
     * the original filename
     * @param array $content
     * the content to cache
     */
    protected function writeToCache($fileName, $content) {
        if ($this->cachePath === null || !is_dir($this->cachePath) || !is_writable($this->cachePath)) {
            return;
        }
        $encoder    = new \Riimu\Kit\PHPEncoder\PHPEncoder();
        $contentPHP = $encoder->encode($content, [
            'whitespace' => false,
            'recursion.detect' => false
        ]);
        $cache      = '<?php $crudlexCacheContent = '.$contentPHP.';';
        file_put_contents($this->getCacheFile($fileName), $cache);
    }

    /**
     * YamlReader constructor.
     * @param string|null $cachePath
     * if given, the path for the cache files which should be a writable directory
     */
    public function __construct($cachePath) {
        $this->cachePath = $cachePath;
    }

    /**
     * Reads and returns the contents of the given Yaml file. If
     * it goes wrong, it throws an exception.
     *
     * @param string $fileName
     * the file to read
     *
     * @return array
     * the file contents
     *
     * @throws \RuntimeException
     * thrown if the file could not be read or parsed
     */
    public function read($fileName) {

        $parsedYaml = $this->readFromCache($fileName);
        if ($parsedYaml !== null) {
            return $parsedYaml;
        }

        try {
            $fileContent = file_get_contents($fileName);
            $parsedYaml  = Yaml::parse($fileContent);
            if (!is_array($parsedYaml)) {
                $parsedYaml = [];
            }
            $this->writeToCache($fileName, $parsedYaml);
            return $parsedYaml;
        } catch (\Exception $e) {
            throw new \RuntimeException('Could not read Yaml file '.$fileName, $e->getCode(), $e);
        }
    }

}
