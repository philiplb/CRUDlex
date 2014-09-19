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
use Symfony\Component\HttpFoundation\Request;

interface CRUDFileProcessorInterface {

    public function createFile(Request $request, CRUDEntity $entity, $entityName, $field);

    public function updateFile(Request $request, CRUDEntity $entity, $entityName, $field);

    public function deleteFile(CRUDEntity $entity, $entityName, $field);
}
