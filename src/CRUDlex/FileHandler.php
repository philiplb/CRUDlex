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
use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Handles the files.
 */
class FileHandler {

    /**
     * @var FilesystemInterface
     */
    protected $filesystem;

    /**
     * @var EntityDefinition
     */
    protected $entityDefinition;

    /**
     * Constructs a file system path for the given parameters for storing the
     * file of the file field.
     *
     * @param string $entityName
     * the entity name
     * @param Entity $entity
     * the entity
     * @param string $field
     * the file field in the entity
     *
     * @return string
     * the constructed path for storing the file of the file field
     */
    protected function getPath($entityName, Entity $entity, $field) {
        return $this->entityDefinition->getField($field, 'path').'/'.$entityName.'/'.$entity->get('id').'/'.$field;
    }

    /**
     * Executes a function for each file field of this entity.
     *
     * @param Entity $entity
     * the just created entity
     * @param string $entityName
     * the name of the entity as this class here is not aware of it
     * @param \Closure $function
     * the function to perform, takes $entity, $entityName and $field as parameter
     */
    protected function performOnFiles(Entity $entity, $entityName, $function) {
        $fields = $this->entityDefinition->getEditableFieldNames();
        foreach ($fields as $field) {
            if ($this->entityDefinition->getType($field) == 'file') {
                $function($entity, $entityName, $field);
            }
        }
    }

    /**
     * Writes the uploaded files.
     *
     * @param AbstractData $data
     * the AbstractData instance who should receive the events
     * @param Request $request
     * the HTTP request containing the file data
     * @param Entity $entity
     * the just manipulated entity
     * @param string $entityName
     * the name of the entity as this class here is not aware of it
     * @param string $action
     * the name of the performed action
     *
     * @return boolean
     * true if all before events passed
     */
    protected function shouldWriteFile(AbstractData $data, Request $request, Entity $entity, $entityName, $action) {
        $result = $data->shouldExecuteEvents($entity, 'before', $action);
        if (!$result) {
            return false;
        }
        $filesystem = $this->filesystem;
        $this->performOnFiles($entity, $entityName, function($entity, $entityName, $field) use ($filesystem, $request) {
            $file = $request->files->get($field);
            if ($file->isValid()) {
                $path     = $this->getPath($entityName, $entity, $field);
                $filename = $path.'/'.$file->getClientOriginalName();
                if ($filesystem->has($filename)) {
                    $filesystem->delete($filename);
                }
                $stream = fopen($file->getRealPath(), 'r+');
                $filesystem->writeStream($filename, $stream);
                fclose($stream);
            }
        });
        $data->shouldExecuteEvents($entity, 'after', $action);
        return true;
    }

    /**
     * FileHandler constructor.
     * @param FilesystemInterface $filesystem
     * the filesystem to use
     */
    public function __construct(FilesystemInterface $filesystem, EntityDefinition $entityDefinition) {
        $this->filesystem       = $filesystem;
        $this->entityDefinition = $entityDefinition;
    }


    /**
     * Renders (outputs) a file of an entity. This includes setting headers
     * like the file size, mimetype and name, too.
     *
     * @param Entity $entity
     * the entity to render the file from
     * @param string $entityName
     * the name of the entity as this class here is not aware of it
     * @param string $field
     * the field of the entity containing the file to be rendered
     *
     * @return StreamedResponse
     * the HTTP streamed response
     */
    public function renderFile(Entity $entity, $entityName, $field) {
        $targetPath = $this->getPath($entityName, $entity, $field);
        $fileName   = $entity->get($field);
        $file       = $targetPath.'/'.$fileName;
        $mimeType   = $this->filesystem->getMimetype($file);
        $size       = $this->filesystem->getSize($file);
        $stream     = $this->filesystem->readStream($file);
        $response   = new StreamedResponse(function() use ($stream) {
            while ($data = fread($stream, 1024)) {
                echo $data;
                flush();
            }
            fclose($stream);
        }, 200, [
            'Cache-Control' => 'public, max-age=86400',
            'Content-length' => $size,
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"'
        ]);
        return $response;
    }


    /**
     * Deletes all files of an existing entity.
     *
     * @param AbstractData $data
     * the AbstractData instance who should receive the events
     * @param Entity $entity
     * the entity to delete the files from
     * @param string $entityName
     * the name of the entity as this class here is not aware of it
     *
     * @return boolean
     * true on successful deletion
     */
    public function deleteFiles(AbstractData $data, Entity $entity, $entityName) {
        $result = $data->shouldExecuteEvents($entity, 'before', 'deleteFiles');
        if (!$result) {
            return false;
        }
        $this->performOnFiles($entity, $entityName, function($entity, $entityName, $field) {
            // For now, we are defensive and don't delete ever. As soon as soft deletion is optional, files will get deleted.
        });
        $data->shouldExecuteEvents($entity, 'after', 'deleteFiles');
        return true;
    }

    /**
     * Deletes a specific file from an existing entity.
     *
     * @param AbstractData $data
     * the AbstractData instance who should receive the events
     * @param Entity $entity
     * the entity to delete the file from
     * @param string $entityName
     * the name of the entity as this class here is not aware of it
     * @param string $field
     * the field of the entity containing the file to be deleted
     * @return bool true on successful deletion
     * true on successful deletion
     */
    public function deleteFile(AbstractData $data, Entity $entity, $entityName, $field) {
        $result = $data->shouldExecuteEvents($entity, 'before', 'deleteFile');
        if (!$result) {
            return false;
        }
        // For now, we are defensive and don't delete ever. As soon as soft deletion is optional, files will get deleted.
        $data->shouldExecuteEvents($entity, 'after', 'deleteFile');
        return true;
    }

    /**
     * Creates the uploaded files of a newly created entity.
     *
     * @param AbstractData $data
     * the AbstractData instance who should receive the events
     * @param Request $request
     * the HTTP request containing the file data
     * @param Entity $entity
     * the just created entity
     * @param string $entityName
     * the name of the entity as this class here is not aware of it
     *
     * @return boolean
     * true if all before events passed
     */
    public function createFiles(AbstractData $data, Request $request, Entity $entity, $entityName) {
        return $this->shouldWriteFile($data, $request, $entity, $entityName, 'createFiles');
    }

    /**
     * Updates the uploaded files of an updated entity.
     *
     * @param AbstractData $data
     * the AbstractData instance who should receive the events
     * @param Request $request
     * the HTTP request containing the file data
     * @param Entity $entity
     * the updated entity
     * @param string $entityName
     * the name of the entity as this class here is not aware of it
     *
     * @return boolean
     * true on successful update
     */
    public function updateFiles(AbstractData $data, Request $request, Entity $entity, $entityName) {
        // With optional soft deletion, the file should be deleted first.
        return $this->shouldWriteFile($data, $request, $entity, $entityName, 'updateFiles');
    }

}
