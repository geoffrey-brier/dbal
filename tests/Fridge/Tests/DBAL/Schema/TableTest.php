<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\Schema;

use Fridge\DBAL\Schema\Table,
    Fridge\DBAL\Type\Type;

/**
 * Table test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class TableTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\Schema\Table */
    protected $table;

    /** @var \Fridge\DBAL\Schema\Column */
    protected $columnMock;

    /** @var \Fridge\DBAL\Schema\PrimaryKey */
    protected $primaryKeyMock;

    /** @var \Fridge\DBAL\Schema\ForeignKey */
    protected $foreignKeyMock;

    /** @var \Fridge\DBAL\Schema\Index */
    protected $indexMock;

    /** @var \Fridge\DBAL\Schema\Schema */
    protected $schemaMock;

    /** @var \Fridge\DBAL\Schema\Check */
    protected $checkMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->columnMock = $columnMock = $this->getMock('Fridge\DBAL\Schema\Column', array(), array(), '', false);
        $this->columnMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('foo'));

        $this->primaryKeyMock = $this->getMock('Fridge\DBAL\Schema\PrimaryKey', array(), array(), '', false);
        $this->primaryKeyMock
            ->expects($this->any())
            ->method('getColumnNames')
            ->will($this->returnValue(array('foo')));

        $this->foreignKeyMock = $this->getMock('Fridge\DBAL\Schema\ForeignKey', array(), array(), '', false);
        $this->foreignKeyMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('foo'));
        $this->foreignKeyMock
            ->expects($this->any())
            ->method('getLocalColumnNames')
            ->will($this->returnValue(array('foo')));
        $this->foreignKeyMock
            ->expects($this->any())
            ->method('getForeignTableName')
            ->will($this->returnValue('bar'));
        $this->foreignKeyMock
            ->expects($this->any())
            ->method('getForeignColumnNames')
            ->will($this->returnValue(array('bar')));

        $this->indexMock = $this->getMock('Fridge\DBAL\Schema\Index', array(), array(), '', false);
        $this->indexMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('foo'));
        $this->indexMock
            ->expects($this->any())
            ->method('getColumnNames')
            ->will($this->returnValue(array('foo')));

        $this->schemaMock = $this->getMock('Fridge\DBAL\Schema\Schema', array(), array(), '', false);

        $this->checkMock = $this->getMock('Fridge\DBAL\Schema\Check', array(), array(), '', false);
        $this->checkMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('foo'));
        $this->checkMock
            ->expects($this->any())
            ->method('getDefinition')
            ->will($this->returnValue('bar'));

        $this->table = new Table('foo');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->table);
        unset($this->columnMock);
        unset($this->primaryKeyMock);
        unset($this->foreignKeyMock);
        unset($this->indexMock);
        unset($this->schemaMock);
        unset($this->checkMock);
    }

    public function testInitialState()
    {
        $this->assertSame('foo', $this->table->getName());
        $this->assertFalse($this->table->hasSchema());
        $this->assertNull($this->table->getSchema());
        $this->assertFalse($this->table->hasColumns());
        $this->assertEmpty($this->table->getColumns());
        $this->assertFalse($this->table->hasPrimaryKey());
        $this->assertNull($this->table->getPrimaryKey());
        $this->assertFalse($this->table->hasForeignKeys());
        $this->assertEmpty($this->table->getForeignKeys());
        $this->assertFalse($this->table->hasIndexes());
        $this->assertEmpty($this->table->getIndexes());
        $this->assertEmpty($this->table->getChecks());
    }

    public function testSchema()
    {
        $this->schemaMock
            ->expects($this->once())
            ->method('addTable')
            ->with($this->equalTo($this->table));

        $this->table->setSchema($this->schemaMock);

        $this->assertTrue($this->table->hasSchema());
        $this->assertSame($this->schemaMock, $this->table->getSchema());
    }

    public function testColumns()
    {
        $this->table->setColumns(array($this->columnMock));

        $this->assertTrue($this->table->hasColumns());
        $this->assertTrue($this->table->hasColumn('foo'));
        $this->assertSame($this->columnMock, $this->table->getColumn('foo'));
    }

    public function testCreateColumnWithDBALType()
    {
        $column = $this->table->createColumn('foo', Type::getType(Type::STRING), array('length' => 20));

        $this->assertTrue($this->table->hasColumn('foo'));

        $this->assertSame('foo', $column->getName());
        $this->assertSame(Type::STRING, $column->getType()->getName());
        $this->assertSame(20, $column->getLength());
    }

    public function testCreateColumnWithStringType()
    {
        $column = $this->table->createColumn('foo', Type::STRING, array('length' => 20));

        $this->assertTrue($this->table->hasColumn('foo'));

        $this->assertSame('foo', $column->getName());
        $this->assertSame(Type::STRING, $column->getType()->getName());
        $this->assertSame(20, $column->getLength());
    }

    public function testSetColumnsDropPreviousColumns()
    {
        $columnMock = $columnMock = $this->getMock('Fridge\DBAL\Schema\Column', array(), array(), '', false);
        $columnMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('bar'));

        $this->table->setColumns(array($columnMock));

        $this->assertTrue($this->table->hasColumns());
        $this->assertTrue($this->table->hasColumn('bar'));
        $this->assertFalse($this->table->hasColumn('foo'));
        $this->assertSame($columnMock, $this->table->getColumn('bar'));

        $this->table->setColumns(array($this->columnMock));

        $this->assertTrue($this->table->hasColumns());
        $this->assertTrue($this->table->hasColumn('foo'));
        $this->assertFalse($this->table->hasColumn('bar'));
        $this->assertSame($this->columnMock, $this->table->getColumn('foo'));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The column "foo" of the table "foo" does not exist.
     */
    public function testGetColumnWithInvalidName()
    {
        $this->table->getColumn('foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The column "foo" of the table "foo" already exists.
     */
    public function testAddColumnWithInvalidName()
    {
        $this->table->addColumn($this->columnMock);
        $this->table->addColumn($this->columnMock);
    }

    public function testRenameColumn()
    {
        $this->table->addColumn($this->columnMock);
        $this->table->renameColumn('foo', 'bar');

        $this->assertFalse($this->table->hasColumn('foo'));
        $this->assertTrue($this->table->hasColumn('bar'));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     */
    public function testRenameColumnWithInvalidOldName()
    {
        $this->table->addColumn($this->columnMock);
        $this->table->renameColumn('bar', 'foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     */
    public function testRenameColumnWithInvalidNewName()
    {
        $this->table->addColumn($this->columnMock);
        $this->table->renameColumn('foo', 'foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     */
    public function testDropColumnWithInvalidName()
    {
        $this->table->dropColumn('foo');
    }

    public function testPrimaryKey()
    {
        $this->columnMock
            ->expects($this->once())
            ->method('setNotNull')
            ->with($this->equalTo(true));

        $this->table->addColumn($this->columnMock);
        $this->table->setPrimaryKey($this->primaryKeyMock);

        $this->assertTrue($this->table->hasPrimaryKey());
        $this->assertSame($this->primaryKeyMock, $this->table->getPrimaryKey());
    }

    public function testCreatePrimaryKey()
    {
        $this->table->addColumn($this->columnMock);

        $primaryKey = $this->table->createPrimaryKey(array('foo'), 'bar');

        $this->assertTrue($this->table->hasPrimaryKey());

        $this->assertSame('bar', $primaryKey->getName());
        $this->assertSame(array('foo'), $primaryKey->getColumnNames());

        $indexes = array_values($this->table->getIndexes());
        $this->assertSame(array('foo'), $indexes[0]->getColumnNames());
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     */
    public function testPrimaryKeyWithInvalidColumns()
    {
        $this->table->setPrimaryKey($this->primaryKeyMock);
    }

    public function testForeignKeysWithoutSchema()
    {
        $this->table->addColumn($this->columnMock);
        $this->table->setForeignKeys(array($this->foreignKeyMock));

        $this->assertTrue($this->table->hasForeignKeys());
        $this->assertTrue($this->table->hasForeignKey('foo'));
        $this->assertSame($this->foreignKeyMock, $this->table->getForeignKey('foo'));
    }

    public function testForeignKeyWithSchema()
    {
        $foreignTableMock = $this->getMock('Fridge\DBAL\Schema\Table', array(), array('bar', array()));
        $foreignTableMock
            ->expects($this->atLeastOnce())
            ->method('hasColumn')
            ->with($this->equalTo('bar'))
            ->will($this->returnValue(true));

        $this->schemaMock
            ->expects($this->any())
            ->method('getTable')
            ->with($this->equalTo('bar'))
            ->will($this->returnValue($foreignTableMock));

        $this->table->setSchema($this->schemaMock);
        $this->table->addColumn($this->columnMock);
        $this->table->setForeignKeys(array($this->foreignKeyMock));

        $this->assertTrue($this->table->hasForeignKeys());
        $this->assertTrue($this->table->hasForeignKey('foo'));
        $this->assertSame($this->foreignKeyMock, $this->table->getForeignKey('foo'));
    }

    public function testCreateForeignKeyWithForeignTableName()
    {
        $this->table->addColumn($this->columnMock);

        $foreignKey = $this->table->createForeignKey(array('foo'), 'bar', array('bar'), 'foo');

        $this->assertTrue($this->table->hasForeignKey('foo'));

        $this->assertSame('foo', $foreignKey->getName());
        $this->assertSame(array('foo'), $foreignKey->getLocalColumnNames());
        $this->assertSame('bar', $foreignKey->getForeignTableName());
        $this->assertSame(array('bar'), $foreignKey->getForeignColumnNames());

        $indexes = array_values($this->table->getIndexes());
        $this->assertSame(array('foo'), $indexes[0]->getColumnNames());
    }

    public function testCreateForeignKeyWithForeignTable()
    {
        $this->table->addColumn($this->columnMock);

        $foreignKey = $this->table->createForeignKey(array('foo'), new Table('bar'), array('bar'), 'foo');

        $this->assertTrue($this->table->hasForeignKey('foo'));

        $this->assertSame('foo', $foreignKey->getName());
        $this->assertSame(array('foo'), $foreignKey->getLocalColumnNames());
        $this->assertSame('bar', $foreignKey->getForeignTableName());
        $this->assertSame(array('bar'), $foreignKey->getForeignColumnNames());
    }

    public function testSetForeignKeysDropPreviousForeignKeys()
    {
        $columnMock = $this->getMock('Fridge\DBAL\Schema\Column', array(), array(), '', false);
        $columnMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('bar'));

        $foreignKeyMock = $this->getMock('Fridge\DBAL\Schema\ForeignKey', array(), array(), '', false);
        $foreignKeyMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('bar'));
        $foreignKeyMock
            ->expects($this->any())
            ->method('getLocalColumnNames')
            ->will($this->returnValue(array('bar')));

        $this->table->addColumn($columnMock);
        $this->table->setForeignKeys(array($foreignKeyMock));

        $this->assertTrue($this->table->hasForeignKeys());
        $this->assertTrue($this->table->hasForeignKey('bar'));
        $this->assertFalse($this->table->hasForeignKey('foo'));
        $this->assertSame($foreignKeyMock, $this->table->getForeignKey('bar'));

        $this->foreignKeyMock
            ->expects($this->any())
            ->method('getLocalColumnNames')
            ->will($this->returnValue(array('foo')));

        $this->table->addColumn($this->columnMock);
        $this->table->setForeignKeys(array($this->foreignKeyMock));

        $this->assertTrue($this->table->hasForeignKeys());
        $this->assertTrue($this->table->hasForeignKey('foo'));
        $this->assertFalse($this->table->hasForeignKey('bar'));
        $this->assertSame($this->foreignKeyMock, $this->table->getForeignKey('foo'));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The foreign key "foo" of the table "foo" does not exist.
     */
    public function testGetForeignKeyWithInvalidName()
    {
        $this->table->getForeignKey('foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The foreign key "foo" of the table "foo" already exists.
     */
    public function testAddForeignKeyWithInvalidName()
    {
        $this->table->addColumn($this->columnMock);

        $this->table->addForeignKey($this->foreignKeyMock);
        $this->table->addForeignKey($this->foreignKeyMock);
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     */
    public function testAddForeignKeyWithInvalidLocalColumn()
    {
        $this->table->addForeignKey($this->foreignKeyMock);
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     */
    public function testAddForeignKeyWithInvalidForeignColumn()
    {
        $foreignTableMock = $this->getMock('Fridge\DBAL\Schema\Table', array(), array('bar', array()));
        $foreignTableMock
            ->expects($this->atLeastOnce())
            ->method('hasColumn')
            ->with($this->equalTo('bar'))
            ->will($this->returnValue(false));

        $this->schemaMock
            ->expects($this->any())
            ->method('getTable')
            ->with($this->equalTo('bar'))
            ->will($this->returnValue($foreignTableMock));

        $this->table->addColumn($this->columnMock);
        $this->table->setSchema($this->schemaMock);
        $this->table->setForeignKeys(array($this->foreignKeyMock));
    }

    public function testRenameForeignKey()
    {
        $foreignKeyMock = $this->getMock('Fridge\DBAL\Schema\ForeignKey', array(), array(), '', false);
        $foreignKeyMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('bar'));

        $this->table->addColumn($this->columnMock);
        $this->table->addForeignKey($this->foreignKeyMock);

        $this->assertTrue($this->table->hasForeignKey('foo'));
        $this->assertFalse($this->table->hasForeignKey('bar'));

        $this->table->renameForeignKey('foo', 'bar');

        $this->assertFalse($this->table->hasForeignKey('foo'));
        $this->assertTrue($this->table->hasForeignKey('bar'));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     */
    public function testRemaneForeignKeyWithInvalidOldName()
    {
        $this->table->renameForeignKey('foo', 'bar');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     */
    public function testRenameForeignKeyWithInvalidNewName()
    {
        $columnMock = $this->getMock('Fridge\DBAL\Schema\Column', array(), array(), '', false);
        $columnMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('bar'));

        $foreignKeyMock = $this->getMock('Fridge\DBAL\Schema\ForeignKey', array(), array(), '', false);
        $foreignKeyMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('bar'));
        $foreignKeyMock
            ->expects($this->any())
            ->method('getLocalColumnNames')
            ->will($this->returnValue(array('bar')));

        $this->table->addColumn($columnMock);
        $this->table->addForeignKey($foreignKeyMock);

        $this->table->addColumn($this->columnMock);
        $this->table->addForeignKey($this->foreignKeyMock);

        $this->table->renameForeignKey('foo', 'bar');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     */
    public function testDropForeignKeyWithInvalidName()
    {
        $this->table->dropForeignKey('foo');
    }

    public function testIndex()
    {
        $this->table->addColumn($this->columnMock);
        $this->table->setIndexes(array($this->indexMock));

        $this->assertTrue($this->table->hasIndexes());
        $this->assertTrue($this->table->hasIndex('foo'));
        $this->assertSame($this->indexMock, $this->table->getIndex('foo'));
    }

    public function testCreateIndex()
    {
        $this->table->addColumn($this->columnMock);

        $index = $this->table->createIndex(array('foo'), true, 'foo');

        $this->assertTrue($this->table->hasIndex('foo'));

        $this->assertSame('foo', $index->getName());
        $this->assertSame(array('foo'), $index->getColumnNames());
        $this->assertSame(true, $index->isUnique());
    }

    public function testSetIndexesDropPreviousIndexes()
    {
        $this->table->addColumn($this->columnMock);
        $this->table->setIndexes(array($this->indexMock));

        $this->assertTrue($this->table->hasIndexes());
        $this->assertTrue($this->table->hasIndex('foo'));
        $this->assertFalse($this->table->hasIndex('bar'));
        $this->assertSame($this->indexMock, $this->table->getIndex('foo'));

        $indexMock = $this->getMock('Fridge\DBAL\Schema\Index', array(), array(), '', false);
        $indexMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('bar'));
        $indexMock
            ->expects($this->any())
            ->method('getColumnNames')
            ->will($this->returnValue(array('foo')));

        $this->table->setIndexes(array($indexMock));

        $this->assertTrue($this->table->hasIndexes());
        $this->assertTrue($this->table->hasIndex('bar'));
        $this->assertFalse($this->table->hasIndex('foo'));
        $this->assertSame($indexMock, $this->table->getIndex('bar'));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The index "foo" of the table "foo" does not exist.
     */
    public function testGetIndexWithInvalidName()
    {
        $this->table->getIndex('foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The index "foo" of the table "foo" already exists.
     */
    public function testAddIndexWithInvalidName()
    {
        $this->table->addColumn($this->columnMock);

        $this->table->addIndex($this->indexMock);
        $this->table->addIndex($this->indexMock);
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     */
    public function testAddIndexWithInvalidColumn()
    {
        $this->table->addIndex($this->indexMock);
    }

    public function testAddIndexDontAddIndexDueToBetterIndex()
    {
        $indexMock = $this->getMock('Fridge\DBAL\Schema\Index', array(), array(), '', false, false);
        $indexMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('bar'));
        $indexMock
            ->expects($this->any())
            ->method('getColumnNames')
            ->will($this->returnValue(array('foo')));
        $indexMock
            ->expects($this->any())
            ->method('isBetterThan')
            ->will($this->returnValue(true));

        $this->table->addColumn($this->columnMock);

        $this->table->addIndex($indexMock);
        $this->table->addIndex($this->indexMock);

        $this->assertTrue($this->table->hasIndex('bar'));
        $this->assertFalse($this->table->hasIndex('foo'));
    }

    public function testAddIndexDropUselessIndex()
    {
        $indexMock = $this->getMock('Fridge\DBAL\Schema\Index', array(), array(), '', false, false);
        $indexMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('bar'));
        $indexMock
            ->expects($this->any())
            ->method('getColumnNames')
            ->will($this->returnValue(array('foo')));
        $indexMock
            ->expects($this->any())
            ->method('isBetterThan')
            ->will($this->returnValue(true));

        $this->table->addColumn($this->columnMock);
        $this->table->addIndex($this->indexMock);
        $this->table->addIndex($indexMock);

        $this->assertTrue($this->table->hasIndex('bar'));
        $this->assertFalse($this->table->hasIndex('foo'));
    }

    public function testRenameIndex()
    {
        $this->table->addColumn($this->columnMock);
        $this->table->addIndex($this->indexMock);

        $this->assertTrue($this->table->hasIndex('foo'));
        $this->assertFalse($this->table->hasIndex('bar'));

        $this->table->renameIndex('foo', 'bar');

        $this->assertTrue($this->table->hasIndex('bar'));
        $this->assertFalse($this->table->hasIndex('foo'));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     */
    public function testRemaneIndexWithInvalidOldName()
    {
        $this->table->renameIndex('foo', 'bar');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     */
    public function testRenameIndexWithInvalidNewName()
    {
        $indexMock = $this->getMock('Fridge\DBAL\Schema\Index', array(), array(), '', false, false);
        $indexMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('bar'));
        $indexMock
            ->expects($this->any())
            ->method('getColumnNames')
            ->will($this->returnValue(array('foo')));

        $this->table->addColumn($this->columnMock);
        $this->table->addIndex($this->indexMock);
        $this->table->addIndex($indexMock);

        $this->table->renameIndex('foo', 'bar');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     */
    public function testDropIndexWithInvalidValue()
    {
        $this->table->dropIndex('foo');
    }

    public function testChecks()
    {
        $this->table->addCheck($this->checkMock);
        $this->table->setChecks(array($this->checkMock));

        $this->assertTrue($this->table->hasChecks());
        $this->assertTrue($this->table->hasCheck('foo'));
        $this->assertSame($this->checkMock, $this->table->getCheck('foo'));
    }

    public function testCreateCheck()
    {
        $check = $this->table->createCheck('bar', 'foo');

        $this->assertTrue($this->table->hasCheck('foo'));

        $this->assertSame('foo', $check->getName());
        $this->assertSame('bar', $check->getDefinition());
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The check "bar" of the table "foo" does not exist.
     */
    public function testGetCheckWithInvalidName()
    {
        $this->table->getCheck('bar');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The check "foo" of the table "foo" already exists.
     */
    public function testAddCheckWithInvalidName()
    {
        $this->table->addCheck($this->checkMock);
        $this->table->addCheck($this->checkMock);
    }

    public function testRenameCheck()
    {
        $this->table->addCheck($this->checkMock);

        $this->assertTrue($this->table->hasCheck('foo'));
        $this->assertFalse($this->table->hasCheck('bar'));

        $this->table->renameCheck('foo', 'bar');

        $this->assertTrue($this->table->hasCheck('bar'));
        $this->assertFalse($this->table->hasCheck('foo'));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     */
    public function testRemaneCheckWithInvalidOldName()
    {
        $this->table->renameCheck('foo', 'bar');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     */
    public function testRenameCheckWithInvalidNewName()
    {
        $checkMock = $this->getMock('Fridge\DBAL\Schema\Check', array(), array(), '', false, false);
        $checkMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('bar'));
        $checkMock
            ->expects($this->any())
            ->method('getDefinition')
            ->will($this->returnValue(array('foo')));

        $this->table->addCheck($this->checkMock);
        $this->table->addCheck($checkMock);

        $this->table->renameCheck('foo', 'bar');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     */
    public function testDropCheckWithInvalidValue()
    {
        $this->table->dropCheck('foo');
    }

    public function testClone()
    {
        $column1 = $this->table->createColumn('foo', Type::INTEGER);
        $column2 = $this->table->createColumn('bar', Type::INTEGER);

        $primaryKey = $this->table->createPrimaryKey(array('foo'), 'pk');
        $foreignKey = $this->table->createForeignKey(array('bar'), 'bar', array('bar'), 'fk');
        $index = $this->table->createIndex(array('bar'), true, 'idx_fk');
        $check = $this->table->createCheck('foo', 'ck');

        $clone = clone $this->table;

        $this->assertEquals($this->table, $clone);
        $this->assertNotSame($this->table, $clone);

        $this->assertEquals($column1, $clone->getColumn($column1->getName()));
        $this->assertNotSame($column1, $clone->getColumn($column1->getName()));

        $this->assertEquals($column2, $clone->getColumn($column2->getName()));
        $this->assertNotSame($column2, $clone->getColumn($column2->getName()));

        $this->assertEquals($primaryKey, $clone->getPrimaryKey());
        $this->assertNotSame($primaryKey, $clone->getPrimaryKey());

        $this->assertEquals($foreignKey, $clone->getForeignKey($foreignKey->getName()));
        $this->assertNotSame($foreignKey, $clone->getForeignKey($foreignKey->getName()));

        $this->assertEquals($index, $clone->getIndex($index->getName()));
        $this->assertNotSame($index, $clone->getIndex($index->getName()));

        $this->assertEquals($check, $clone->getCheck($check->getName()));
        $this->assertNotSame($check, $clone->getCheck($check->getName()));
    }
}
