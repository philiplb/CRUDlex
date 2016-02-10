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

use CRUDlex\CRUDEntityDefinition;
use CRUDlex\CRUDEntity;

/**
 * The abstract class for reading and writing data.
 */
abstract class CRUDData {

    /**
     * Return value on successful deletion.
     */
    const DELETION_SUCCESS = 0;

    /**
     * Return value on failed deletion due to existing references.
     */
    const DELETION_FAILED_STILL_REFERENCED = 1;

    /**
     * Return value on failed deletion due to a failed before delete event.
     */
    const DELETION_FAILED_EVENT = 2;

    /**
     * Holds the {@see CRUDEntityDefinition} entity definition.
     */
    protected $definition;

    /**
     * Holds the {@see CRUDFileProcessorInterface} file processor.
     */
    protected $fileProcessor;

    /**
     * Holds the events.
     */
    protected $events;

    /**
     * Performs the actual deletion.
     *
     * @param CRUDEntity $entity
     * the id of the entry to delete
     * @param boolean $deleteCascade
     * whether to delete children and subchildren
     *
     * @return integer
     * true on successful deletion
     */
    abstract protected function doDelete(CRUDEntity $entity, $deleteCascade);

    /**
     * Creates an {@see CRUDEntity} from the raw data array with the field name
     * as keys and field values as values.
     *
     * @param array $row
     * the array with the raw data
     *
     * @return CRUDEntity
     * the entity containing the array data then
     */
    protected function hydrate(array $row) {
        $fieldNames = $this->definition->getFieldNames();
        $entity = new CRUDEntity($this->definition);
        foreach ($fieldNames as $fieldName) {
            $entity->set($fieldName, $row[$fieldName]);
        }
        return $entity;
    }

    /**
     * Executes the event chain of an entity.
     *
     * @param CRUDEntity $entity
     * the entity having the event chain to execute
     * @param string $moment
     * the "moment" of the event, can be either "before" or "after"
     * @param string $action
     * the "action" of the event, can be either "create", "update" or "delete"
     *
     * @return boolean
     * true on successful execution of the full chain or false if it broke at
     * any point (and stopped the execution)
     */
    protected function executeEvents(CRUDEntity $entity, $moment, $action) {
        if ($this->events !== null && array_key_exists($moment.'.'.$action, $this->events)) {
            foreach ($this->events[$moment.'.'.$action] as $event) {
                $result = $event($entity);
                if (!$result) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Executes a function for each file field of this entity.
     *
     * @param CRUDEntity $entity
     * the just created entity
     * @param string $entityName
     * the name of the entity as this class here is not aware of it
     * @param \Closure $function
     * the function to perform, takes $entity, $entityName and $field as parameter
     */
    protected function performOnFiles(CRUDEntity $entity, $entityName, $function) {
        $fields = $this->definition->getEditableFieldNames();
        foreach ($fields as $field) {
            if ($this->definition->getType($field) == 'file') {
                $function($entity, $entityName, $field);
            }
        }
    }

    /**
     * Adds an event to fire for the given parameters. The event function must
     * have this signature:
     * function (CRUDEntity $entity)
     * and has to return true or false.
     * The events are executed one after another in the added order as long as
     * they return "true". The first event returning "false" will stop the
     * process.
     *
     * @param string $moment
     * the "moment" of the event, can be either "before" or "after"
     * @param string $action
     * the "action" of the event, can be either "create", "update" or "delete"
     * @param anonymous function $function
     * the event function to be called if set
     */
    public function pushEvent($moment, $action, $function) {
        $events = isset($this->events[$moment.'.'.$action]) ? $this->events[$moment.'.'.$action] : array();
        $events[] = $function;
        $this->events[$moment.'.'.$action] = $events;
    }


    /**
     * Removes and returns the latest event for the given parameters.
     *
     * @param string $moment
     * the "moment" of the event, can be either "before" or "after"
     * @param string $action
     * the "action" of the event, can be either "create", "update" or "delete"
     *
     * @return anonymous function
     * the popped event or null if no event was available.
     */
    public function popEvent($moment, $action) {
        if (array_key_exists($moment.'.'.$action, $this->events)) {
            return array_pop($this->events[$moment.'.'.$action]);
        }
        return null;
    }


    /**
     * Gets the entity with the given id.
     *
     * @param string $id
     * the id
     *
     * @return CRUDEntity
     * the entity belonging to the id or null if not existant
     */
    abstract public function get($id);

    /**
     * Gets a list of entities fullfilling the given filter or all if no
     * selection was given.
     *
     * @param array $filter
     * the filter all resulting entities must fulfill, the keys as field names
     * @param array $filterOperators
     * the operators of the filter like "=" defining the full condition of the field
     * @param integer $skip
     * if given and not null, it specifies the amount of rows to skip
     * @param integer $amount
     * if given and not null, it specifies the maximum amount of rows to retrieve
     * @param string $sortField
     * if given and not null, it specifies the field to sort the entries
     * @param boolean $sortAscending
     * if given and not null, it specifies that the sort order is ascending,
     * descending else
     *
     * @return CRUDEntity[]
     * the entities fulfilling the filter or all if no filter was given
     */
    abstract public function listEntries(array $filter = array(), array $filterOperators = array(), $skip = null, $amount = null, $sortField = null, $sortAscending = null);

    /**
     * Persists the given entity as new entry in the datasource.
     *
     * @param CRUDEntity $entity
     * the entity to persist
     *
     * @return boolean
     * true on successful creation
     */
    abstract public function create(CRUDEntity $entity);

    /**
     * Updates an existing entry in the datasource having the same id.
     *
     * @param CRUDEntity $entity
     * the entity with the new data
     */
    abstract public function update(CRUDEntity $entity);

    /**
     * Deletes an entry from the datasource having the given id.
     *
     * @param CRUDEntity $entity
     * the id of the entry to delete
     *
     * @return integer
     * returns one of:
     * - CRUDData::DELETION_SUCCESS -> successful deletion
     * - CRUDData::DELETION_FAILED_STILL_REFERENCED -> failed deletion due to existing references
     * - CRUDData::DELETION_FAILED_EVENT -> failed deletion due to a failed before delete event
     */
    public function delete($entity) {
        return $this->doDelete($entity, $this->definition->isDeleteCascade());
    }

    /**
     * Gets ids and names of a table. Used for building up the dropdown box of
     * reference type fields.
     *
     * @param string $table
     * the table
     * @param string nameField
     * the field defining the name of the rows
     *
     * @return array
     * an array with the ids as key and the names as values
     */
    abstract public function getReferences($table, $nameField);

    /**
     * Retrieves the amount of entities in the datasource fulfilling the given
     * parameters.
     *
     * @param string $table
     * the table to count in
     * @param array $params
     * an array with the field names as keys and field values as values
     * @param array $paramsOperators
     * the operators of the parameters like "=" defining the full condition of the field
     * @param bool $excludeDeleted
     * false, if soft deleted entries in the datasource should be counted, too
     *
     * @return int
     * the count fulfilling the given parameters
     */
    abstract public function countBy($table, array $params, array $paramsOperators, $excludeDeleted);

    /**
     * Adds the id and name of referenced entities to the given entities. Each
     * reference field is before the raw id of the referenced entity and after
     * the fetch, it's an array with the keys id and name.
     *
     * @param CRUDEntity[] &$entities
     * the entities to fetch the references for
     */
    abstract public function fetchReferences(array &$entities = null);

    /**
     * Gets the {@see CRUDEntityDefinition} instance.
     *
     * @return CRUDEntityDefinition
     * the definition instance
     */
    public function getDefinition() {
        return $this->definition;
    }

    /**
     * Creates a new, empty entity instance having all fields prefilled with
     * null or the defined value in case of fixed fields.
     *
     * @return CRUDEntity
     * the newly created entity
     */
    public function createEmpty() {
        $entity = new CRUDEntity($this->definition);
        $fields = $this->definition->getEditableFieldNames();
        foreach ($fields as $field) {
            $value = null;
            if ($this->definition->getType($field) == 'fixed') {
                $value = $this->definition->getFixedValue($field);
            }
            $entity->set($field, $value);
        }
        $entity->set('id', null);
        return $entity;
    }

    /**
     * Creates the uploaded files of a newly created entity.
     *
     * @param Request $request
     * the HTTP request containing the file data
     * @param CRUDEntity $entity
     * the just created entity
     * @param string $entityName
     * the name of the entity as this class here is not aware of it
     */
    public function createFiles(Request $request, CRUDEntity $entity, $entityName) {
        $fileProcessor = $this->fileProcessor;
        $this->performOnFiles($entity, $entityName, function($entity, $entityName, $field) use ($fileProcessor, $request) {
            $fileProcessor->createFile($request, $entity, $entityName, $field);
        });
    }

    /**
     * Updates the uploaded files of an updated entity.
     *
     * @param Request $request
     * the HTTP request containing the file data
     * @param CRUDEntity $entity
     * the updated entity
     * @param string $entityName
     * the name of the entity as this class here is not aware of it
     */
    public function updateFiles(Request $request, CRUDEntity $entity, $entityName) {
        $fileProcessor = $this->fileProcessor;
        $this->performOnFiles($entity, $entityName, function($entity, $entityName, $field) use ($fileProcessor, $request) {
            $fileProcessor->updateFile($request, $entity, $entityName, $field);
        });
    }

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
    public function deleteFile(CRUDEntity $entity, $entityName, $field) {
        $this->fileProcessor->deleteFile($entity, $entityName, $field);
    }

    /**
     * Deletes all files of an existing entity.
     *
     * @param CRUDEntity $entity
     * the entity to delete the files from
     * @param string $entityName
     * the name of the entity as this class here is not aware of it
     */
    public function deleteFiles(CRUDEntity $entity, $entityName) {
        $fileProcessor = $this->fileProcessor;
        $this->performOnFiles($entity, $entityName, function($entity, $entityName, $field) use ($fileProcessor) {
            $fileProcessor->deleteFile($entity, $entityName, $field);
        });
    }

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
     * @return Response
     * the HTTP response, likely to be a streamed one
     */
    public function renderFile(CRUDEntity $entity, $entityName, $field) {
        return $this->fileProcessor->renderFile($entity, $entityName, $field);
    }

}
