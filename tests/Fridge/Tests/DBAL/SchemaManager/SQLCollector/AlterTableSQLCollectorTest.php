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
            new Schema\Table('foo'),
            new Schema\Table('bar'),
            array(new Schema\Column('created', Type::getType(Type::INTEGER))),
            array(
                new Schema\Diff\ColumnDiff(
                    new Schema\Column('altered', Type::getType(Type::INTEGER)),
                    new Schema\Column('altered', Type::getType(Type::SMALLINTEGER)),
                    array()
                ),
            ),
            array(new Schema\Column('dropped', Type::getType(Type::INTEGER))),
            new Schema\PrimaryKey('created', array('bar')),
            new Schema\PrimaryKey('dropped', array('foo')),
            array(new Schema\ForeignKey('created', array('bar'), 'bar', array('bar'))),
            array(new Schema\ForeignKey('dropped', array('foo'), 'bar', array('bar'))),
            array(new Schema\Index('created', array('baz'))),
            array(new Schema\Index('dropped', array('baz'), true)),
            array(new Schema\Check('created', 'foo')),
            array(new Schema\Check('dropped', 'bar'))
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
        $this->assertSame($this->platformMock, $this->sqlCollector->getPlatform());

        $platformMock = $this->getMock('Fridge\DBAL\Platform\PlatformInterface');
        $this->sqlCollector->setPlatform($platformMock);

        $this->assertSame($platformMock, $this->sqlCollector->getPlatform());
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

        $droppedChecks = $this->tableDiff->getDroppedChecks();
        $createdChecks = $this->tableDiff->getCreatedChecks();

        $this->platformMock
            ->expects($this->once())
            ->method('getRenameTableSQLQueries')
            ->with($this->equalTo($this->tableDiff))
            ->will($this->returnValue(array('RENAME TABLE')));

        $this->platformMock
            ->expects($this->once())
            ->method('getDropCheckSQLQuery')
            ->with($this->equalTo($droppedChecks[0]), $this->equalTo($this->tableDiff->getNewAsset()->getName()))
            ->will($this->returnValue('DROP CHECK'));

        $this->platformMock
            ->expects($this->once())
            ->method('getDropForeignKeySQLQuery')
            ->with($this->equalTo($droppedForeignKeys[0]), $this->equalTo($this->tableDiff->getNewAsset()->getName()))
            ->will($this->returnValue('DROP FOREIGN KEY'));

        $this->platformMock
            ->expects($this->once())
            ->method('getDropIndexSQLQuery')
            ->with($this->equalTo($droppedIndexes[0]), $this->equalTo($this->tableDiff->getNewAsset()->getName()))
            ->will($this->returnValue('DROP INDEX'));

        $this->platformMock
            ->expects($this->once())
            ->method('getDropPrimaryKeySQLQuery')
            ->with(
                $this->equalTo($this->tableDiff->getDroppedPrimaryKey()),
                $this->equalTo($this->tableDiff->getNewAsset()->getName())
            )
            ->will($this->returnValue('DROP PRIMARY KEY'));

        $this->platformMock
            ->expects($this->once())
            ->method('getDropColumnSQLQuery')
            ->with($this->equalTo($droppedColumns[0]), $this->equalTo($this->tableDiff->getNewAsset()->getName()))
            ->will($this->returnValue('DROP COLUMN'));

        $this->platformMock
            ->expects($this->once())
            ->method('getAlterColumnSQLQueries')
            ->with($this->equalTo($alteredColumns[0]), $this->equalTo($this->tableDiff->getNewAsset()->getName()))
            ->will($this->returnValue(array('ALTER COLUMN')));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateColumnSQLQueries')
            ->with($this->equalTo($createdColumns[0]), $this->equalTo($this->tableDiff->getNewAsset()->getName()))
            ->will($this->returnValue(array('CREATE COLUMN')));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreatePrimaryKeySQLQueries')
            ->with(
                $this->equalTo($this->tableDiff->getCreatedPrimaryKey()),
                $this->equalTo($this->tableDiff->getNewAsset()->getName())
            )
            ->will($this->returnValue(array('CREATE PRIMARY KEY')));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateIndexSQLQueries')
            ->with($this->equalTo($createdIndexes[0]), $this->equalTo($this->tableDiff->getNewAsset()->getName()))
            ->will($this->returnValue(array('CREATE INDEX')));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateForeignKeySQLQueries')
            ->with($this->equalTo($createdForeignKeys[0]), $this->equalTo($this->tableDiff->getNewAsset()->getName()))
            ->will($this->returnValue(array('CREATE FOREIGN KEY')));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateCheckSQLQueries')
            ->with($this->equalTo($createdChecks[0]), $this->equalTo($this->tableDiff->getNewAsset()->getName()))
            ->will($this->returnValue(array('CREATE CHECK')));

        $this->sqlCollector->collect($this->tableDiff);

        $this->assertSame(array('RENAME TABLE'), $this->sqlCollector->getRenameTableQueries());
        $this->assertSame(array('DROP CHECK'), $this->sqlCollector->getDropCheckQueries());
        $this->assertSame(array('DROP FOREIGN KEY'), $this->sqlCollector->getDropForeignKeyQueries());
        $this->assertSame(array('DROP INDEX'), $this->sqlCollector->getDropIndexQueries());
        $this->assertSame(array('DROP PRIMARY KEY'), $this->sqlCollector->getDropPrimaryKeyQueries());
        $this->assertSame(array('DROP COLUMN'), $this->sqlCollector->getDropColumnQueries());
        $this->assertSame(array('ALTER COLUMN'), $this->sqlCollector->getAlterColumnQueries());
        $this->assertSame(array('CREATE COLUMN'), $this->sqlCollector->getCreateColumnQueries());
        $this->assertSame(array('CREATE PRIMARY KEY'), $this->sqlCollector->getCreatePrimaryKeyQueries());
        $this->assertSame(array('CREATE INDEX'), $this->sqlCollector->getCreateIndexQueries());
        $this->assertSame(array('CREATE FOREIGN KEY'), $this->sqlCollector->getCreateForeignKeyQueries());
        $this->assertSame(array('CREATE CHECK'), $this->sqlCollector->getCreateCheckQueries());
        $this->assertSame(
            array(
                'RENAME TABLE',
                'DROP CHECK',
                'DROP FOREIGN KEY',
                'DROP INDEX',
                'DROP PRIMARY KEY',
                'DROP COLUMN',
                'ALTER COLUMN',
                'CREATE COLUMN',
                'CREATE PRIMARY KEY',
                'CREATE INDEX',
                'CREATE FOREIGN KEY',
                'CREATE CHECK',
            ),
            $this->sqlCollector->getQueries()
        );
    }

    public function testInit()
    {
        $this->platformMock
            ->expects($this->once())
            ->method('getRenameTableSQLQueries')
            ->will($this->returnValue(array('foo')));

        $this->platformMock
            ->expects($this->once())
            ->method('getAlterColumnSQLQueries')
            ->will($this->returnValue(array('foo')));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateColumnSQLQueries')
            ->will($this->returnValue(array('foo')));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreatePrimaryKeySQLQueries')
            ->will($this->returnValue(array('foo')));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateIndexSQLQueries')
            ->will($this->returnValue(array('foo')));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateForeignKeySQLQueries')
            ->will($this->returnValue(array('foo')));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateCheckSQLQueries')
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
        $this->assertEmpty($this->sqlCollector->getDropCheckQueries());
        $this->assertEmpty($this->sqlCollector->getDropForeignKeyQueries());
        $this->assertEmpty($this->sqlCollector->getDropIndexQueries());
        $this->assertEmpty($this->sqlCollector->getDropPrimaryKeyQueries());
        $this->assertEmpty($this->sqlCollector->getDropColumnQueries());
        $this->assertEmpty($this->sqlCollector->getAlterColumnQueries());
        $this->assertEmpty($this->sqlCollector->getCreateColumnQueries());
        $this->assertEmpty($this->sqlCollector->getCreatePrimaryKeyQueries());
        $this->assertEmpty($this->sqlCollector->getCreateIndexQueries());
        $this->assertEmpty($this->sqlCollector->getCreateForeignKeyQueries());
        $this->assertEmpty($this->sqlCollector->getCreateCheckQueries());
        $this->assertEmpty($this->sqlCollector->getQueries());
    }
}
