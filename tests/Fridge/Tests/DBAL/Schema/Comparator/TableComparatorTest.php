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

    /** @var \Fridge\DBAL\Schema\Check */
    protected $oldCheck;

    /** @var \Fridge\DBAL\Schema\Check */
    protected $newCheck;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->tableComparator = new Schema\Comparator\TableComparator();

        $this->oldTable = new Schema\Table('foo');
        $this->newTable = clone $this->oldTable;

        $this->oldColumn = new Schema\Column('foo', Type::getType(Type::INTEGER));
        $this->newColumn = clone $this->oldColumn;

        $this->oldPrimaryKey = new Schema\PrimaryKey('foo', array('foo'));
        $this->newPrimaryKey = clone $this->oldPrimaryKey;

        $this->oldForeignKey = new Schema\ForeignKey('foo', array('foo'), 'bar', array('bar'));
        $this->newForeignKey = clone $this->oldForeignKey;

        $this->oldIndex = new Schema\Index('foo', array('foo'));
        $this->newIndex = clone $this->oldIndex;

        $this->oldCheck = new Schema\Check('foo', 'foo');
        $this->newCheck = clone $this->oldCheck;
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

        unset($this->oldCheck);
        unset($this->newCheck);
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

    public function testCompareForeignKeysWithOnDeleteDifference()
    {
        $this->newForeignKey->setOnDelete(Schema\ForeignKey::CASCADE);

        $this->assertTrue($this->tableComparator->compareForeignKeys($this->oldForeignKey, $this->newForeignKey));
    }

    public function testCompareForeignKeysWithOnUpdateDifference()
    {
        $this->newForeignKey->setOnUpdate(Schema\ForeignKey::CASCADE);

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

    public function testCompareCheckWithoutDifference()
    {
        $this->assertFalse($this->tableComparator->compareChecks($this->oldCheck, $this->newCheck));
    }

    public function testCompareCheckWithNameDifference()
    {
        $this->newCheck->setName('bar');

        $this->assertTrue($this->tableComparator->compareChecks($this->oldCheck, $this->newCheck));
    }

    public function testCompareCheckWithDefinitionDifference()
    {
        $this->newCheck->setDefinition('bar');

        $this->assertTrue($this->tableComparator->compareChecks($this->oldCheck, $this->newCheck));
    }

    public function testCompareWithoutDifference()
    {
        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);

        $this->assertFalse($tableDiff->hasDifference());
    }

    public function testCompareWithNameDifference()
    {
        $this->newTable->setName('bar');

        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);

        $this->assertSame($this->oldTable, $tableDiff->getOldAsset());
        $this->assertSame($this->newTable, $tableDiff->getNewAsset());
    }

    public function testCompareWithCreatedColumnsDifference()
    {
        $this->newTable->addColumn($this->newColumn);

        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);

        $this->assertSame(array($this->newColumn), $tableDiff->getCreatedColumns());
    }

    public function testCompareWithRenamedColumnsDifference()
    {
        $this->newColumn->setName('foo1');

        $oldBarColumn = clone $this->oldColumn;
        $oldBarColumn->setName('bar');
        $oldBarColumn->setType(Type::getType(Type::STRING));

        $newBarColumn = clone $oldBarColumn;
        $newBarColumn->setName('bar1');

        $oldBazColumn = clone $this->oldColumn;
        $oldBazColumn->setName('baz');
        $oldBazColumn->setType(Type::getType(Type::BOOLEAN));

        $newBazColumn = clone $oldBazColumn;
        $newBazColumn->setName('baz1');

        $this->oldTable->addColumn($this->oldColumn);
        $this->oldTable->addColumn($oldBarColumn);
        $this->oldTable->addColumn($oldBazColumn);

        $this->newTable->addColumn($newBazColumn);
        $this->newTable->addColumn($newBarColumn);
        $this->newTable->addColumn($this->newColumn);

        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);

        $alteredColumns = $tableDiff->getAlteredColumns();

        $this->assertArrayHasKey(0, $alteredColumns);
        $this->assertSame($oldBazColumn, $alteredColumns[0]->getOldAsset());
        $this->assertSame($newBazColumn, $alteredColumns[0]->getNewAsset());

        $this->assertArrayHasKey(1, $alteredColumns);
        $this->assertSame($oldBarColumn, $alteredColumns[1]->getOldAsset());
        $this->assertSame($newBarColumn, $alteredColumns[1]->getNewAsset());

        $this->assertArrayHasKey(2, $alteredColumns);
        $this->assertSame($this->oldColumn, $alteredColumns[2]->getOldAsset());
        $this->assertSame($this->newColumn, $alteredColumns[2]->getNewAsset());

        $this->assertEmpty($tableDiff->getCreatedColumns());
        $this->assertEmpty($tableDiff->getDroppedColumns());
    }

    public function testCompareWithAlteredColumnsDifference()
    {
        $this->newColumn->setLength(10);

        $this->oldTable->addColumn($this->oldColumn);
        $this->newTable->addColumn($this->newColumn);

        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);

        $alteredColumns = $tableDiff->getAlteredColumns();

        $this->assertArrayHasKey(0, $alteredColumns);
        $this->assertSame($this->oldColumn, $alteredColumns[0]->getOldAsset());
        $this->assertSame($this->newColumn, $alteredColumns[0]->getNewAsset());
    }

    public function testCompareWithDroppedColumnsDifference()
    {
        $this->oldTable->addColumn($this->oldColumn);

        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);

        $this->assertSame(array($this->oldColumn), $tableDiff->getDroppedColumns());
    }

    public function testCompareWithCreatedPrimaryKey()
    {
        $this->newTable->addColumn($this->newColumn);
        $this->newTable->setPrimaryKey($this->newPrimaryKey);

        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);

        $this->assertSame($this->newPrimaryKey, $tableDiff->getCreatedPrimaryKey());
    }

    public function testCompareWithDroppedPrimaryKey()
    {
        $this->oldTable->addColumn($this->oldColumn);
        $this->oldTable->setPrimaryKey($this->oldPrimaryKey);

        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);

        $this->assertSame($this->oldPrimaryKey, $tableDiff->getDroppedPrimaryKey());
    }

    public function testCompareWithCreatedForeignKeysDifference()
    {
        $this->newTable->addColumn($this->newColumn);
        $this->newTable->addForeignKey($this->newForeignKey);

        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);

        $this->assertSame(array($this->newForeignKey), $tableDiff->getCreatedForeignKeys());
    }

    public function testCompareWithAlteredForeignKeysDifference()
    {
        $this->newForeignKey->setForeignTableName('baz');

        $this->oldTable->addColumn($this->oldColumn);
        $this->oldTable->addForeignKey($this->oldForeignKey);

        $this->newTable->addColumn($this->newColumn);
        $this->newTable->addForeignKey($this->newForeignKey);

        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);

        $this->assertSame(array($this->newForeignKey), $tableDiff->getCreatedForeignKeys());
        $this->assertSame(array($this->oldForeignKey), $tableDiff->getDroppedForeignKeys());
    }

    public function testCompareWithDroppedForeignKeysDifference()
    {
        $this->oldTable->addColumn($this->oldColumn);
        $this->oldTable->addForeignKey($this->oldForeignKey);

        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);

        $this->assertSame(array($this->oldForeignKey), $tableDiff->getDroppedForeignKeys());
    }

    public function testCompareWithCreatedIndexesDifference()
    {
        $this->newTable->addColumn($this->oldColumn);
        $this->newTable->addIndex($this->oldIndex);

        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);

        $this->assertSame(array($this->oldIndex), $tableDiff->getCreatedIndexes());
    }

    public function testCompareWithAlteredIndexesDifference()
    {
        $this->newIndex->setUnique(true);

        $this->oldTable->addColumn($this->oldColumn);
        $this->oldTable->addIndex($this->oldIndex);

        $this->newTable->addColumn($this->oldColumn);
        $this->newTable->addIndex($this->newIndex);

        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);

        $this->assertSame(array($this->newIndex), $tableDiff->getCreatedIndexes());
        $this->assertSame(array($this->oldIndex), $tableDiff->getDroppedIndexes());
    }

    public function testCompareWithDroppedIndexesDifference()
    {
        $this->oldTable->addColumn($this->oldColumn);
        $this->oldTable->addIndex($this->oldIndex);

        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);

        $this->assertSame(array($this->oldIndex), $tableDiff->getDroppedIndexes());
    }

    public function testCompareWithCreatedChecksDifference()
    {
        $this->oldTable->addCheck($this->oldCheck);

        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);

        $this->assertSame(array($this->oldCheck), $tableDiff->getDroppedChecks());
    }

    public function testCompareWithAlteredChecksDifference()
    {
        $this->oldTable->addCheck($this->oldCheck);
        $this->newTable->addCheck($this->newCheck);

        $this->newCheck->setDefinition('bar');

        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);

        $this->assertSame(array($this->oldCheck), $tableDiff->getDroppedChecks());
        $this->assertSame(array($this->newCheck), $tableDiff->getCreatedChecks());
    }

    public function testCompareWithDroppedChecksDifference()
    {
        $this->newTable->addCheck($this->newCheck);

        $tableDiff = $this->tableComparator->compare($this->oldTable, $this->newTable);

        $this->assertSame(array($this->newCheck), $tableDiff->getCreatedChecks());
    }
}
