<?php

/*
 * This file is part of the Crudlex package.
 *
 * (c) Philip Lehmann-Böhm <philip@philiplb.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CRUDlex;

use CRUDlex\CRUDData;
use CRUDlex\CRUDEntityDefinition;

interface CRUDDataFactoryInterface {

    public function createData(CRUDEntityDefinition $definition);

}
