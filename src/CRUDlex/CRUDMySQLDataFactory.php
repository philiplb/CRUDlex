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

use CRUDlex\CRUDDataFactoryInterface;
use CRUDlex\CRUDMySQLData;

/**
 * A factory implementation for {@see CRUDMySQLData} instances.
 */
class CRUDMySQLDataFactory implements CRUDDataFactoryInterface {

    /**
     * Holds the Doctrine DBAL instance.
     */
    protected $db;

    /**
     * Constructor.
     *
     * @param $db
     * the Doctrine DBAL instance
     */
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * {@inheritdoc}
     */
    public function createData(CRUDEntityDefinition $definition, CRUDFileProcessorInterface $fileProcessor) {
        return new CRUDMySQLData($definition, $fileProcessor, $this->db);
    }

}
