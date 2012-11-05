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
            new Schema\Schema('foo'),
            new Schema\Schema('bar'),
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
                    new Schema\Table('foo'),
                    new Schema\Table('bar'),
                    array(new Schema\Column('created', Type::getType(Type::INTEGER))),
                    array(
                        new Schema\Diff\ColumnDiff(
                            new Schema\Column('foo', Type::getType(Type::INTEGER)),
                            new Schema\Column('altered', Type::getType(Type::INTEGER)),
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
        $this->assertSame($this->platformMock, $this->sqlCollector->getPlatform());

        $platformMock = $this->getMock('Fridge\DBAL\Platform\PlatformInterface');
        $this->sqlCollector->setPlatform($platformMock);

        $this->assertSame($platformMock, $this->sqlCollector->getPlatform());
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

        $droppedChecks = $alteredTables[0]->getDroppedChecks();
        $createdChecks = $alteredTables[0]->getCreatedChecks();

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
            ->method('getDropCheckSQLQuery')
            ->will($this->returnValue('DROP CHECK'));

        $this->platformMock
            ->expects($this->any())
            ->method('getDropForeignKeySQLQuery')
            ->will($this->returnValue('DROP FOREIGN KEY'));

        $this->platformMock
            ->expects($this->once())
            ->method('getDropIndexSQLQuery')
            ->with($this->equalTo($droppedIndexes[0]), $this->equalTo($alteredTables[0]->getNewAsset()->getName()))
            ->will($this->returnValue('DROP INDEX'));

        $this->platformMock
            ->expects($this->once())
            ->method('getDropPrimaryKeySQLQuery')
            ->with(
                $this->equalTo($alteredTables[0]->getDroppedPrimaryKey()),
                $this->equalTo($alteredTables[0]->getNewAsset()->getName())
            )
            ->will($this->returnValue('DROP PRIMARY KEY'));

        $this->platformMock
            ->expects($this->once())
            ->method('getDropTableSQLQuery')
            ->with($this->equalTo($droppedTables[0]))
            ->will($this->returnValue('DROP TABLE'));

        $this->platformMock
            ->expects($this->once())
            ->method('getDropColumnSQLQuery')
            ->with($this->equalTo($droppedColumns[0]), $this->equalTo($alteredTables[0]->getNewAsset()->getName()))
            ->will($this->returnValue('DROP COLUMN'));

        $this->platformMock
            ->expects($this->once())
            ->method('getAlterColumnSQLQueries')
            ->with($this->equalTo($alteredColumns[0]), $this->equalTo($alteredTables[0]->getNewAsset()->getName()))
            ->will($this->returnValue(array('ALTER COLUMN')));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateColumnSQLQueries')
            ->with($this->equalTo($createdColumns[0]), $this->equalTo($alteredTables[0]->getNewAsset()->getName()))
            ->will($this->returnValue(array('CREATE COLUMN')));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateTableSQLQueries')
            ->with($this->equalTo($createdTables[0]), array('foreign_key' => false))
            ->will($this->returnValue(array('CREATE TABLE')));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreatePrimaryKeySQLQueries')
            ->with(
                $this->equalTo($alteredTables[0]->getCreatedPrimaryKey()),
                $this->equalTo($alteredTables[0]->getNewAsset()->getName())
            )
            ->will($this->returnValue(array('CREATE PRIMARY KEY')));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateIndexSQLQuery')
            ->with($this->equalTo($createdIndexes[0]), $this->equalTo($alteredTables[0]->getNewAsset()->getName()))
            ->will($this->returnValue('CREATE INDEX'));

        $this->platformMock
            ->expects($this->any())
            ->method('getCreateForeignKeySQLQueries')
            ->will($this->returnValue(array('CREATE FOREIGN KEY')));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateCheckSQLQuery')
            ->will($this->returnValue('CREATE CHECK'));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateViewSQLQueries')
            ->with($this->equalTo($createdViews[0]))
            ->will($this->returnValue(array('CREATE VIEW')));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateSequenceSQLQueries')
            ->with($this->equalTo($createdSequences[0]))
            ->will($this->returnValue(array('CREATE SEQUENCE')));

        $this->platformMock
            ->expects($this->once())
            ->method('getRenameDatabaseSQLQueries')
            ->with($this->equalTo($this->schemaDiff))
            ->will($this->returnValue(array('RENAME SCHEMA')));

        $this->sqlCollector->collect($this->schemaDiff);

        $this->assertSame(array('DROP SEQUENCE'), $this->sqlCollector->getDropSequenceQueries());
        $this->assertSame(array('DROP VIEW'), $this->sqlCollector->getDropViewQueries());
        $this->assertSame(array('RENAME TABLE'), $this->sqlCollector->getRenameTableQueries());
        $this->assertSame(array('DROP CHECK'), $this->sqlCollector->getDropCheckQueries());
        $this->assertSame(
            array('DROP FOREIGN KEY', 'DROP FOREIGN KEY'),
            $this->sqlCollector->getDropForeignKeyQueries()
        );
        $this->assertSame(array('DROP INDEX'), $this->sqlCollector->getDropIndexQueries());
        $this->assertSame(array('DROP PRIMARY KEY'), $this->sqlCollector->getDropPrimaryKeyQueries());
        $this->assertSame(array('DROP TABLE'), $this->sqlCollector->getDropTableQueries());
        $this->assertSame(array('DROP COLUMN'), $this->sqlCollector->getDropColumnQueries());
        $this->assertSame(array('ALTER COLUMN'), $this->sqlCollector->getAlterColumnQueries());
        $this->assertSame(array('CREATE COLUMN'), $this->sqlCollector->getCreateColumnQueries());
        $this->assertSame(array('CREATE TABLE'), $this->sqlCollector->getCreateTableQueries());
        $this->assertSame(array('CREATE PRIMARY KEY'), $this->sqlCollector->getCreatePrimaryKeyQueries());
        $this->assertSame(array('CREATE INDEX'), $this->sqlCollector->getCreateIndexQueries());
        $this->assertSame(
            array('CREATE FOREIGN KEY', 'CREATE FOREIGN KEY'),
            $this->sqlCollector->getCreateForeignKeyQueries()
        );
        $this->assertSame(array('CREATE CHECK'), $this->sqlCollector->getCreateCheckQueries());
        $this->assertSame(array('CREATE VIEW'), $this->sqlCollector->getCreateViewQueries());
        $this->assertSame(array('CREATE SEQUENCE'), $this->sqlCollector->getCreateSequenceQueries());
        $this->assertSame(array('RENAME SCHEMA'), $this->sqlCollector->getRenameSchemaQueries());
        $this->assertSame(
            array(
                'DROP SEQUENCE',
                'DROP VIEW',
                'RENAME TABLE',
                'DROP CHECK',
                'DROP FOREIGN KEY',
                'DROP FOREIGN KEY',
                'DROP INDEX',
                'DROP PRIMARY KEY',
                'DROP TABLE',
                'DROP COLUMN',
                'ALTER COLUMN',
                'CREATE COLUMN',
                'CREATE TABLE',
                'CREATE PRIMARY KEY',
                'CREATE INDEX',
                'CREATE FOREIGN KEY',
                'CREATE FOREIGN KEY',
                'CREATE CHECK',
                'CREATE VIEW',
                'CREATE SEQUENCE',
                'RENAME SCHEMA',
            ),
            $this->sqlCollector->getQueries()
        );
    }

    public function testInit()
    {
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
            ->method('getCreateTableSQLQueries')
            ->will($this->returnValue(array('foo')));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreatePrimaryKeySQLQueries')
            ->will($this->returnValue(array('foo')));

        $this->platformMock
            ->expects($this->any())
            ->method('getCreateForeignKeySQLQueries')
            ->will($this->returnValue(array('foo')));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateViewSQLQueries')
            ->will($this->returnValue(array('foo')));

        $this->platformMock
            ->expects($this->once())
            ->method('getCreateSequenceSQLQueries')
            ->will($this->returnValue(array('foo')));

        $this->platformMock
            ->expects($this->once())
            ->method('getRenameDatabaseSQLQueries')
            ->with($this->equalTo($this->schemaDiff))
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
