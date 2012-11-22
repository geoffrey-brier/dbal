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
    public function testCreateSequence()
    {
        $sequenceMock = $this->getMockBuilder('Fridge\DBAL\Schema\Sequence')
            ->setConstructorArgs(array('foo'))
            ->getMock();

        $this->schemaManager->createSequence($sequenceMock);
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testDropSequence()
    {
        $sequenceMock = $this->getMockBuilder('Fridge\DBAL\Schema\Sequence')
            ->setConstructorArgs(array('foo'))
            ->getMock();

        $this->schemaManager->dropSequence($sequenceMock);
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testDropAndCreateSequence()
    {
        $sequenceMock = $this->getMockBuilder('Fridge\DBAL\Schema\Sequence')
            ->setConstructorArgs(array('foo'))
            ->getMock();

        $this->schemaManager->dropAndCreateSequence($sequenceMock);
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testDropCheck()
    {
        $checkMock = $this->getMockBuilder('Fridge\DBAL\Schema\Check')
            ->setConstructorArgs(array('foo', 'bar'))
            ->getMock();

        $this->schemaManager->dropCheck($checkMock, 'foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testCreateCheck()
    {
        $checkMock = $this->getMockBuilder('Fridge\DBAL\Schema\Check')
            ->setConstructorArgs(array('foo', 'bar'))
            ->getMock();

        $this->schemaManager->createCheck($checkMock, 'foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testDropAndCreateCheck()
    {
        $checkMock = $this->getMockBuilder('Fridge\DBAL\Schema\Check')
            ->setConstructorArgs(array('foo', 'bar'))
            ->getMock();

        $this->schemaManager->dropAndCreateCheck($checkMock, 'foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testDropConstraintWithCheck()
    {
        $checkMock = $this->getMockBuilder('Fridge\DBAL\Schema\Check')
            ->setConstructorArgs(array('foo', 'bar'))
            ->getMock();

        $this->schemaManager->dropConstraint($checkMock, 'foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testCreateConstraintWithCheck()
    {
        $checkMock = $this->getMockBuilder('Fridge\DBAL\Schema\Check')
            ->setConstructorArgs(array('foo', 'bar'))
            ->getMock();

        $this->schemaManager->createConstraint($checkMock, 'foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testDropAndCreateConstraintWithCheck()
    {
        $checkMock = $this->getMockBuilder('Fridge\DBAL\Schema\Check')
            ->setConstructorArgs(array('foo', 'bar'))
            ->getMock();

        $this->schemaManager->dropAndCreateConstraint($checkMock, 'foo');
    }
}
