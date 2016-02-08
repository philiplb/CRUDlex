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

use Doctrine\DBAL\Query\QueryBuilder;
use CRUDlex\CRUDEntity;
use CRUDlex\CRUDData;
use CRUDlex\CRUDFileProcessorInterface;

/**
 * MySQL CRUDData implementation using a given Doctrine DBAL instance.
 */
class CRUDMySQLData extends CRUDData {

    /**
     * Holds the Doctrine DBAL instance.
     */
    protected $db;

    /**
     * Sets the values and parameters of the upcoming given query according
     * to the entity.
     *
     * @param CRUDEntity $entity
     * the entity with its fields and values
     * @param QueryBuilder $queryBuilder
     * the upcoming query
     * @param boolean $setValue
     * whether to use QueryBuilder::setValue (true) or QueryBuilder::set (false)
     */
    protected function setValuesAndParameters(CRUDEntity $entity, QueryBuilder $queryBuilder, $setValue) {
        $formFields = $this->definition->getEditableFieldNames();
        $count = count($formFields);
        for ($i = 0; $i < $count; ++$i) {
            $value = $entity->get($formFields[$i]);
            $type = $this->definition->getType($formFields[$i]);
            if ($type == 'bool') {
                $value = $value ? 1 : 0;
            }
            if ($type == 'date' || $type == 'datetime' || $type == 'reference') {
                $value = $value == '' ? null : $value;
            }
            if ($setValue) {
                $queryBuilder->setValue('`'.$formFields[$i].'`', '?');
            } else {
                $queryBuilder->set('`'.$formFields[$i].'`', '?');
            }
            $queryBuilder->setParameter($i, $value);
        }
    }

    /**
     * Performs the cascading children deletion.
     *
     * @param integer $id
     * the current entities id
     * @param boolean $deleteCascade
     * whether to delete children and subchildren
     */
    protected function deleteChildren($id, $deleteCascade) {
        foreach ($this->definition->getChildren() as $childArray) {
            $childData = $this->definition->getServiceProvider()->getData($childArray[2]);
            $children = $childData->listEntries(array($childArray[1] => $id));
            foreach ($children as $child) {
                $childData->doDelete($child, $deleteCascade);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete(CRUDEntity $entity, $deleteCascade) {
        $result = $this->executeEvents($entity, 'before', 'delete');
        if (!$result) {
            return static::DELETION_FAILED_EVENT;
        }
        $id = $entity->get('id');
        if ($deleteCascade) {
            $this->deleteChildren($id, $deleteCascade);
        } else {
            foreach ($this->definition->getChildren() as $child) {
                $queryBuilder = $this->db->createQueryBuilder();
                $queryBuilder
                    ->select('COUNT(id)')
                    ->from($child[0], $child[0])
                    ->where($child[1].' = ?')
                    ->andWhere('deleted_at IS NULL')
                    ->setParameter(0, $id);
                $queryResult = $queryBuilder->execute();
                $result = $queryResult->fetch(\PDO::FETCH_NUM);
                if ($result[0] > 0) {
                    return static::DELETION_FAILED_STILL_REFERENCED;
                }
            }
        }

        $query = $this->db->createQueryBuilder();
        $query
            ->update($this->definition->getTable())
            ->set('deleted_at', 'NOW()')
            ->where('id = ?')
            ->setParameter(0, $id);

        $query->execute();
        $this->executeEvents($entity, 'after', 'delete');
        return static::DELETION_SUCCESS;
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
        $i = 0;
        foreach ($filter as $field => $value) {
            if ($value === null) {
                $queryBuilder->andWhere('`'.$field.'` IS NULL');
            } else {
                $operator = array_key_exists($field, $filterOperators) ? $filterOperators[$field] : '=';
                $queryBuilder
                    ->andWhere('`'.$field.'` '.$operator.' ?')
                    ->setParameter($i, $value);
            }
            $i++;
        }
    }

    /**
     * Adds pagination parameters to the query.
     *
     * @param QueryBuilder $queryBuilder
     * the query
     * @param $skip
     * the rows to skip
     * @param $amount
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
            $order = $sortAscending === true ? 'ASC' : 'DESC';
            $queryBuilder->orderBy($sortField, $order);
        }
    }


    /**
     * Adds the id and name of referenced entities to the given entities. The
     * reference field is before the raw id of the referenced entity and after
     * the fetch, it's an array with the keys id and name.
     *
     * @param CRUDEntity[] &$entities
     * the entities to fetch the references for
     * @param string $field
     * the reference field
     */
    protected function fetchReferencesForField(array &$entities, $field) {
        $nameField = $this->definition->getReferenceNameField($field);
        $queryBuilder = $this->db->createQueryBuilder();

        $ids = array();
        $amount = count($entities);
        for ($i = 0; $i < $amount; ++$i) {
            $ids[] = $entities[$i]->get($field);
        }

        $table = $this->definition->getReferenceTable($field);
        $queryBuilder
            ->from($table, $table)
            ->where('id IN (?)')
            ->andWhere('deleted_at IS NULL');
        if ($nameField) {
            $queryBuilder->select('id', $nameField);
        } else {
            $queryBuilder->select('id');
        }

        $queryBuilder->setParameter(0, $ids, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY);

        $queryResult = $queryBuilder->execute();
        $rows = $queryResult->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            for ($i = 0; $i < $amount; ++$i) {
                if ($entities[$i]->get($field) == $row['id']) {
                    $value = array('id' => $entities[$i]->get($field));
                    if ($nameField) {
                        $value['name'] = $row[$nameField];
                    }
                    $entities[$i]->set($field, $value);
                }
            }
        }
    }

    /**
     * Constructor.
     *
     * @param CRUDEntityDefinition $definition
     * the entity definition
     * @param CRUDFileProcessorInterface $fileProcessor
     * the file processor to use
     * @param $db
     * the Doctrine DBAL instance to use
     */
    public function __construct(CRUDEntityDefinition $definition, CRUDFileProcessorInterface $fileProcessor, $db) {
        $this->definition = $definition;
        $this->fileProcessor = $fileProcessor;
        $this->db = $db;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id) {
        $entities = $this->listEntries(array('id' => $id));
        if (count($entities) == 0) {
            return null;
        }
        return $entities[0];
    }

    /**
     * {@inheritdoc}
     */
    public function listEntries(array $filter = array(), array $filterOperators = array(), $skip = null, $amount = null, $sortField = null, $sortAscending = null) {
        $fieldNames = $this->definition->getFieldNames();

        $queryBuilder = $this->db->createQueryBuilder();
        $table = $this->definition->getTable();
        $queryBuilder
            ->select('`'.implode('`,`', $fieldNames).'`')
            ->from($table, $table)
            ->where('deleted_at IS NULL');

        $this->addFilter($queryBuilder, $filter, $filterOperators);
        $this->addPagination($queryBuilder, $skip, $amount);
        $this->addSort($queryBuilder, $sortField, $sortAscending);

        $queryResult = $queryBuilder->execute();
        $rows = $queryResult->fetchAll(\PDO::FETCH_ASSOC);
        $entities = array();
        foreach ($rows as $row) {
            $entities[] = $this->hydrate($row);
        }
        return $entities;
    }

    /**
     * {@inheritdoc}
     */
    public function create(CRUDEntity $entity) {

        $result = $this->executeEvents($entity, 'before', 'create');
        if (!$result) {
            return false;
        }

        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->insert($this->definition->getTable())
            ->setValue('created_at', 'NOW()')
            ->setValue('updated_at', 'NOW()')
            ->setValue('version', 0);

        $this->setValuesAndParameters($entity, $queryBuilder, true);
        $queryBuilder->execute();
        $entity->set('id', $this->db->lastInsertId());

        $createdEntity = $this->get($entity->get('id'));
        $entity->set('version', $createdEntity->get('version'));
        $entity->set('created_at', $createdEntity->get('created_at'));
        $entity->set('updated_at', $createdEntity->get('updated_at'));

        $this->executeEvents($entity, 'after', 'create');

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function update(CRUDEntity $entity) {

        $result = $this->executeEvents($entity, 'before', 'update');
        if (!$result) {
            return false;
        }

        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->update($this->definition->getTable())
            ->set('updated_at', 'NOW()')
            ->set('version', 'version + 1');

        $formFields = $this->definition->getEditableFieldNames();
        $this->setValuesAndParameters($entity, $queryBuilder, false);
        $affected = $queryBuilder
            ->where('id = ?')
            ->setParameter(count($formFields), $entity->get('id'))
            ->execute();

        $this->executeEvents($entity, 'after', 'update');

        return $affected;
    }

    /**
     * {@inheritdoc}
     */
    public function getReferences($table, $nameField) {

        $queryBuilder = $this->db->createQueryBuilder();
        if ($nameField) {
            $queryBuilder->select('id', $nameField);
        } else {
            $queryBuilder->select('id');
        }
        $queryBuilder->from($table, $table)->where('deleted_at IS NULL');
        if ($nameField) {
            $queryBuilder->orderBy($nameField);
        } else {
            $queryBuilder->orderBy('id');
        }
        $queryResult = $queryBuilder->execute();
        $entries = $queryResult->fetchAll(\PDO::FETCH_ASSOC);
        $result = array();
        foreach ($entries as $entry) {
            $result[$entry['id']] = $nameField ? $entry[$nameField] : $entry['id'];
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function countBy($table, array $params, array $paramsOperators, $excludeDeleted) {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->select('COUNT(id)')
            ->from($table, $table);

        if (count($params) > 0) {
            $i = 0;
            foreach ($params as $name => $value) {
                $queryBuilder
                    ->andWhere('`'.$name.'`'.$paramsOperators[$name].'?')
                    ->setParameter($i, $value);
                $i++;
            }
            if ($excludeDeleted) {
                $queryBuilder->andWhere('deleted_at IS NULL');
            }
        } else {
            if ($excludeDeleted) {
                $queryBuilder->where('deleted_at IS NULL');
            }
        }

        $queryResult = $queryBuilder->execute();
        $result = $queryResult->fetch(\PDO::FETCH_NUM);
        return intval($result[0]);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchReferences(array &$entities = null) {
        if (!$entities) {
            return;
        }
        foreach ($this->definition->getFieldNames() as $field) {
            if ($this->definition->getType($field) !== 'reference') {
                continue;
            }
            $this->fetchReferencesForField($entities, $field);
        }
    }

}
