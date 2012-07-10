<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\SchemaManager\Alteration\Column;

use Fridge\DBAL\Schema,
    Fridge\DBAL\Type\Type,
    Fridge\Tests\DBAL\SchemaManager\Alteration\AbstractAlterationTest as BaseAlteration;

/**
 * Base column alteration test case.
 *
 * All column alteration tests must extend this class.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractAlterationTest extends BaseAlteration
{
    /** @var \Fridge\DBAL\Schema\Comparator\ColumnComparator */
    protected $columnComparator;

    /** @var \Fridge\DBAL\Schema\Table */
    protected $table;

    /** @var \Fridge\DBAL\Schema\Column */
    protected $oldColumn;

    /** @var \Fridge\DBAL\Schema\Column */
    protected $newColumn;

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

        $this->columnComparator = new Schema\Comparator\ColumnComparator();

        $this->oldColumn = new Schema\Column('foo', Type::getType(Type::STRING));
        $this->table = new Schema\Table('foo', array($this->oldColumn));
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        if ($this->connection !== null) {
            $this->connection->getSchemaManager()->dropTable($this->table);
        }

        parent::tearDown();

        unset($this->columnComparator);
        unset($this->table);
        unset($this->oldColumn);
        unset($this->newColumn);
    }

    /**
     * Set up a string column.
     */
    protected function setUpStringColumn()
    {
        $this->oldColumn->setLength(50);

        $this->setUpColumn();
    }

    /**
     * Set up a decimal column.
     */
    protected function setUpDecimalColumn()
    {
        $this->oldColumn->setType(Type::getType(Type::DECIMAL));
        $this->oldColumn->setPrecision(10);
        $this->oldColumn->setScale(2);

        $this->setUpColumn();
    }

    /**
     * Set up a column.
     */
    protected function setUpColumn()
    {
        $this->newColumn = clone $this->oldColumn;

        $this->connection->getSchemaManager()->createTable($this->table);
    }

    public function testRename()
    {
        $this->setUpStringColumn();
        $this->newColumn->setName('bar');

        $this->assertAlteration();
    }

    public function testType()
    {
        $this->setUpStringColumn();

        $this->newColumn->setType(Type::getType(Type::TEXT));
        $this->newColumn->setLength(null);

        $this->assertAlteration();
    }

    public function testMandatoryType()
    {
        $this->setUpStringColumn();

        $this->newColumn->setType(Type::getType(Type::TARRAY));
        $this->newColumn->setLength(null);

        $this->assertAlteration();
    }

    public function testLength()
    {
        $this->setUpStringColumn();
        $this->newColumn->setLength(100);

        $this->assertAlteration();
    }

    public function testPrecision()
    {
        $this->setUpDecimalColumn();
        $this->newColumn->setPrecision(8);

        $this->assertAlteration();
    }

    public function testScale()
    {
        $this->setUpDecimalColumn();
        $this->newColumn->setScale(3);

        $this->assertAlteration();
    }

    public function testSetNotNull()
    {
        $this->setUpStringColumn();
        $this->newColumn->setNotNull(true);

        $this->assertAlteration();
    }

    public function testDropNotNull()
    {
        $this->oldColumn->setNotNull(true);
        $this->oldColumn->setLength(50);

        $this->setUpColumn();

        $this->newColumn->setNotNull(false);

        $this->assertAlteration();
    }

    public function testSetDefault()
    {
        $this->setUpStringColumn();
        $this->newColumn->setDefault('foo');

        $this->assertAlteration();
    }

    public function testDropDefault()
    {
        $this->oldColumn->setDefault('foo');
        $this->oldColumn->setLength(50);

        $this->setUpColumn();

        $this->newColumn->setDefault(null);

        $this->assertAlteration();
    }

    public function testSetComment()
    {
        $this->setUpStringColumn();
        $this->newColumn->setComment('foo');

        $this->assertAlteration();
    }

    public function testDropComment()
    {
        $this->oldColumn->setComment('foo');
        $this->oldColumn->setLength(50);

        $this->setUpColumn();

        $this->newColumn->setComment(null);

        $this->assertAlteration();
    }

    /**
     * Asserts the old column is altered.
     */
    protected function assertAlteration()
    {
        $columnDiff = $this->columnComparator->compare($this->oldColumn, $this->newColumn);
        $this->connection->getSchemaManager()->alterColumn($columnDiff, $this->table->getName());

        $this->assertEquals(
            array($this->newColumn),
            $this->connection->getSchemaManager()->getTableColumns($this->table->getName())
        );
    }
}
