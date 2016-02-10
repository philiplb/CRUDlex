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

/**
 * Small utility class to generate functions for streamed responses returning
 * a file.
 */
class CRUDStreamedFileResponse {

    /**
     * Generates a lambda which is streaming the given file to standard output.
     *
     * @param string $file
     * the filename to stream
     */
    public function getStreamedFileFunction($file) {
        return function() use ($file) {
            set_time_limit(0);
            $handle = fopen($file, 'rb');
            if ($handle !== false) {
                $chunkSize = 1024 * 1024;
                while (!feof($handle)) {
                    $buffer = fread($handle, $chunkSize);
                    echo $buffer;
                    flush();
                }
                fclose($handle);
            }
        };
    }

}
