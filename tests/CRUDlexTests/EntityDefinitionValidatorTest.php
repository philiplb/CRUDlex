<?php

/*
 * This file is part of the CRUDlex package.
 *
 * (c) Philip Lehmann-Böhm <philip@philiplb.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use CRUDlex\EntityDefinitionValidator;
use Symfony\Component\Yaml\Yaml;

class EntityTest extends \PHPUnit_Framework_TestCase {

    public function testValidate() {

        $fileContent = file_get_contents(__DIR__.'/../crud.yml');
        $validDefinition = Yaml::parse($fileContent);

        $validator = new EntityDefinitionValidator();

        $validator->validate($validDefinition);

        try {
            $validator->validate(['foo' => 'bar']);
            $this->fail();
        } catch (\LogicException $e) {
            // Expected
        } catch (\Exception $e) {
            $this->fail();
        }

    }

}