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

use Fridge\DBAL\SchemaManager\MySQLSchemaManager,
    Fridge\Tests\ConnectionUtility,
    Fridge\Tests\Fixture\MySQLFixture;

/**
 * PDO MySQL schema manager test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PDOMySQLSchemaManagerTest extends AbstractSchemaManagerTest
{
    /**
     * {@inheritdoc}
     */
    static public function setUpBeforeClass()
    {
        if (ConnectionUtility::hasConnection(ConnectionUtility::PDO_MYSQL)) {
            self::$fixture = new MySQLFixture(ConnectionUtility::PDO_MYSQL);
        } else {
            self::$fixture = null;
        }

        parent::setUpBeforeClass();
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (ConnectionUtility::hasConnection(ConnectionUtility::PDO_MYSQL)) {
            $this->schemaManager = new MySQLSchemaManager(ConnectionUtility::getConnection(ConnectionUtility::PDO_MYSQL));
        }

        parent::setUp();
    }

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
