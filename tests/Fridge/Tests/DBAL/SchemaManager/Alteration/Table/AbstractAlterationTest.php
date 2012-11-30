<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\SchemaManager\Alteration\Table;

use Fridge\DBAL\Schema\Comparator\TableComparator,
    Fridge\DBAL\Schema\ForeignKey,
    Fridge\DBAL\Schema\Table,
    Fridge\DBAL\Type\Type,
    Fridge\Tests\DBAL\SchemaManager\Alteration\AbstractAlterationTest as BaseAlteration;

/**
 * Base table alteration test case.
 *
 * All table alteration tests must extend this class.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractAlterationTest extends BaseAlteration
{
    /** @var \Fridge\DBAL\Schema\Comparator\TableComparator */
    protected $tableComparator;

    /** @var \Fridge\DBAL\Schema\Table */
    protected $oldTable;

    /** @var \Fridge\DBAL\Schema\Table */
    protected $newTable;

    /** @var \Fridge\DBAL\Schema\Table */
    protected $oldForeignKeyTable;

    /** @var \Fridge\DBAL\Schema\Table */
    protected $newForeignKeyTable;

    /**
     * {@inheritdoc}
     */
    static public function setUpBeforeClass()
    {
        if (self::$fixture !== null) {
            self::$fixture->createDatabase();
        }
    }

    /**
     * {@inheritdoc}
     */
    static public function tearDownAfterClass()
    {
        if (self::$fixture !== null) {
            self::$fixture->dropDatabase();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->tableComparator = new TableComparator();

        $this->oldTable = new Table('foo');
        $this->oldForeignKeyTable = new Table('bar');
    }

    /**
     * Set up columns.
     */
    protected function setUpColumns()
    {
        $this->oldTable->createColumn('foo', Type::STRING, array('length' => 50));
        $this->oldTable->createColumn('bar', Type::STRING, array('length' => 50));

        $this->setUpTable();
    }

    /**
     * Set up a primary key.
     */
    protected function setUpPrimaryKey()
    {
        $this->oldTable->createColumn('foo', Type::STRING, array('length' => 50));
        $this->oldTable->createColumn('bar', Type::STRING, array('length' => 50));

        $this->oldTable->createPrimaryKey(array('foo'), 'pk');

        $this->setUpTable();
    }

    /**
     * Set up a foreign key.
     */
    protected function setUpForeignKey()
    {
        $this->oldTable->createColumn('foo', Type::STRING, array('length' => 50));
        $this->oldTable->createColumn('bar', Type::STRING, array('length' => 50));
        $this->oldTable->createPrimaryKey(array('foo'), 'pk');

        $this->oldForeignKeyTable->createColumn('foo', Type::STRING, array('length' => 50));
        $this->oldForeignKeyTable->createColumn('bar', Type::STRING, array('length' => 50));
        $this->oldForeignKeyTable->createForeignKey(
            array('foo'),
            'foo',
            array('foo'),
            ForeignKey::RESTRICT,
            ForeignKey::RESTRICT,
            'fk_foo'
        );

        $this->setUpForeignKeyTable();
    }

    /**
     * Set up an index.
     */
    protected function setUpIndex()
    {
        $this->oldTable->createColumn('foo', Type::STRING, array('length' => 50));
        $this->oldTable->createColumn('bar', Type::STRING, array('length' => 50));

        $this->oldTable->createIndex(array('foo'), false, 'idx_foo');

        $this->setUpTable();
    }

    /**
     * Set up a check.
     */
    protected function setUpCheck()
    {
        $this->oldTable->createColumn('foo', Type::INTEGER);
        $this->oldTable->createColumn('bar', Type::INTEGER);
        $this->oldTable->createCheck('foo > 0', 'ck_foo');

        $this->setUpTable();
    }

    /**
     * Set up a table.
     */
    protected function setUpTable()
    {
        $this->newTable = clone $this->oldTable;

        $this->connection->getSchemaManager()->createTable($this->oldTable);
    }

    /**
     * Set up a foreign key table.
     */
    protected function setUpForeignKeyTable()
    {
        $this->newTable = clone $this->oldTable;
        $this->newForeignKeyTable = clone $this->oldForeignKeyTable;

        $this->connection->getSchemaManager()->createTables(array($this->oldTable, $this->oldForeignKeyTable));
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        if (($this->connection !== null) && ($this->newTable !== null)) {
            if ($this->newForeignKeyTable === null) {
                $this->connection->getSchemaManager()->dropTable($this->newTable);
            } else {
                $this->connection->getSchemaManager()->dropTables(array($this->newTable, $this->newForeignKeyTable));
            }
        }

        parent::tearDown();

        unset($this->tableComparator);

        unset($this->oldTable);
        unset($this->newTable);

        unset($this->oldForeignKeyTable);
        unset($this->newForeignKeyTable);
    }

    public function testRename()
    {
        $this->setUpColumns();
        $this->newTable->setName('baz');

        $this->assertAlteration();
    }

    public function testCreateColumn()
    {
        $this->setUpColumns();
        $this->newTable->createColumn('baz', Type::getType(Type::TEXT));

        $this->assertAlteration();
    }

    public function testAlterColumn()
    {
        $this->setUpColumns();
        $this->newTable->getColumn('foo')->setNotNull(true);

        $this->assertAlteration();
    }

    public function testDropColumn()
    {
        $this->setUpColumns();
        $this->newTable->dropColumn('bar');

        $this->assertAlteration();
    }

    public function testCreatePrimaryKey()
    {
        $this->setUpColumns();
        $this->newTable->createPrimaryKey(array('foo'), 'pk');

        $this->assertAlteration();
    }

    public function testAlterPrimaryKey()
    {
        $this->setUpPrimaryKey();

        $this->newTable->dropPrimaryKey();
        $this->newTable->createPrimaryKey(array('bar'), 'pk');

        $this->assertAlteration();
    }

    public function testDropPrimaryKey()
    {
        $this->setUpPrimaryKey();
        $this->newTable->dropPrimaryKey();

        $this->assertAlteration();
    }

    public function testCreateForeignKey()
    {
        $this->setUpForeignKey();
        $this->newForeignKeyTable->createForeignKey(
            array('bar'),
            'foo',
            array('foo'),
            ForeignKey::RESTRICT,
            ForeignKey::RESTRICT,
            'fk_bar'
        );

        $this->assertForeignKeyAlteration();
    }

    public function testAlterForeignKey()
    {
        $this->setUpForeignKey();

        $this->newForeignKeyTable->dropForeignKey('fk_foo');
        $this->newForeignKeyTable->dropIndex('idx_fk_foo');

        $this->newForeignKeyTable->createForeignKey(
            array('bar'),
            'foo',
            array('foo'),
            ForeignKey::RESTRICT,
            ForeignKey::RESTRICT,
            'fk_foo'
        );

        $this->assertForeignKeyAlteration();
    }

    public function testDropForeignKey()
    {
        $this->setUpForeignKey();
        $this->newForeignKeyTable->dropForeignKey('fk_foo');

        $this->assertForeignKeyAlteration();
    }

    public function testCreateIndex()
    {
        $this->setUpIndex();
        $this->newTable->createIndex(array('bar'), false, 'idx_bar');

        $this->assertAlteration();
    }

    public function testAlterIndex()
    {
        $this->setUpIndex();
        $this->newTable->getIndex('idx_foo')->setUnique(true);

        $this->assertAlteration();
    }

    public function testDropIndex()
    {
        $this->setUpIndex();
        $this->newTable->dropIndex('idx_foo');

        $this->assertAlteration();
    }

    public function testCreateCheck()
    {
        $this->setUpCheck();
        $this->newTable->createCheck('bar > 0', 'ck_bar');

        $this->assertAlteration();
    }

    public function testAlterCheck()
    {
        $this->setUpCheck();
        $this->newTable->getCheck('ck_foo')->setDefinition('foo > 10');

        $this->assertAlteration();
    }

    public function testDropCheck()
    {
        $this->setUpCheck();
        $this->newTable->dropCheck('ck_foo');

        $this->assertAlteration();
    }

    /**
     * Asserts the old table is altered.
     */
    protected function assertAlteration()
    {
        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);
        $this->connection->getSchemaManager()->alterTable($tableDiff);

        $this->assertEquals(
            $this->newTable,
            $this->connection->getSchemaManager()->getTable($this->newTable->getName())
        );
    }

    /**
     * Asserts the old foreign key table is altered.
     */
    protected function assertForeignKeyAlteration()
    {
        $tableDiff = $this->tableComparator->compare($this->oldForeignKeyTable, $this->newForeignKeyTable);
        $this->connection->getSchemaManager()->alterTable($tableDiff);

        $this->assertEquals(
            $this->newForeignKeyTable,
            $this->connection->getSchemaManager()->getTable($this->newForeignKeyTable->getName())
        );
    }
}
