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
 * Mysqli schema manager tests.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class MysqliSchemaManagerTest extends AbstractMySQLSchemaManagerTest
{
    /**
     * {@inheritdoc}
     */
    static public function setUpBeforeClass()
    {
        if (ConnectionUtility::hasConnection(ConnectionUtility::MYSQLI)) {
            self::$fixture = new MySQLFixture(ConnectionUtility::MYSQLI);
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
        if (ConnectionUtility::hasConnection(ConnectionUtility::MYSQLI)) {
            $this->schemaManager = new MySQLSchemaManager(ConnectionUtility::getConnection(ConnectionUtility::MYSQLI));
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

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testGetTableChecks()
    {
        $this->schemaManager->getTableChecks('foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testDropCheck()
    {
        $checkMock = $this->getMock('Fridge\DBAL\Schema\Check', array(), array('foo', 'bar'));

        $this->schemaManager->dropCheck($checkMock, 'foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testCreateCheck()
    {
        $checkMock = $this->getMock('Fridge\DBAL\Schema\Check', array(), array('foo', 'bar'));

        $this->schemaManager->createCheck($checkMock, 'foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testDropAndCreateCheck()
    {
        $checkMock = $this->getMock('Fridge\DBAL\Schema\Check', array(), array('foo', 'bar'));

        $this->schemaManager->dropAndCreateCheck($checkMock, 'foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testDropConstraintWithCheck()
    {
        $checkMock = $this->getMock('Fridge\DBAL\Schema\Check', array(), array('foo', 'bar'));

        $this->schemaManager->dropConstraint($checkMock, 'foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testCreateConstraintWithCheck()
    {
        $checkMock = $this->getMock('Fridge\DBAL\Schema\Check', array(), array('foo', 'bar'));

        $this->schemaManager->createConstraint($checkMock, 'foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testDropAndCreateConstraintWithCheck()
    {
        $checkMock = $this->getMock('Fridge\DBAL\Schema\Check', array(), array('foo', 'bar'));

        $this->schemaManager->dropAndCreateConstraint($checkMock, 'foo');
    }
}
