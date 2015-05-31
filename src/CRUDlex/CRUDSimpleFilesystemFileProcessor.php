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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

use CRUDlex\CRUDFileProcessorInterface;
use CRUDlex\CRUDEntity;

class CRUDSimpleFilesystemFileProcessor implements CRUDFileProcessorInterface {

    /**
     * Constructs a file system path for the given parameters for storing the
     * file of the file field.
     *
     * @param string $entityName
     * the entity name
     * @param CRUDEntity $entity
     * the entity
     * @param string $field
     * the file field in the entity
     *
     * @return string
     * the constructed path for storing the file of the file field
     */
    protected function getPath($entityName, CRUDEntity $entity, $field) {
        return $entity->getDefinition()->getFilePath($field).'/'.$entityName.'/'.$entity->get('id').'/'.$field;
    }

    /**
     * {@inheritdoc}
     */
    public function createFile(Request $request, CRUDEntity $entity, $entityName, $field) {
        $file = $request->files->get($field);
        if ($file) {
            $targetPath = $this->getPath($entityName, $entity, $field);
            if (!file_exists($targetPath)) {
                mkdir($targetPath, 0777, true);
            }
            $file->move($targetPath, $file->getClientOriginalName());
        }
    }

    /**
     * {@inheritdoc}
     * For now, this implementation is defensive and doesn't delete ever.
     */
    public function updateFile(Request $request, CRUDEntity $entity, $entityName, $field) {
        // We could first delete the old file, but for now, we are defensive and don't delete ever.
        $this->createFile($request, $entity, $entityName, $field);
    }

    /**
     * {@inheritdoc}
     * For now, this implementation is defensive and doesn't delete ever.
     */
    public function deleteFile(CRUDEntity $entity, $entityName, $field) {
        // For now, we are defensive and don't delete ever.
    }

    /**
     * {@inheritdoc}
     */
    public function renderFile(CRUDEntity $entity, $entityName, $field) {
        $targetPath = $this->getPath($entityName, $entity, $field);
        $fileName = $entity->get($field);
        $file = $targetPath.'/'.$fileName;
        $response = new Response('');
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file);
        finfo_close($finfo);
        $size = filesize($file);
        if ($fileName && file_exists($file)) {
            $response = new StreamedResponse(function () use ($file) {
                set_time_limit(0);
                $handle = fopen($file,"rb");
                if ($handle !== false) {
                    $chunkSize = 1024 * 1024;
                    while (!feof($handle)) {
                        $buffer = fread($handle, $chunkSize);
                        echo $buffer;
                        flush();
                    }
                    fclose($handle);
                }
            }, 200, array(
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
                'Content-length' => $size
            ));
            $response->send();
        }
        return $response;
    }
}
