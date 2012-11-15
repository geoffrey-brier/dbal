<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\SchemaManager\SQLCollector;

use Fridge\DBAL\Schema,
    Fridge\DBAL\SchemaManager\SQLCollector\DropTableSQLCollector,
    Fridge\DBAL\Type\Type;

/**
 * Drop table SQL collector test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class DropTableSQLCollectorTest extends \PHPUnit_Framework_TestCase
{
    /**  @var \Fridge\DBAL\SchemaManager\SQLCollector\DropTableSQLCollector */
    protected $sqlCollector;

    /** @var \Fridge\DBAL\Platform\PlatformInterface */
    protected $platformMock;

    /** @var \Fridge\DBAL\Schema\Table */
    protected $table;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->platformMock = $this->getMock('Fridge\DBAL\Platform\PlatformInterface');
        $this->sqlCollector = new DropTableSQLCollector($this->platformMock);

        $this->table = new Schema\Table(
            'foo',
            array(new Schema\Column('foo', Type::getType(Type::INTEGER))),
            null,
            array(new Schema\ForeignKey('foo', array('foo'), 'bar', array('bar')))
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->sqlCollector);
        unset($this->platformMock);
        unset($this->table);
    }

    public function testInitialState()
    {
        $this->assertInitialState();
    }

    public function testPlatform()
    {
        $this->assertSame($this->platformMock, $this->sqlCollector->getPlatform());

        $platformMock = $this->getMock('Fridge\DBAL\Platform\PlatformInterface');
        $this->sqlCollector->setPlatform($platformMock);

        $this->assertSame($platformMock, $this->sqlCollector->getPlatform());
    }

    public function testCollect()
    {
        $this->platformMock
            ->expects($this->once())
            ->method('getDropTableSQLQueries')
            ->with($this->equalTo($this->table))
            ->will($this->returnValue(array('DROP TABLE')));

        $this->platformMock
            ->expects($this->once())
            ->method('getDropForeignKeySQLQueries')
            ->with($this->equalTo($this->table->getForeignKey('foo')), $this->equalTo($this->table->getName()))
            ->will($this->returnValue(array('DROP FOREIGN KEY')));

        $this->sqlCollector->collect($this->table);

        $this->assertSame(array('DROP FOREIGN KEY'), $this->sqlCollector->getDropForeignKeyQueries());
        $this->assertSame(array('DROP TABLE'), $this->sqlCollector->getDropTableQueries());
        $this->assertSame(array('DROP FOREIGN KEY', 'DROP TABLE'), $this->sqlCollector->getQueries());
    }

    public function testPlatformWithCollectedQueries()
    {
        $this->platformMock
            ->expects($this->once())
            ->method('getDropTableSQLQueries')
            ->will($this->returnValue(array('foo')));

        $this->platformMock
            ->expects($this->once())
            ->method('getDropForeignKeySQLQueries')
            ->will($this->returnValue(array('foo')));

        $this->sqlCollector->collect($this->table);
        $this->sqlCollector->setPlatform($this->platformMock);

        $this->assertInitialState();
    }

    public function testInit()
    {
        $this->platformMock
            ->expects($this->once())
            ->method('getDropTableSQLQueries')
            ->will($this->returnValue(array('foo')));

        $this->platformMock
            ->expects($this->once())
            ->method('getDropForeignKeySQLQueries')
            ->will($this->returnValue(array('foo')));

        $this->sqlCollector->collect($this->table);
        $this->sqlCollector->init();

        $this->assertInitialState();
    }

    /**
     * Asserts the initial state.
     */
    protected function assertInitialState()
    {
        $this->assertEmpty($this->sqlCollector->getDropForeignKeyQueries());
        $this->assertEmpty($this->sqlCollector->getDropTableQueries());
        $this->assertEmpty($this->sqlCollector->getQueries());
    }
}