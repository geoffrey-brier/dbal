<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\Schema\Comparator;

use Fridge\DBAL\Schema,
    Fridge\DBAL\Type\Type;

/**
 * Table comparator test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class TableComparatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\Schema\Comparator\TableComparator */
    protected $tableComparator;

    /** @var \Fridge\DBAL\Schema\Table */
    protected $oldTable;

    /** @var \Fridge\DBAL\Schema\Table */
    protected $newTable;

    /** @var \Fridge\DBAL\Schema\Column */
    protected $oldColumn;

    /** @var \Fridge\DBAL\Schema\Column */
    protected $newColumn;

    /** @var \Fridge\DBAL\Schema\PrimaryKey */
    protected $oldPrimaryKey;

    /** @var \Fridge\DBAL\Schema\PrimaryKey */
    protected $newPrimaryKey;

    /** @var \Fridge\DBAL\Schema\ForeignKey */
    protected $oldForeignKey;

    /** @var \Fridge\DBAL\Schema\ForeignKey */
    protected $newForeignKey;

    /** @var \Fridge\DBAL\Schema\Index */
    protected $oldIndex;

    /** @var \Fridge\DBAL\Schema\Index */
    protected $newIndex;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->tableComparator = new Schema\Comparator\TableComparator();

        $this->oldTable = new Schema\Table('foo');
        $this->newTable = new Schema\Table('foo');

        $this->oldColumn = new Schema\Column('foo', Type::getType(Type::INTEGER));
        $this->newColumn = new Schema\Column('foo', Type::getType(Type::INTEGER));

        $this->oldPrimaryKey = new Schema\PrimaryKey('foo', array('foo'));
        $this->newPrimaryKey = new Schema\PrimaryKey('foo', array('foo'));

        $this->oldForeignKey = new Schema\ForeignKey('foo', array('foo'), 'bar', array('bar'));
        $this->newForeignKey = new Schema\ForeignKey('foo', array('foo'), 'bar', array('bar'));

        $this->oldIndex = new Schema\Index('foo', array('foo'));
        $this->newIndex = new Schema\Index('foo', array('foo'));
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->tableComparator);

        unset($this->oldTable);
        unset($this->newTable);

        unset($this->oldColumn);
        unset($this->newColumn);

        unset($this->oldPrimaryKey);
        unset($this->newPrimaryKey);

        unset($this->oldForeignKey);
        unset($this->newForeignKey);

        unset($this->oldIndex);
        unset($this->newIndex);
    }

    public function testComparePrimaryKeysWithoutDifference()
    {
        $this->assertFalse($this->tableComparator->comparePrimaryKeys($this->oldPrimaryKey, $this->newPrimaryKey));
    }

    public function testComparePrimaryKeysWithNameDifference()
    {
        $this->newPrimaryKey->setName('bar');

        $this->assertTrue($this->tableComparator->comparePrimaryKeys($this->oldPrimaryKey, $this->newPrimaryKey));
    }

    public function testComparePrimaryKeysWithColumnNamesDifference()
    {
        $this->newPrimaryKey->setColumnNames(array('bar'));

        $this->assertTrue($this->tableComparator->comparePrimaryKeys($this->oldPrimaryKey, $this->newPrimaryKey));
    }

    public function testCompareForeignKeysWithoutDifference()
    {
        $this->assertFalse($this->tableComparator->compareForeignKeys($this->oldForeignKey, $this->newForeignKey));
    }

    public function testCompareForeignKeysWithNameDifference()
    {
        $this->newForeignKey->setName('bar');

        $this->assertTrue($this->tableComparator->compareForeignKeys($this->oldForeignKey, $this->newForeignKey));
    }

    public function testCompareForeignKeysWithLocalColumnNamesDifference()
    {
        $this->newForeignKey->setLocalColumnNames(array('bar'));

        $this->assertTrue($this->tableComparator->compareForeignKeys($this->oldForeignKey, $this->newForeignKey));
    }

    public function testCompareForeignKeysWithForeignTableNameDifference()
    {
        $this->newForeignKey->setForeignTableName('foo');

        $this->assertTrue($this->tableComparator->compareForeignKeys($this->oldForeignKey, $this->newForeignKey));
    }

    public function testCompareForeignKeysWithForeignColumnNamesDifference()
    {
        $this->newForeignKey->setForeignColumnNames(array('foo'));

        $this->assertTrue($this->tableComparator->compareForeignKeys($this->oldForeignKey, $this->newForeignKey));
    }

    public function testCompareIndexesWithoutDifference()
    {
        $this->assertFalse($this->tableComparator->compareIndexes($this->oldIndex, $this->newIndex));
    }

    public function testCompareIndexesWithNameDifference()
    {
        $this->newIndex->setName('bar');

        $this->assertTrue($this->tableComparator->compareIndexes($this->oldIndex, $this->newIndex));
    }

    public function testCompareIndexesWithColumnNamesDifference()
    {
        $this->newIndex->setColumnNames(array('bar'));

        $this->assertTrue($this->tableComparator->compareIndexes($this->oldIndex, $this->newIndex));
    }

    public function testCompareIndexesWithUniqueFlagDifference()
    {
        $this->newIndex->setUnique(true);

        $this->assertTrue($this->tableComparator->compareIndexes($this->oldIndex, $this->newIndex));
    }

    public function testCompareWithoutDifference()
    {
        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);

        $this->assertEquals('foo', $tableDiff->getOldName());
        $this->assertEquals('foo', $tableDiff->getNewName());
        $this->assertFalse($tableDiff->hasDifference());
    }

    public function testCompareWithNameDifference()
    {
        $this->newTable->setName('bar');

        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);

        $this->assertEquals('foo', $tableDiff->getOldName());
        $this->assertEquals('bar', $tableDiff->getNewName());
    }

    public function testCompareWithCreatedColumnsDifference()
    {
        $this->newTable->addColumn($this->newColumn);

        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);

        $this->assertEquals(array($this->newColumn), $tableDiff->getCreatedColumns());
    }

    public function testCompareWithAlteredColumnsDifference()
    {
        $this->newColumn->setLength(10);

        $this->oldTable->addColumn($this->oldColumn);
        $this->newTable->addColumn($this->newColumn);

        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);

        $alteredColumns = $tableDiff->getAlteredColumns();

        $this->assertArrayHasKey(0, $alteredColumns);
        $this->assertEquals($this->oldColumn->getName(), $alteredColumns[0]->getOldName());
        $this->assertEquals($this->newColumn->getName(), $alteredColumns[0]->getNewName());
        $this->assertEquals($this->newColumn, $alteredColumns[0]->getColumn());
    }

    public function testCompareWithDroppedColumnsDifference()
    {
        $this->oldTable->addColumn($this->oldColumn);

        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);

        $this->assertEquals(array($this->oldColumn), $tableDiff->getDroppedColumns());
    }

    public function testCompareWithCreatedPrimaryKey()
    {
        $this->newTable->addColumn($this->newColumn);
        $this->newTable->setPrimaryKey($this->newPrimaryKey);

        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);

        $this->assertEquals($this->newPrimaryKey, $tableDiff->getCreatedPrimaryKey());
    }

    public function testCompareWithDroppedPrimaryKey()
    {
        $this->oldTable->addColumn($this->oldColumn);
        $this->oldTable->setPrimaryKey($this->oldPrimaryKey);

        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);

        $this->assertEquals($this->oldPrimaryKey, $tableDiff->getDroppedPrimaryKey());
    }

    public function testCompareWithCreatedForeignKeysDifference()
    {
        $this->newTable->addColumn($this->newColumn);
        $this->newTable->addForeignKey($this->newForeignKey);

        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);

        $this->assertEquals(array($this->newForeignKey), $tableDiff->getCreatedForeignKeys());
    }

    public function testCompareWithAlteredForeignKeysDifference()
    {
        $this->newForeignKey->setForeignTableName('baz');

        $this->oldTable->addColumn($this->oldColumn);
        $this->oldTable->addForeignKey($this->oldForeignKey);

        $this->newTable->addColumn($this->newColumn);
        $this->newTable->addForeignKey($this->newForeignKey);

        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);

        $this->assertEquals(array($this->newForeignKey), $tableDiff->getCreatedForeignKeys());
        $this->assertEquals(array($this->oldForeignKey), $tableDiff->getDroppedForeignKeys());
    }

    public function testCompareWithDroppedForeignKeysDifference()
    {
        $this->oldTable->addColumn($this->oldColumn);
        $this->oldTable->addForeignKey($this->oldForeignKey);

        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);

        $this->assertEquals(array($this->oldForeignKey), $tableDiff->getDroppedForeignKeys());
    }

    public function testCompareWithCreatedIndexesDifference()
    {
        $this->newTable->addColumn($this->oldColumn);
        $this->newTable->addIndex($this->oldIndex);

        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);

        $this->assertEquals(array($this->oldIndex), $tableDiff->getCreatedIndexes());
    }

    public function testCompareWithAlteredIndexesDifference()
    {
        $this->newIndex->setUnique(true);

        $this->oldTable->addColumn($this->oldColumn);
        $this->oldTable->addIndex($this->oldIndex);

        $this->newTable->addColumn($this->oldColumn);
        $this->newTable->addIndex($this->newIndex);

        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);

        $this->assertEquals(array($this->newIndex), $tableDiff->getCreatedIndexes());
        $this->assertEquals(array($this->oldIndex), $tableDiff->getDroppedIndexes());
    }

    public function testCompareWithDroppedIndexesDifference()
    {
        $this->oldTable->addColumn($this->oldColumn);
        $this->oldTable->addIndex($this->oldIndex);

        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);

        $this->assertEquals(array($this->oldIndex), $tableDiff->getDroppedIndexes());
    }
}
