<?php

/*
 * This file is part of the CRUDlex package.
 *
 * (c) Philip Lehmann-Böhm <philip@philiplb.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace CRUDlexTestEnv;

use CRUDlex\CRUDEntity;
use CRUDlex\CRUDData;
use CRUDlex\CRUDEntityDefinition;

class CRUDTestData extends CRUDData {

    public function __construct(CRUDEntityDefinition $definition) {
        $this->definition = $definition;
    }

    public function get($id) {

    }

    public function listEntries(){

    }

    public function create(CRUDEntity $entity){

    }

    public function update(CRUDEntity $entity){

    }

    public function delete($id){

    }

    public function getReferences($table, $nameField){

    }

    public function countBy($table, $params, $paramsOperators, $includeDeleted){

    }

    public function fetchReferences($entity){

    }
}
