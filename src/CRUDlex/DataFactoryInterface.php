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

use CRUDlex\AbstractData;
use CRUDlex\EntityDefinition;
use CRUDlex\FileProcessorInterface;

/**
 * An interface used by the {@see ServiceProvider} to construct
 * {@see Data} instances. By implementing this and handing it into
 * the service provider, the user can control what database (-variation) he
 * wants to use.
 */
interface DataFactoryInterface {

    /**
     * Creates instances.
     *
     * @param EntityDefinition $definition
     * the definition of the entities managed by the to be created instance
     * @param FileProcessorInterface $fileProcessor
     * the file processor managing uploaded files
     *
     * @return AbstractData
     * the newly created instance
     */
    public function createData(EntityDefinition $definition, FileProcessorInterface $fileProcessor);

}
