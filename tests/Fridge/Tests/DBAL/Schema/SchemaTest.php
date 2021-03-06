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

use Fridge\DBAL\Schema\Schema,
    Fridge\DBAL\Type\Type;

/**
 * Schema test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class SchemaTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\Schema\Schema */
    protected $schema;

    /** @var \Fridge\DBAL\Schema\Table */
    protected $tableMock;

    /** @var \Fridge\DBAL\Schema\Sequence */
    protected $sequenceMock;

    /** @var \Fridge\DBAL\Schema\View */
    protected $viewMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->tableMock = $this->getMock('Fridge\DBAL\Schema\Table', array(), array(), '', false);
        $this->tableMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('foo'));

        $this->sequenceMock = $this->getMock('Fridge\DBAL\Schema\Sequence', array(), array(), '', false);
        $this->sequenceMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('foo'));

        $this->viewMock = $this->getMock('Fridge\DBAL\Schema\View', array(), array(), '', false);
        $this->viewMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('foo'));

        $this->schema = new Schema('foo');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->schema);
        unset($this->tableMock);
        unset($this->sequenceMock);
        unset($this->viewMock);
    }

    public function testInitialState()
    {
        $this->assertEquals('foo', $this->schema->getName());
        $this->assertFalse($this->schema->hasTables());
        $this->assertEmpty($this->schema->getTables());
        $this->assertFalse($this->schema->hasSequences());
        $this->assertEmpty($this->schema->getSequences());
        $this->assertFalse($this->schema->hasViews());
        $this->assertEmpty($this->schema->getViews());
    }

    public function testTable()
    {
        $this->tableMock
            ->expects($this->once())
            ->method('setSchema')
            ->with($this->equalTo($this->schema));

        $this->schema->setTables(array($this->tableMock));

        $this->assertTrue($this->schema->hasTable('foo'));
        $this->assertEquals($this->tableMock, $this->schema->getTable('foo'));
    }

    public function testCreateTableWithFullOptions()
    {
        $table = $this->schema->createTable(
            'foo',
            array(
                array('name' => 'foo', 'type' => Type::STRING, 'options' => array('length' => 20)),
                array('name' => 'bar', 'type' => Type::STRING),
            ),
            array('name' => 'foo', 'columns' => array('foo')),
            array(
                array('name' => 'foo', 'local_columns' => array('foo'), 'foreign_table' => 'bar', 'foreign_columns' => array('bar')),
            ),
            array(
                array('name' => 'foo', 'columns' => array('bar'), 'unique' => true),
            )
        );

        $this->assertTrue($this->schema->hasTable('foo'));

        $this->assertEquals('foo', $table->getName());

        $this->assertTrue($table->hasColumn('foo'));
        $this->assertEquals(Type::STRING, $table->getColumn('foo')->getType()->getName());
        $this->assertEquals(20, $table->getColumn('foo')->getLength());

        $this->assertTrue($table->hasPrimaryKey());
        $this->assertEquals('foo', $table->getPrimaryKey()->getName());
        $this->assertEquals(array('foo'), $table->getPrimaryKey()->getColumnNames());

        $this->assertTrue($table->hasForeignKey('foo'));
        $this->assertEquals('foo', $table->getForeignKey('foo')->getName());
        $this->assertEquals(array('foo'), $table->getForeignKey('foo')->getLocalColumnNames());
        $this->assertEquals('bar', $table->getForeignKey('foo')->getForeignTableName());
        $this->assertEquals(array('bar'), $table->getForeignKey('foo')->getForeignColumnNames());

        $this->assertTrue($table->hasIndex('foo'));
        $this->assertEquals(array('bar'), $table->getIndex('foo')->getColumnNames());
        $this->assertTrue($table->getIndex('foo')->isUnique());
    }

    public function testCreateTableWithMinimalOptions()
    {
        $table = $this->schema->createTable(
            'foo',
            array(
                array('name' => 'foo', 'type' => Type::STRING),
                array('name' => 'bar', 'type' => Type::STRING),
            ),
            array('columns' => array('foo')),
            array(
                array('local_columns' => array('foo'), 'foreign_table' => 'bar', 'foreign_columns' => array('bar')),
            ),
            array(
                array('columns' => array('bar')),
            )
        );

        $this->assertTrue($table->hasPrimaryKey());
        $this->assertEquals(array('foo'), $table->getPrimaryKey()->getColumnNames());

        $foreignKeys = array_values($table->getForeignKeys());
        $this->assertEquals(array('foo'), $foreignKeys[0]->getLocalColumnNames());
        $this->assertEquals('bar', $foreignKeys[0]->getForeignTableName());
        $this->assertEquals(array('bar'), $foreignKeys[0]->getForeignColumnNames());

        $indexes = array_values($table->getIndexes());

        $this->assertEquals(array('foo'), $indexes[0]->getColumnNames());
        $this->assertTrue($indexes[0]->isUnique());

        $this->assertEquals(array('bar'), $indexes[1]->getColumnNames());
        $this->assertFalse($indexes[1]->isUnique());
    }

    public function testSetTablesDropPreviousTables()
    {
        $tableMock = $this->getMock('Fridge\DBAL\Schema\Table', array(), array(), '', false);
        $tableMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('bar'));

        $this->schema->setTables(array($this->tableMock));

        $this->assertTrue($this->schema->hasTable('foo'));
        $this->assertFalse($this->schema->hasTable('bar'));

        $this->schema->setTables(array($tableMock));

        $this->assertTrue($this->schema->hasTable('bar'));
        $this->assertFalse($this->schema->hasTable('foo'));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The table "foo" of the schema "foo" does not exist.
     */
    public function testGetTableWithInvalidName()
    {
        $this->schema->getTable('foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The table "foo" of the schema "foo" already exists.
     */
    public function testAddTableWithInvalidName()
    {
        $this->schema->addTable($this->tableMock);
        $this->schema->addTable($this->tableMock);
    }

    public function testRenameTable()
    {
        $this->schema->addTable($this->tableMock);

        $this->assertTrue($this->schema->hasTable('foo'));
        $this->assertFalse($this->schema->hasTable('bar'));

        $this->schema->renameTable('foo', 'bar');

        $this->assertTrue($this->schema->hasTable('bar'));
        $this->assertFalse($this->schema->hasTable('foo'));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     */
    public function testRemaneTableWithInvalidOldName()
    {
        $this->schema->renameTable('foo', 'bar');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     */
    public function testRenameTableWithInvalidNewName()
    {
        $tableMock = $this->getMock('Fridge\DBAL\Schema\Table', array(), array(), '', false);
        $tableMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('bar'));

        $this->schema->addTable($tableMock);
        $this->schema->addTable($this->tableMock);

        $this->schema->renameTable('foo', 'bar');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The table "foo" of the schema "foo" does not exist.
     */
    public function testDropTableWithInvalidName()
    {
        $this->schema->dropTable('foo');
    }

    public function testSequence()
    {
        $this->schema->setSequences(array($this->sequenceMock));

        $this->assertTrue($this->schema->hasSequence('foo'));
        $this->assertEquals($this->sequenceMock, $this->schema->getSequence('foo'));
    }

    public function testCreateSequence()
    {
        $sequence = $this->schema->createSequence('foo', 2, 3);

        $this->assertTrue($this->schema->hasSequence('foo'));

        $this->assertEquals('foo', $sequence->getName());
        $this->assertEquals(2, $sequence->getInitialValue());
        $this->assertEquals(3, $sequence->getIncrementSize());
    }

    public function testSetSequencesDropPreviousSequences()
    {
        $sequenceMock = $this->getMock('Fridge\DBAL\Schema\Sequence', array(), array(), '', false);
        $sequenceMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('bar'));

        $this->schema->setSequences(array($this->sequenceMock));

        $this->assertTrue($this->schema->hasSequence('foo'));
        $this->assertFalse($this->schema->hasSequence('bar'));

        $this->schema->setSequences(array($sequenceMock));

        $this->assertTrue($this->schema->hasSequence('bar'));
        $this->assertFalse($this->schema->hasSequence('foo'));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The sequence "foo" of the schema "foo" does not exist.
     */
    public function testGetSequenceWithInvalidName()
    {
        $this->schema->getSequence('foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The sequence "foo" of the schema "foo" already exists.
     */
    public function testAddSequenceWithValidName()
    {
        $this->schema->addSequence($this->sequenceMock);
        $this->schema->addSequence($this->sequenceMock);
    }

    public function testRenameSequence()
    {
        $this->schema->addSequence($this->sequenceMock);

        $this->assertTrue($this->schema->hasSequence('foo'));
        $this->assertFalse($this->schema->hasSequence('bar'));

        $this->schema->renameSequence('foo', 'bar');

        $this->assertTrue($this->schema->hasSequence('bar'));
        $this->assertFalse($this->schema->hasSequence('foo'));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     */
    public function testRemaneSequenceWithInvalidOldName()
    {
        $this->schema->renameSequence('foo', 'bar');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     */
    public function testRenameSequenceWithInvalidNewName()
    {
        $sequenceMock = $this->getMock('Fridge\DBAL\Schema\Sequence', array(), array(), '', false);
        $sequenceMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('bar'));

        $this->schema->addSequence($this->sequenceMock);
        $this->schema->addSequence($sequenceMock);

        $this->schema->renameSequence('foo', 'bar');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     */
    public function testDropSequenceWithInvalidValue()
    {
        $this->schema->dropSequence('foo');
    }

    public function testView()
    {
        $this->schema->setViews(array($this->viewMock));

        $this->assertTrue($this->schema->hasView('foo'));
        $this->assertEquals($this->viewMock, $this->schema->getView('foo'));
    }

    public function testCreateView()
    {
        $view = $this->schema->createView('foo', 'bar');

        $this->assertTrue($this->schema->hasView('foo'));

        $this->assertEquals('foo', $view->getName());
        $this->assertEquals('bar', $view->getSQL());
    }

    public function testSetViewsDropPreviousViews()
    {
        $viewMock = $this->getMock('Fridge\DBAL\Schema\View', array(), array(), '', false);
        $viewMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('bar'));

        $this->schema->setViews(array($this->viewMock));

        $this->assertTrue($this->schema->hasView('foo'));
        $this->assertFalse($this->schema->hasView('bar'));

        $this->schema->setViews(array($viewMock));

        $this->assertTrue($this->schema->hasView('bar'));
        $this->assertFalse($this->schema->hasView('foo'));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The view "foo" of the schema "foo" does not exist.
     */
    public function testGetViewWithInvalidName()
    {
        $this->schema->getView('foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The view "foo" of the schema "foo" already exists.
     */
    public function testAddViewWithInvalidName()
    {
        $this->schema->addView($this->viewMock);
        $this->schema->addView($this->viewMock);
    }

    public function testRenameView()
    {
        $this->schema->addView($this->viewMock);

        $this->assertTrue($this->schema->hasView('foo'));
        $this->assertFalse($this->schema->hasView('bar'));

        $this->schema->renameView('foo', 'bar');

        $this->assertTrue($this->schema->hasView('bar'));
        $this->assertFalse($this->schema->hasView('foo'));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     */
    public function testRemaneViewWithInvalidOldName()
    {
        $this->schema->renameView('foo', 'bar');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     */
    public function testRenameViewWithInvalidNewName()
    {
        $viewMock = $this->getMock('Fridge\DBAL\Schema\View', array(), array(), '', false);
        $viewMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('bar'));

        $this->schema->addView($this->viewMock);
        $this->schema->addView($viewMock);

        $this->schema->renameView('foo', 'bar');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     */
    public function testDropViewWithInvalidValue()
    {
        $this->schema->dropView('foo');
    }

    public function testClone()
    {
        $this->schema->addTable($this->tableMock);
        $this->schema->addSequence($this->sequenceMock);
        $this->schema->addView($this->viewMock);

        $clone = clone $this->schema;

        $this->assertEquals($this->schema, $clone);
        $this->assertNotSame($this->schema, $clone);

        $this->assertEquals($this->tableMock, $clone->getTable($this->tableMock->getName()));
        $this->assertNotSame($this->tableMock, $clone->getTable($this->tableMock->getName()));

        $this->assertEquals($this->sequenceMock, $clone->getSequence($this->sequenceMock->getName()));
        $this->assertNotSame($this->sequenceMock, $clone->getSequence($this->sequenceMock->getName()));

        $this->assertEquals($this->viewMock, $clone->getView($this->viewMock->getName()));
        $this->assertNotSame($this->viewMock, $clone->getView($this->viewMock->getName()));
    }
}
