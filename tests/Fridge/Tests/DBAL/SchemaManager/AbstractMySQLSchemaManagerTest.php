<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\SchemaManager;

/**
 * Executes the functional schema manager test suite on a mysql database.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractMySQLSchemaManagerTest extends AbstractSchemaManagerTest
{
    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testGetSequences()
    {
        $this->schemaManager->getSequences();
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testCreateSequence()
    {
        $sequenceMock = $this->getMock('Fridge\DBAL\Schema\Sequence', array(), array('foo'));

        $this->schemaManager->createSequence($sequenceMock);
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testDropSequence()
    {
        $sequenceMock = $this->getMock('Fridge\DBAL\Schema\Sequence', array(), array('foo'));

        $this->schemaManager->dropSequence($sequenceMock);
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testDropAndCreateSequence()
    {
        $sequenceMock = $this->getMock('Fridge\DBAL\Schema\Sequence', array(), array('foo'));

        $this->schemaManager->dropAndCreateSequence($sequenceMock);
    }
}
