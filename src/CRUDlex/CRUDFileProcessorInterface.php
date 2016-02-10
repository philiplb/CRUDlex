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

use CRUDlex\CRUDEntity;

/**
 * This interface is used to handle file uploads.
 */
interface CRUDFileProcessorInterface {

    /**
     * Creates the uploaded file of a newly created entity.
     *
     * @param Request $request
     * the HTTP request containing the file data
     * @param CRUDEntity $entity
     * the just created entity
     * @param string $entityName
     * the name of the entity as this class here is not aware of it
     * @param string $field
     * the file field
     */
    public function createFile(Request $request, CRUDEntity $entity, $entityName, $field);

    /**
     * Updates the uploaded file of an updated entity.
     *
     * @param Request $request
     * the HTTP request containing the file data
     * @param CRUDEntity $entity
     * the updated entity
     * @param string $entityName
     * the name of the entity as this class here is not aware of it
     * @param string $field
     * the file field
     */
    public function updateFile(Request $request, CRUDEntity $entity, $entityName, $field);

    /**
     * Deletes a specific file from an existing entity.
     *
     * @param CRUDEntity $entity
     * the entity to delete the file from
     * @param string $entityName
     * the name of the entity as this class here is not aware of it
     * @param string $field
     * the field of the entity containing the file to be deleted
     */
    public function deleteFile(CRUDEntity $entity, $entityName, $field);

    /**
     * Renders (outputs) a file of an entity. This includes setting headers
     * like the file size, mimetype and name, too.
     *
     * @param CRUDEntity $entity
     * the entity to render the file from
     * @param string $entityName
     * the name of the entity as this class here is not aware of it
     * @param string $field
     * the field of the entity containing the file to be rendered
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * the HTTP response, likely to be a streamed one
     */
    public function renderFile(CRUDEntity $entity, $entityName, $field);
}
