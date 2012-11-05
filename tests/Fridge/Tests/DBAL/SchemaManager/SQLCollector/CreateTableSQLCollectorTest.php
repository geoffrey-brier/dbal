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
    Fridge\DBAL\SchemaManager\SQLCollector\CreateTableSQLCollector,
    Fridge\DBAL\Type\Type;

/**
 * Create table SQL collector test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CreateTableSQLCollectorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\SchemaManager\SQLCollector\CreateTableSQLCollector */
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
        $this->sqlCollector = new CreateTableSQLCollector($this->platformMock);

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
            ->method('getCreateTableSQLQueries')
            ->with($this->equalTo($this->table), $this->equalTo(array('foreign_key' => false)))
            ->will($this->returnValue(array('CREATE TABLE')));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateForeignKeySQLQueries')
            ->with($this->equalTo($this->table->getForeignKey('foo')))
            ->will($this->returnValue(array('CREATE FOREIGN KEY')));

        $this->sqlCollector->collect($this->table);

        $this->assertSame(array('CREATE TABLE'), $this->sqlCollector->getCreateTableQueries());
        $this->assertSame(array('CREATE FOREIGN KEY'), $this->sqlCollector->getCreateForeignKeyQueries());
        $this->assertSame(array('CREATE TABLE', 'CREATE FOREIGN KEY'), $this->sqlCollector->getQueries());
    }

    public function testPlatformWithCollectedQueries()
    {
        $this->platformMock
            ->expects($this->once())
            ->method('getCreateTableSQLQueries')
            ->will($this->returnValue(array('foo')));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateForeignKeySQLQueries')
            ->will($this->returnValue(array('foo')));

        $this->sqlCollector->collect($this->table);
        $this->sqlCollector->setPlatform($this->platformMock);

        $this->assertInitialState();
    }

    public function testInit()
    {
        $this->platformMock
            ->expects($this->once())
            ->method('getCreateTableSQLQueries')
            ->will($this->returnValue(array('foo')));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateForeignKeySQLQueries')
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
        $this->assertEmpty($this->sqlCollector->getCreateTableQueries());
        $this->assertEmpty($this->sqlCollector->getCreateForeignKeyQueries());
        $this->assertEmpty($this->sqlCollector->getQueries());
    }
}
