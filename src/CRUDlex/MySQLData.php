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

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * MySQL Data implementation using a given Doctrine DBAL instance.
 */
class MySQLData extends AbstractData {

    /**
     * Holds the Doctrine DBAL instance.
     */
    protected $database;

    /**
     * Flag whether to use UUIDs as primary key.
     */
    protected $useUUIDs;

    /**
     * Sets the values and parameters of the upcoming given query according
     * to the entity.
     *
     * @param Entity $entity
     * the entity with its fields and values
     * @param QueryBuilder $queryBuilder
     * the upcoming query
     * @param string $setMethod
     * what method to use on the QueryBuilder: 'setValue' or 'set'
     */
    protected function setValuesAndParameters(Entity $entity, QueryBuilder $queryBuilder, $setMethod) {
        $formFields = $this->getFormFields();
        $count      = count($formFields);
        for ($i = 0; $i < $count; ++$i) {
            $type  = $this->definition->getType($formFields[$i]);
            $value = $entity->get($formFields[$i]);
            if ($type == 'boolean') {
                $value = $value ? 1 : 0;
            }
            if ($type == 'reference' && is_array($value)) {
                $value = $value['id'];
            }
            $queryBuilder->$setMethod('`'.$formFields[$i].'`', '?');
            $queryBuilder->setParameter($i, $value);
        }
    }

    /**
     * Checks whether the by id given entity still has children referencing it.
     *
     * @param integer $id
     * the current entities id
     *
     * @return boolean
     * true if the entity still has children
     */
    protected function hasChildren($id) {
        foreach ($this->definition->getChildren() as $child) {
            $queryBuilder = $this->database->createQueryBuilder();
            $queryBuilder
                ->select('COUNT(id)')
                ->from('`'.$child[0].'`', '`'.$child[0].'`')
                ->where('`'.$child[1].'` = ?')
                ->andWhere('deleted_at IS NULL')
                ->setParameter(0, $id);
            $queryResult = $queryBuilder->execute();
            $result      = $queryResult->fetch(\PDO::FETCH_NUM);
            if ($result[0] > 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete(Entity $entity, $deleteCascade) {
        $result = $this->shouldExecuteEvents($entity, 'before', 'delete');
        if (!$result) {
            return static::DELETION_FAILED_EVENT;
        }
        $id = $entity->get('id');
        if ($deleteCascade) {
            $this->deleteChildren($id, $deleteCascade);
        } elseif ($this->hasChildren($id)) {
            return static::DELETION_FAILED_STILL_REFERENCED;
        }

        $query = $this->database->createQueryBuilder();
        $query
            ->update('`'.$this->definition->getTable().'`')
            ->set('deleted_at', 'UTC_TIMESTAMP()')
            ->where('id = ?')
            ->setParameter(0, $id);

        $query->execute();
        $this->shouldExecuteEvents($entity, 'after', 'delete');
        return static::DELETION_SUCCESS;
    }

    /**
     * Gets all possible many-to-many ids existing for this definition.
     *
     * @param array $fields
     * the many field names to fetch for
     * @param $params
     * the parameters the possible many field values to fetch for
     * @return array
     * an array of this many-to-many ids
     */
    protected function getManyIds(array $fields, array $params) {
        $manyIds = [];
        foreach ($fields as $field) {
            $thisField    = $this->definition->getSubTypeField($field, 'many', 'thisField');
            $thatField    = $this->definition->getSubTypeField($field, 'many', 'thatField');
            $queryBuilder = $this->database->createQueryBuilder();
            $queryBuilder
                ->select('`'.$thisField.'`')
                ->from($field)
                ->where('`'.$thatField.'` IN (?)')
                ->setParameter(0, array_column($params[$field], 'id'), Connection::PARAM_STR_ARRAY)
                ->groupBy('`'.$thisField.'`')
            ;
            $queryResult = $queryBuilder->execute();
            $manyResults = $queryResult->fetchAll(\PDO::FETCH_ASSOC);
            $manyIds     = array_merge($manyIds, array_column($manyResults, $thisField));

        }
        return $manyIds;
    }

    /**
     * Adds sorting parameters to the query.
     *
     * @param QueryBuilder $queryBuilder
     * the query
     * @param $filter
     * the filter all resulting entities must fulfill, the keys as field names
     * @param $filterOperators
     * the operators of the filter like "=" defining the full condition of the field
     */
    protected function addFilter(QueryBuilder $queryBuilder, array $filter, array $filterOperators) {
        $i          = 0;
        $manyFields = [];
        foreach ($filter as $field => $value) {
            if ($this->definition->getType($field) === 'many') {
                $manyFields[] = $field;
                continue;
            }
            if ($value === null) {
                $queryBuilder->andWhere('`'.$field.'` IS NULL');
            } else {
                $operator = array_key_exists($field, $filterOperators) ? $filterOperators[$field] : '=';
                $queryBuilder
                    ->andWhere('`'.$field.'` '.$operator.' ?')
                    ->setParameter($i, $value, \PDO::PARAM_STR);
            }
            $i++;
        }
        $idsToInclude = $this->getManyIds($manyFields, $filter);
        if (!empty($idsToInclude)) {
            $queryBuilder
                ->andWhere('id IN (?)')
                ->setParameter($i, $idsToInclude, Connection::PARAM_STR_ARRAY)
            ;
        }
    }

    /**
     * Adds pagination parameters to the query.
     *
     * @param QueryBuilder $queryBuilder
     * the query
     * @param integer|null $skip
     * the rows to skip
     * @param integer|null $amount
     * the maximum amount of rows
     */
    protected function addPagination(QueryBuilder $queryBuilder, $skip, $amount) {
        $queryBuilder->setMaxResults(9999999999);
        if ($amount !== null) {
            $queryBuilder->setMaxResults(abs(intval($amount)));
        }
        if ($skip !== null) {
            $queryBuilder->setFirstResult(abs(intval($skip)));
        }
    }

    /**
     * Adds sorting parameters to the query.
     *
     * @param QueryBuilder $queryBuilder
     * the query
     * @param string|null $sortField
     * the sort field
     * @param boolean|null $sortAscending
     * true if sort ascending, false if descending
     */
    protected function addSort(QueryBuilder $queryBuilder, $sortField, $sortAscending) {
        if ($sortField !== null) {

            $type = $this->definition->getType($sortField);
            if ($type === 'many') {
                $sortField = $this->definition->getInitialSortField();
            }

            $order = $sortAscending === true ? 'ASC' : 'DESC';
            $queryBuilder->orderBy('`'.$sortField.'`', $order);
        }
    }

    /**
     * Adds the id and name of referenced entities to the given entities. The
     * reference field is before the raw id of the referenced entity and after
     * the fetch, it's an array with the keys id and name.
     *
     * @param Entity[] &$entities
     * the entities to fetch the references for
     * @param string $field
     * the reference field
     */
    protected function fetchReferencesForField(array &$entities, $field) {
        $nameField    = $this->definition->getSubTypeField($field, 'reference', 'nameField');
        $queryBuilder = $this->database->createQueryBuilder();

        $ids = $this->getReferenceIds($entities, $field);

        $referenceEntity = $this->definition->getSubTypeField($field, 'reference', 'entity');
        $table           = $this->definition->getServiceProvider()->getData($referenceEntity)->getDefinition()->getTable();
        $queryBuilder
            ->from('`'.$table.'`', '`'.$table.'`')
            ->where('id IN (?)')
            ->andWhere('deleted_at IS NULL');
        if ($nameField) {
            $queryBuilder->select('id', $nameField);
        } else {
            $queryBuilder->select('id');
        }

        $queryBuilder->setParameter(0, $ids, Connection::PARAM_STR_ARRAY);

        $queryResult = $queryBuilder->execute();
        $rows        = $queryResult->fetchAll(\PDO::FETCH_ASSOC);
        $amount      = count($entities);
        foreach ($rows as $row) {
            for ($i = 0; $i < $amount; ++$i) {
                if ($entities[$i]->get($field) == $row['id']) {
                    $value = ['id' => $entities[$i]->get($field)];
                    if ($nameField) {
                        $value['name'] = $row[$nameField];
                    }
                    $entities[$i]->set($field, $value);
                }
            }
        }
    }

    /**
     * Generates a new UUID.
     *
     * @return string|null
     * the new UUID or null if this instance isn't configured to do so
     */
    protected function generateUUID() {
        $uuid = null;
        if ($this->useUUIDs) {
            $sql    = 'SELECT UUID() as id';
            $result = $this->database->fetchAssoc($sql);
            $uuid   = $result['id'];
        }
        return $uuid;
    }

    /**
     * Enriches the given mapping of entity id to raw entity data with some many-to-many data.
     *
     * @param array $idToData
     * a reference to the map entity id to raw entity data
     * @param $manyField
     * the many field to enrich data with
     */
    protected function enrichWithManyField(&$idToData, $manyField) {
        $queryBuilder = $this->database->createQueryBuilder();
        $nameField    = $this->definition->getSubTypeField($manyField, 'many', 'nameField');
        $thisField    = $this->definition->getSubTypeField($manyField, 'many', 'thisField');
        $thatField    = $this->definition->getSubTypeField($manyField, 'many', 'thatField');
        $entity       = $this->definition->getSubTypeField($manyField, 'many', 'entity');
        $entityTable  = $this->definition->getServiceProvider()->getData($entity)->getDefinition()->getTable();
        $nameSelect   = $nameField !== null ? ', t2.`'.$nameField.'` AS name' : '';
        $queryBuilder
            ->select('t1.`'.$thisField.'` AS this, t1.`'.$thatField.'` AS id'.$nameSelect)
            ->from('`'.$manyField.'`', 't1')
            ->leftJoin('t1', '`'.$entityTable.'`', 't2', 't2.id = t1.`'.$thatField.'`')
            ->where('t1.`'.$thisField.'` IN (?)')
            ->andWhere('t2.deleted_at IS NULL');
        $queryBuilder->setParameter(0, array_keys($idToData), Connection::PARAM_STR_ARRAY);
        $queryResult    = $queryBuilder->execute();
        $manyReferences = $queryResult->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($manyReferences as $manyReference) {
            $entityId = $manyReference['this'];
            unset($manyReference['this']);
            $idToData[$entityId][$manyField][] = $manyReference;
        }
    }

    /**
     * Fetches to the rows belonging many-to-many entries and adds them to the rows.
     *
     * @param array $rows
     * the rows to enrich
     * @return array
     * the enriched rows
     */
    protected function enrichWithMany(array $rows) {
        $manyFields = $this->getManyFields();
        $idToData   = [];
        foreach ($rows as $row) {
            foreach ($manyFields as $manyField) {
                $row[$manyField] = [];
            }
            $idToData[$row['id']] = $row;
        }
        foreach ($manyFields as $manyField) {
            $this->enrichWithManyField($idToData, $manyField);
        }
        return array_values($idToData);
    }

    /**
     * First, deletes all to the given entity related many-to-many entries from the DB
     * and then writes them again.
     *
     * @param Entity $entity
     * the entity to save the many-to-many entries of
     */
    protected function saveMany(Entity $entity) {
        $manyFields = $this->getManyFields();
        $id         = $entity->get('id');
        foreach ($manyFields as $manyField) {
            $thisField = $this->definition->getSubTypeField($manyField, 'many', 'thisField');
            $thatField = $this->definition->getSubTypeField($manyField, 'many', 'thatField');
            $this->database->delete($manyField, [$thisField => $id]);
            $manyValues = $entity->get($manyField) ?: [];
            foreach ($manyValues as $thatId) {
                $this->database->insert($manyField, [
                    $thisField => $id,
                    $thatField => $thatId['id']
                ]);
            }
        }
    }

    /**
     * Adds the id and name of referenced entities to the given entities. Each
     * reference field is before the raw id of the referenced entity and after
     * the fetch, it's an array with the keys id and name.
     *
     * @param Entity[] &$entities
     * the entities to fetch the references for
     *
     * @return void
     */
    protected function enrichWithReference(array &$entities) {
        if (empty($entities)) {
            return;
        }
        foreach ($this->definition->getFieldNames() as $field) {
            if ($this->definition->getType($field) !== 'reference') {
                continue;
            }
            $this->fetchReferencesForField($entities, $field);
        }
    }

    /**
     * Constructor.
     *
     * @param EntityDefinition $definition
     * the entity definition
     * @param FileProcessorInterface $fileProcessor
     * the file processor to use
     * @param $database
     * the Doctrine DBAL instance to use
     * @param boolean $useUUIDs
     * flag whether to use UUIDs as primary key
     */
    public function __construct(EntityDefinition $definition, FileProcessorInterface $fileProcessor, $database, $useUUIDs) {
        $this->definition    = $definition;
        $this->fileProcessor = $fileProcessor;
        $this->database      = $database;
        $this->useUUIDs      = $useUUIDs;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id) {
        $entities = $this->listEntries(['id' => $id]);
        if (count($entities) == 0) {
            return null;
        }
        return $entities[0];
    }

    /**
     * {@inheritdoc}
     */
    public function listEntries(array $filter = [], array $filterOperators = [], $skip = null, $amount = null, $sortField = null, $sortAscending = null) {
        $fieldNames = $this->definition->getFieldNames();

        $queryBuilder = $this->database->createQueryBuilder();
        $table        = $this->definition->getTable();
        $queryBuilder
            ->select('`'.implode('`,`', $fieldNames).'`')
            ->from('`'.$table.'`', '`'.$table.'`')
            ->where('deleted_at IS NULL');

        $this->addFilter($queryBuilder, $filter, $filterOperators);
        $this->addPagination($queryBuilder, $skip, $amount);
        $this->addSort($queryBuilder, $sortField, $sortAscending);

        $queryResult = $queryBuilder->execute();
        $rows        = $queryResult->fetchAll(\PDO::FETCH_ASSOC);
        $rows        = $this->enrichWithMany($rows);
        $entities    = [];
        foreach ($rows as $row) {
            $entities[] = $this->hydrate($row);
        }
        $this->enrichWithReference($entities);
        return $entities;
    }

    /**
     * {@inheritdoc}
     */
    public function create(Entity $entity) {

        $result = $this->shouldExecuteEvents($entity, 'before', 'create');
        if (!$result) {
            return false;
        }

        $queryBuilder = $this->database->createQueryBuilder();
        $queryBuilder
            ->insert('`'.$this->definition->getTable().'`')
            ->setValue('created_at', 'UTC_TIMESTAMP()')
            ->setValue('updated_at', 'UTC_TIMESTAMP()')
            ->setValue('version', 0);


        $this->setValuesAndParameters($entity, $queryBuilder, 'setValue');

        $id = $this->generateUUID();
        if ($this->useUUIDs) {
            $queryBuilder->setValue('`id`', '?');
            $uuidI = count($this->getFormFields());
            $queryBuilder->setParameter($uuidI, $id);
        }

        $queryBuilder->execute();

        if (!$this->useUUIDs) {
            $id = $this->database->lastInsertId();
        }

        $this->enrichEntityWithMetaData($id, $entity);
        $this->saveMany($entity);
        $entities = [$entity];
        $this->enrichWithReference($entities);

        $this->shouldExecuteEvents($entity, 'after', 'create');

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function update(Entity $entity) {

        if (!$this->shouldExecuteEvents($entity, 'before', 'update')) {
            return false;
        }

        $queryBuilder = $this->database->createQueryBuilder();
        $queryBuilder->update('`'.$this->definition->getTable().'`')
            ->set('updated_at', 'UTC_TIMESTAMP()')
            ->set('version', 'version + 1')
            ->where('id = ?')
            ->setParameter(count($this->getFormFields()), $entity->get('id'));

        $this->setValuesAndParameters($entity, $queryBuilder, 'set');
        $affected = $queryBuilder->execute();

        $this->saveMany($entity);
        $entities = [$entity];
        $this->enrichWithReference($entities);

        $this->shouldExecuteEvents($entity, 'after', 'update');

        return $affected;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdToNameMap($entity, $nameField) {
        $nameSelect   = $nameField !== null ? ',`'.$nameField.'`' : '';
        $drivingField = $nameField ?: 'id';

        $table        = $this->definition->getServiceProvider()->getData($entity)->getDefinition()->getTable();
        $queryBuilder = $this->database->createQueryBuilder();
        $queryBuilder
            ->select('id'.$nameSelect)
            ->from('`'.$table.'`', 't1')
            ->where('deleted_at IS NULL')
            ->orderBy($drivingField)
        ;
        $queryResult    = $queryBuilder->execute();
        $manyReferences = $queryResult->fetchAll(\PDO::FETCH_ASSOC);
        $result         = array_reduce($manyReferences, function(&$carry, $manyReference) use ($drivingField) {
            $carry[$manyReference['id']] = $manyReference[$drivingField];
            return $carry;
        }, []);
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function countBy($table, array $params, array $paramsOperators, $excludeDeleted) {
        $queryBuilder = $this->database->createQueryBuilder();
        $queryBuilder
            ->select('COUNT(id)')
            ->from('`'.$table.'`', '`'.$table.'`')
        ;

        $deletedExcluder = 'where';
        $i               = 0;
        $manyFields      = [];
        foreach ($params as $name => $value) {
            if ($this->definition->getType($name) === 'many') {
                $manyFields[] = $name;
                continue;
            }
            $queryBuilder
                ->andWhere('`'.$name.'` '.$paramsOperators[$name].' ?')
                ->setParameter($i, $value, \PDO::PARAM_STR)
            ;
            $i++;
            $deletedExcluder = 'andWhere';
        }

        $idsToInclude = $this->getManyIds($manyFields, $params);
        if (!empty($idsToInclude)) {
            $queryBuilder
                ->andWhere('id IN (?)')
                ->setParameter($i, $idsToInclude, Connection::PARAM_STR_ARRAY)
            ;
            $deletedExcluder = 'andWhere';
        }

        if ($excludeDeleted) {
            $queryBuilder->$deletedExcluder('deleted_at IS NULL');
        }

        $queryResult = $queryBuilder->execute();
        $result      = $queryResult->fetch(\PDO::FETCH_NUM);
        return intval($result[0]);
    }

    /**
     * {@inheritdoc}
     */
    public function hasManySet($field, array $thatIds, $excludeId = null) {
        $thisField    = $this->definition->getSubTypeField($field, 'many', 'thisField');
        $thatField    = $this->definition->getSubTypeField($field, 'many', 'thatField');
        $thatEntity   = $this->definition->getSubTypeField($field, 'many', 'entity');
        $entityTable  = $this->definition->getServiceProvider()->getData($thatEntity)->getDefinition()->getTable();
        $queryBuilder = $this->database->createQueryBuilder();
        $queryBuilder->select('t1.`'.$thisField.'` AS this, t1.`'.$thatField.'` AS that')
            ->from('`'.$field.'`', 't1')
            ->leftJoin('t1', '`'.$entityTable.'`', 't2', 't2.id = t1.`'.$thatField.'`')
            ->where('t2.deleted_at IS NULL')
            ->orderBy('this, that');
        if ($excludeId !== null) {
            $queryBuilder->andWhere('t1.`'.$thisField.'` != ?')->setParameter(0, $excludeId);
        }
        $existingMany = $queryBuilder->execute()->fetchAll(\PDO::FETCH_ASSOC);
        $existingMap  = array_reduce($existingMany, function(&$carry, $existing) {
            $carry[$existing['this']][] = $existing['that'];
            return $carry;
        }, []);
        sort($thatIds);
        return in_array($thatIds, array_values($existingMap));
    }

}
