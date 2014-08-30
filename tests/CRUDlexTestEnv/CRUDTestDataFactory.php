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

use CRUDlex\CRUDEntityDefinition;
use CRUDlex\CRUDDataFactoryInterface;
use CRUDlexTestEnv\CRUDTestData;

class CRUDTestDataFactory implements CRUDDataFactoryInterface {

    public function createData(CRUDEntityDefinition $definition) {
        return new CRUDTestData($definition);
    }

}
