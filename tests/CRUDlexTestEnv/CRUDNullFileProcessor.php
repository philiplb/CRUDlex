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

use CRUDlex\CRUDFileProcessorInterface;
use CRUDlex\CRUDEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CRUDNullFileProcessor implements CRUDFileProcessorInterface {

    private $createFileCalled;

    private $updateFileCalled;

    private $deleteFileCalled;

    private $renderFileCalled;

    public function __construct() {
        $this->reset();
    }

    public function reset() {
        $this->createFileCalled = false;
        $this->updateFileCalled = false;
        $this->deleteFileCalled = false;
        $this->renderFileCalled = false;
    }

    public function createFile(Request $request, CRUDEntity $entity, $entityName, $field) {
        $this->createFileCalled = true;
    }

    public function updateFile(Request $request, CRUDEntity $entity, $entityName, $field) {
        $this->updateFileCalled = true;
    }

    public function deleteFile(CRUDEntity $entity, $entityName, $field) {
        $this->deleteFileCalled = true;
    }

    public function renderFile(CRUDEntity $entity, $entityName, $field) {
        $this->renderFileCalled = true;
        return 'rendered file';
    }

    public function isCreateFileCalled() {
        return $this->createFileCalled;
    }

    public function isUpdateFileCalled() {
        return $this->updateFileCalled;
    }

    public function isDeleteFileCalled() {
        return $this->deleteFileCalled;
    }

    public function isRenderFileCalled() {
        return $this->renderFileCalled;
    }
}
