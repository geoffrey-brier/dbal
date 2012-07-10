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
    Fridge\DBAL\SchemaManager\SQLCollector\AlterTableSQLCollector,
    Fridge\DBAL\Type\Type;

/**
 * Alter table SQL collector test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class AlterTableSQLCollectorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\SchemaManager\SQLCollector\AlterTableSQLCollector */
    protected $sqlCollector;

    /** @var \Fridge\DBAL\Platform\PlatformInterface */
    protected $platformMock;

    /** @var \Fridge\DBAL\Schema\Diff\TableDiff */
    protected $tableDiff;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->platformMock = $this->getMock('Fridge\DBAL\Platform\PlatformInterface');
        $this->sqlCollector = new AlterTableSQLCollector($this->platformMock);

        $this->tableDiff = new Schema\Diff\TableDiff(
            'foo',
            'bar',
            array(new Schema\Column('created', Type::getType(Type::INTEGER))),
            array(new Schema\Diff\ColumnDiff('foo', 'bar', new Schema\Column('altered', Type::getType(Type::INTEGER)))),
            array(new Schema\Column('dropped', Type::getType(Type::INTEGER))),
            new Schema\PrimaryKey('created', array('bar')),
            new Schema\PrimaryKey('dropped', array('foo')),
            array(new Schema\ForeignKey('created', array('bar'), 'bar', array('bar'))),
            array(new Schema\ForeignKey('dropped', array('foo'), 'bar', array('bar'))),
            array(new Schema\Index('created', array('baz'))),
            array(new Schema\Index('dropped', array('baz'), true))
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->sqlCollector);
        unset($this->platformMock);
        unset($this->tableDiff);
    }

    public function testInitialState()
    {
        $this->assertInitialState();
    }

    public function testPlatform()
    {
        $this->assertEquals($this->platformMock, $this->sqlCollector->getPlatform());

        $platformMock = $this->getMock('Fridge\DBAL\Platform\PlatformInterface');
        $this->sqlCollector->setPlatform($platformMock);

        $this->assertEquals($platformMock, $this->sqlCollector->getPlatform());
    }

    public function testCollect()
    {
        $createdColumns = $this->tableDiff->getCreatedColumns();
        $alteredColumns = $this->tableDiff->getAlteredColumns();
        $droppedColumns = $this->tableDiff->getDroppedColumns();

        $droppedForeignKeys = $this->tableDiff->getDroppedForeignKeys();
        $createdForeignKeys = $this->tableDiff->getCreatedForeignKeys();

        $droppedIndexes = $this->tableDiff->getDroppedIndexes();
        $createdIndexes = $this->tableDiff->getCreatedIndexes();

        $this->platformMock
            ->expects($this->once())
            ->method('getRenameTableSQLQuery')
            ->with($this->equalTo($this->tableDiff))
            ->will($this->returnValue('RENAME TABLE'));

        $this->platformMock
            ->expects($this->once())
            ->method('getDropIndexSQLQuery')
            ->with($this->equalTo($droppedIndexes[0]), $this->equalTo($this->tableDiff->getNewName()))
            ->will($this->returnValue('DROP INDEX'));

        $this->platformMock
            ->expects($this->once())
            ->method('getDropForeignKeySQLQuery')
            ->with($this->equalTo($droppedForeignKeys[0]), $this->equalTo($this->tableDiff->getNewName()))
            ->will($this->returnValue('DROP FOREIGN KEY'));


        $this->platformMock
            ->expects($this->once())
            ->method('getDropPrimaryKeySQLQuery')
            ->with(
                $this->equalTo($this->tableDiff->getDroppedPrimaryKey()),
                $this->equalTo($this->tableDiff->getNewName())
            )
            ->will($this->returnValue('DROP PRIMARY KEY'));

        $this->platformMock
            ->expects($this->once())
            ->method('getDropColumnSQLQuery')
            ->with($this->equalTo($droppedColumns[0]), $this->equalTo($this->tableDiff->getNewName()))
            ->will($this->returnValue('DROP COLUMN'));

        $this->platformMock
            ->expects($this->once())
            ->method('getRenameColumnSQLQueries')
            ->with($this->equalTo($alteredColumns[0]), $this->equalTo($this->tableDiff->getNewName()))
            ->will($this->returnValue(array('ALTER COLUMN')));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateColumnSQLQueries')
            ->with($this->equalTo($createdColumns[0]), $this->equalTo($this->tableDiff->getNewName()))
            ->will($this->returnValue(array('CREATE COLUMN')));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreatePrimaryKeySQLQuery')
            ->with(
                $this->equalTo($this->tableDiff->getCreatedPrimaryKey()),
                $this->equalTo($this->tableDiff->getNewName())
            )
            ->will($this->returnValue('CREATE PRIMARY KEY'));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateIndexSQLQuery')
            ->with($this->equalTo($createdIndexes[0]), $this->equalTo($this->tableDiff->getNewName()))
            ->will($this->returnValue('CREATE INDEX'));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateForeignKeySQLQuery')
            ->with($this->equalTo($createdForeignKeys[0]), $this->equalTo($this->tableDiff->getNewName()))
            ->will($this->returnValue('CREATE FOREIGN KEY'));

        $this->sqlCollector->collect($this->tableDiff);

        $this->assertEquals(array('RENAME TABLE'), $this->sqlCollector->getRenameTableQueries());
        $this->assertEquals(array('DROP INDEX'), $this->sqlCollector->getDropIndexQueries());
        $this->assertEquals(array('DROP FOREIGN KEY'), $this->sqlCollector->getDropForeignKeyQueries());
        $this->assertEquals(array('DROP PRIMARY KEY'), $this->sqlCollector->getDropPrimaryKeyQueries());
        $this->assertEquals(array('DROP COLUMN'), $this->sqlCollector->getDropColumnQueries());
        $this->assertEquals(array('ALTER COLUMN'), $this->sqlCollector->getAlterColumnQueries());
        $this->assertEquals(array('CREATE COLUMN'), $this->sqlCollector->getCreateColumnQueries());
        $this->assertEquals(array('CREATE PRIMARY KEY'), $this->sqlCollector->getCreatePrimaryKeyQueries());
        $this->assertEquals(array('CREATE INDEX'), $this->sqlCollector->getCreateIndexQueries());
        $this->assertEquals(array('CREATE FOREIGN KEY'), $this->sqlCollector->getCreateForeignKeyQueries());
        $this->assertEquals(
            array(
                'RENAME TABLE',
                'DROP INDEX',
                'DROP FOREIGN KEY',
                'DROP PRIMARY KEY',
                'DROP COLUMN',
                'ALTER COLUMN',
                'CREATE COLUMN',
                'CREATE PRIMARY KEY',
                'CREATE INDEX',
                'CREATE FOREIGN KEY',
            ),
            $this->sqlCollector->getQueries()
        );
    }

    public function testPlatformWithCollectedQueries()
    {
        $this->platformMock
            ->expects($this->once())
            ->method('getRenameColumnSQLQueries')
            ->will($this->returnValue(array('foo')));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateColumnSQLQueries')
            ->will($this->returnValue(array('foo')));

        $this->sqlCollector->collect($this->tableDiff);
        $this->sqlCollector->setPlatform($this->platformMock);

        $this->assertInitialState();
    }

    public function testInit()
    {
        $this->platformMock
            ->expects($this->once())
            ->method('getRenameColumnSQLQueries')
            ->will($this->returnValue(array('foo')));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateColumnSQLQueries')
            ->will($this->returnValue(array('foo')));

        $this->sqlCollector->collect($this->tableDiff);
        $this->sqlCollector->init();

        $this->assertInitialState();
    }

    /**
     * Asserts the intial state.
     */
    protected function assertInitialState()
    {
        $this->assertEmpty($this->sqlCollector->getRenameTableQueries());
        $this->assertEmpty($this->sqlCollector->getDropIndexQueries());
        $this->assertEmpty($this->sqlCollector->getDropForeignKeyQueries());
        $this->assertEmpty($this->sqlCollector->getDropPrimaryKeyQueries());
        $this->assertEmpty($this->sqlCollector->getDropColumnQueries());
        $this->assertEmpty($this->sqlCollector->getAlterColumnQueries());
        $this->assertEmpty($this->sqlCollector->getCreateColumnQueries());
        $this->assertEmpty($this->sqlCollector->getCreatePrimaryKeyQueries());
        $this->assertEmpty($this->sqlCollector->getCreateIndexQueries());
        $this->assertEmpty($this->sqlCollector->getCreateForeignKeyQueries());
        $this->assertEmpty($this->sqlCollector->getQueries());
    }
}
