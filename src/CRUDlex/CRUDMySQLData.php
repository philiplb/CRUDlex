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
     * Performs the actual deletion.
     *
     * @param string $id
     * the id of the entry to delete
     *
     * @param boolean $deleteCascade
     * whether to delete children and subchildren
     *
     * @return boolean
     * true on successful deletion
     */
    protected function doDelete($id, $deleteCascade) {
        if ($deleteCascade) {
            foreach ($this->definition->getChildren() as $childArray) {
                $childData = $this->definition->getServiceProvider()->getData($childArray[2]);
                $children = $childData->listEntries(array($childArray[1] => $id));
                foreach ($children as $child) {
                    $childData->doDelete($child->get('id'), $deleteCascade);
                }
            }
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
                    return false;
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
        return true;
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
    public function listEntries(array $filter = array(), array $filterOperators = array(), $skip = null, $amount = null) {
        $fieldNames = $this->definition->getFieldNames();

        $queryBuilder = $this->db->createQueryBuilder();
        $table = $this->definition->getTable();
        $queryBuilder
            ->select('`'.implode('`,`', $fieldNames).'`')
            ->from($table, $table)
            ->where('deleted_at IS NULL');

        $i = 0;
        foreach ($filter as $field => $value) {
            if ($value === null) {
                $queryBuilder->andWhere('`'.$field.'` IS NULL');
            } else {
                $operator = key_exists($field, $filterOperators) ? $filterOperators[$field] : '=';
                $queryBuilder
                    ->andWhere('`'.$field.'` '.$operator.' ?')
                    ->setParameter($i, $value);
            }
            $i++;
        }

        $queryBuilder->setMaxResults(9999999999);
        if ($amount !== null) {
            $queryBuilder->setMaxResults(abs(intval($amount)));
        }
        if ($skip !== null) {
            $queryBuilder->setFirstResult(abs(intval($skip)));
        }

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
        $formFields = $this->definition->getEditableFieldNames();

        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->insert($this->definition->getTable())
            ->setValue('created_at', 'NOW()')
            ->setValue('updated_at', 'NOW()')
            ->setValue('version', 0);

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
            $queryBuilder
                ->setValue('`'.$formFields[$i].'`', '?')
                ->setParameter($i, $value);
        }
        $queryBuilder->execute();
        $entity->set('id', $this->db->lastInsertId());
    }

    /**
     * {@inheritdoc}
     */
    public function update(CRUDEntity $entity) {

        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->update($this->definition->getTable())
            ->set('updated_at', 'NOW()');

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
            $queryBuilder
                ->set('`'.$formFields[$i].'`', '?')
                ->setParameter($i, $value);
        }

        $affected = $queryBuilder
            ->where('id = ?')
            ->setParameter(count($formFields), $entity->get('id'))
            ->execute();

        return $affected;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id) {
        return $this->doDelete($id, $this->definition->isDeleteCascade());
    }

    /**
     * {@inheritdoc}
     */
    public function getReferences($table, $nameField) {

        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->select('id', $nameField)
            ->from($table, $table)
            ->where('deleted_at IS NULL')
            ->orderBy($nameField);

        $queryResult = $queryBuilder->execute();
        $entries = $queryResult->fetchAll(\PDO::FETCH_ASSOC);
        $result = array();
        foreach ($entries as $entry) {
            $result[$entry['id']] = $entry[$nameField];
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
            foreach($params as $name => $value) {
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
            $nameField = $this->definition->getReferenceNameField($field);
            $queryBuilder = $this->db->createQueryBuilder();

            $in = '?';
            $amount = count($entities);
            $ids = array($entities[0]->get($field));
            for ($i = 1; $i < $amount; ++$i) {
                $in .= ',?';
                $ids[] = $entities[$i]->get($field);
            }
            $table = $this->definition->getReferenceTable($field);
            $queryBuilder
                ->select('id', $nameField)
                ->from($table, $table)
                ->where('id IN ('.$in.')')
                ->andWhere('deleted_at IS NULL');
            $count = count($ids);
            for ($i = 0; $i < $count; ++$i) {
                $queryBuilder->setParameter($i, $ids[$i]);
            }

            $queryResult = $queryBuilder->execute();
            $rows = $queryResult->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                for ($i = 0; $i < $amount; ++$i) {
                    if ($entities[$i]->get($field) == $row['id']) {
                        $entities[$i]->set($field,
                            array('id' => $entities[$i]->get($field), 'name' => $row[$nameField]));
                    }
                }
            }
        }
    }

}
