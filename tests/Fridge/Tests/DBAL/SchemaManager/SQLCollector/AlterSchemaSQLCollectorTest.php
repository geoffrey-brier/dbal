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
    Fridge\DBAL\SchemaManager\SQLCollector\AlterSchemaSQLCollector,
    Fridge\DBAL\Type\Type;

/**
 * Alter schema SQL collector test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class AlterSchemaSQLCollectorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\SchemaManager\SQLCollector\AlterSchemaSQLCollector */
    protected $sqlCollector;

    /** @var \Fridge\DBAL\Platform\PlatformInterface */
    protected $platformMock;

    /** @var \Fridge\DBAL\Schema\Diff\SchemaDiff */
    protected $schemaDiff;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->platformMock = $this->getMock('Fridge\DBAL\Platform\PlatformInterface');
        $this->sqlCollector = new AlterSchemaSQLCollector($this->platformMock);

        $this->schemaDiff = new Schema\Diff\SchemaDiff(
            'foo',
            'bar',
            array(
                new Schema\Table(
                    'created',
                    array(new Schema\Column('foo', Type::getType(Type::INTEGER))),
                    null,
                    array(new Schema\ForeignKey('foo', array('foo'), 'bar', array('bar')))
                ),
            ),
            array(
                new Schema\Diff\TableDiff(
                    'foo',
                    'bar',
                    array(new Schema\Column('created', Type::getType(Type::INTEGER))),
                    array(
                        new Schema\Diff\ColumnDiff(
                            'foo',
                            'bar',
                            new Schema\Column('altered', Type::getType(Type::INTEGER))
                        ),
                    ),
                    array(new Schema\Column('dropped', Type::getType(Type::INTEGER))),
                    new Schema\PrimaryKey('created', array('bar')),
                    new Schema\PrimaryKey('dropped', array('foo')),
                    array(new Schema\ForeignKey('created', array('bar'), 'bar', array('bar'))),
                    array(new Schema\ForeignKey('dropped', array('foo'), 'bar', array('bar'))),
                    array(new Schema\Index('created', array('baz'))),
                    array(new Schema\Index('dropped', array('baz'), true))
                ),
            ),
            array(
                new Schema\Table(
                    'dropped',
                    array(new Schema\Column('bar', Type::getType(Type::INTEGER))),
                    null,
                    array(new Schema\ForeignKey('bar', array('bar'), 'bar', array('bar')))
                ),
            ),
            array(new Schema\Sequence('foo', 1, 1)),
            array(new Schema\Sequence('foo', 1, 2)),
            array(new Schema\View('foo', 'SELECT * FROM foo')),
            array(new Schema\View('foo', 'SELECT foo FROM foo'))
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->sqlCollector);
        unset($this->platformMock);
        unset($this->schemaDiff);
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
        $createdSequences = $this->schemaDiff->getCreatedSequences();
        $droppedSequences = $this->schemaDiff->getDroppedSequences();

        $createdViews = $this->schemaDiff->getCreatedViews();
        $droppedViews = $this->schemaDiff->getDroppedViews();

        $createdTables = $this->schemaDiff->getCreatedTables();
        $alteredTables = $this->schemaDiff->getAlteredTables();
        $droppedTables = $this->schemaDiff->getDroppedTables();

        $createdColumns = $alteredTables[0]->getCreatedColumns();
        $alteredColumns = $alteredTables[0]->getAlteredColumns();
        $droppedColumns = $alteredTables[0]->getDroppedColumns();

        $droppedForeignKeys = $alteredTables[0]->getDroppedForeignKeys();
        $createdForeignKeys = $alteredTables[0]->getCreatedForeignKeys();

        $droppedIndexes = $alteredTables[0]->getDroppedIndexes();
        $createdIndexes = $alteredTables[0]->getCreatedIndexes();

        $this->platformMock
            ->expects($this->once())
            ->method('getDropSequenceSQLQuery')
            ->with($this->equalTo($droppedSequences[0]))
            ->will($this->returnValue('DROP SEQUENCE'));

        $this->platformMock
            ->expects($this->once())
            ->method('getDropViewSQLQuery')
            ->with($this->equalTo($droppedViews[0]))
            ->will($this->returnValue('DROP VIEW'));

        $this->platformMock
            ->expects($this->once())
            ->method('getRenameTableSQLQuery')
            ->with($this->equalTo($alteredTables[0]))
            ->will($this->returnValue('RENAME TABLE'));

        $this->platformMock
            ->expects($this->once())
            ->method('getDropIndexSQLQuery')
            ->with($this->equalTo($droppedIndexes[0]), $this->equalTo($alteredTables[0]->getNewName()))
            ->will($this->returnValue('DROP INDEX'));

        $this->platformMock
            ->expects($this->any())
            ->method('getDropForeignKeySQLQuery')
            ->will($this->returnValue('DROP FOREIGN KEY'));

        $this->platformMock
            ->expects($this->once())
            ->method('getDropTableSQLQuery')
            ->with($this->equalTo($droppedTables[0]))
            ->will($this->returnValue('DROP TABLE'));

        $this->platformMock
            ->expects($this->once())
            ->method('getDropPrimaryKeySQLQuery')
            ->with(
                $this->equalTo($alteredTables[0]->getDroppedPrimaryKey()),
                $this->equalTo($alteredTables[0]->getNewName())
            )
            ->will($this->returnValue('DROP PRIMARY KEY'));

        $this->platformMock
            ->expects($this->once())
            ->method('getDropColumnSQLQuery')
            ->with($this->equalTo($droppedColumns[0]), $this->equalTo($alteredTables[0]->getNewName()))
            ->will($this->returnValue('DROP COLUMN'));

        $this->platformMock
            ->expects($this->once())
            ->method('getRenameColumnSQLQueries')
            ->with($this->equalTo($alteredColumns[0]), $this->equalTo($alteredTables[0]->getNewName()))
            ->will($this->returnValue(array('ALTER COLUMN')));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateColumnSQLQueries')
            ->with($this->equalTo($createdColumns[0]), $this->equalTo($alteredTables[0]->getNewName()))
            ->will($this->returnValue(array('CREATE COLUMN')));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreatePrimaryKeySQLQuery')
            ->with(
                $this->equalTo($alteredTables[0]->getCreatedPrimaryKey()),
                $this->equalTo($alteredTables[0]->getNewName())
            )
            ->will($this->returnValue('CREATE PRIMARY KEY'));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateIndexSQLQuery')
            ->with($this->equalTo($createdIndexes[0]), $this->equalTo($alteredTables[0]->getNewName()))
            ->will($this->returnValue('CREATE INDEX'));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateTableSQLQueries')
            ->with($this->equalTo($createdTables[0]), array('foreign_key' => false))
            ->will($this->returnValue(array('CREATE TABLE')));

        $this->platformMock
            ->expects($this->any())
            ->method('getCreateForeignKeySQLQuery')
            ->will($this->returnValue('CREATE FOREIGN KEY'));

        $this->platformMock
            ->expects($this->once())
            ->method('getRenameDatabaseSQLQuery')
            ->with($this->equalTo($this->schemaDiff))
            ->will($this->returnValue('RENAME SCHEMA'));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateViewSQLQuery')
            ->with($this->equalTo($createdViews[0]))
            ->will($this->returnValue('CREATE VIEW'));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateSequenceSQLQuery')
            ->with($this->equalTo($createdSequences[0]))
            ->will($this->returnValue('CREATE SEQUENCE'));

        $this->platformMock
            ->expects($this->once())
            ->method('getRenameDatabaseSQLQuery')
            ->with($this->equalTo($this->schemaDiff))
            ->will($this->returnValue('RENAME SCHEMA'));

        $this->sqlCollector->collect($this->schemaDiff);

        $this->assertEquals(array('DROP SEQUENCE'), $this->sqlCollector->getDropSequenceQueries());
        $this->assertEquals(array('DROP VIEW'), $this->sqlCollector->getDropViewQueries());
        $this->assertEquals(array('RENAME TABLE'), $this->sqlCollector->getRenameTableQueries());
        $this->assertEquals(array('DROP INDEX'), $this->sqlCollector->getDropIndexQueries());
        $this->assertEquals(
            array('DROP FOREIGN KEY', 'DROP FOREIGN KEY'),
            $this->sqlCollector->getDropForeignKeyQueries()
        );
        $this->assertEquals(array('DROP TABLE'), $this->sqlCollector->getDropTableQueries());
        $this->assertEquals(array('DROP PRIMARY KEY'), $this->sqlCollector->getDropPrimaryKeyQueries());
        $this->assertEquals(array('DROP COLUMN'), $this->sqlCollector->getDropColumnQueries());
        $this->assertEquals(array('ALTER COLUMN'), $this->sqlCollector->getAlterColumnQueries());
        $this->assertEquals(array('CREATE COLUMN'), $this->sqlCollector->getCreateColumnQueries());
        $this->assertEquals(array('CREATE PRIMARY KEY'), $this->sqlCollector->getCreatePrimaryKeyQueries());
        $this->assertEquals(array('CREATE INDEX'), $this->sqlCollector->getCreateIndexQueries());
        $this->assertEquals(array('CREATE TABLE'), $this->sqlCollector->getCreateTableQueries());
        $this->assertEquals(
            array('CREATE FOREIGN KEY', 'CREATE FOREIGN KEY'),
            $this->sqlCollector->getCreateForeignKeyQueries()
        );
        $this->assertEquals(array('CREATE VIEW'), $this->sqlCollector->getCreateViewQueries());
        $this->assertEquals(array('CREATE SEQUENCE'), $this->sqlCollector->getCreateSequenceQueries());
        $this->assertEquals(array('RENAME SCHEMA'), $this->sqlCollector->getRenameSchemaQueries());
        $this->assertEquals(
            array(
                'DROP SEQUENCE',
                'DROP VIEW',
                'RENAME TABLE',
                'DROP INDEX',
                'DROP FOREIGN KEY',
                'DROP FOREIGN KEY',
                'DROP TABLE',
                'DROP PRIMARY KEY',
                'DROP COLUMN',
                'ALTER COLUMN',
                'CREATE COLUMN',
                'CREATE PRIMARY KEY',
                'CREATE INDEX',
                'CREATE TABLE',
                'CREATE FOREIGN KEY',
                'CREATE FOREIGN KEY',
                'CREATE VIEW',
                'CREATE SEQUENCE',
                'RENAME SCHEMA',
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

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateTableSQLQueries')
            ->will($this->returnValue(array('foo')));

        $this->sqlCollector->collect($this->schemaDiff);
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

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateTableSQLQueries')
            ->will($this->returnValue(array('foo')));

        $this->sqlCollector->collect($this->schemaDiff);
        $this->sqlCollector->init();

        $this->assertInitialState();
    }

    /**
     * Asserts the initial state.
     */
    protected function assertInitialState()
    {
        $this->assertEmpty($this->sqlCollector->getDropSequenceQueries());
        $this->assertEmpty($this->sqlCollector->getDropViewQueries());
        $this->assertEmpty($this->sqlCollector->getRenameTableQueries());
        $this->assertEmpty($this->sqlCollector->getDropIndexQueries());
        $this->assertEmpty($this->sqlCollector->getDropForeignKeyQueries());
        $this->assertEmpty($this->sqlCollector->getDropTableQueries());
        $this->assertEmpty($this->sqlCollector->getDropPrimaryKeyQueries());
        $this->assertEmpty($this->sqlCollector->getDropColumnQueries());
        $this->assertEmpty($this->sqlCollector->getAlterColumnQueries());
        $this->assertEmpty($this->sqlCollector->getCreateColumnQueries());
        $this->assertEmpty($this->sqlCollector->getCreatePrimaryKeyQueries());
        $this->assertEmpty($this->sqlCollector->getCreateIndexQueries());
        $this->assertEmpty($this->sqlCollector->getCreateTableQueries());
        $this->assertEmpty($this->sqlCollector->getCreateForeignKeyQueries());
        $this->assertEmpty($this->sqlCollector->getCreateViewQueries());
        $this->assertEmpty($this->sqlCollector->getCreateSequenceQueries());
        $this->assertEmpty($this->sqlCollector->getRenameSchemaQueries());
        $this->assertEmpty($this->sqlCollector->getQueries());
    }
}
