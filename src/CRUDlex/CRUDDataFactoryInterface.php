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

use CRUDlex\CRUDData;
use CRUDlex\CRUDEntityDefinition;
use CRUDlex\CRUDFileProcessorInterface;

/**
 * An interface used by the {@see CRUDServiceProvider} to construct
 * {@see CRUDData} instances. By implementing this and handing it into
 * the service provider, the user can control what database (-variation) he
 * wants to use.
 */
interface CRUDDataFactoryInterface {

    /**
     * Creates instances.
     *
     * @param CRUDEntityDefinition $definition
     * the definition of the entities managed by the to be created instance
     * @param CRUDFileProcessorInterface $fileProcessor
     * the file processor managing uploaded files
     *
     * @return CRUDData
     * the newly created instance
     */
    public function createData(CRUDEntityDefinition $definition, CRUDFileProcessorInterface $fileProcessor);

}
