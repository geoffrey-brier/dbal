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

use Fridge\DBAL\Schema\Comparator\ColumnComparator,
    Fridge\DBAL\Schema\Column,
    Fridge\DBAL\Type\Type;

/**
 * Column comparator test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ColumnComparatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\Schema\Comparator\ColumnComparator */
    protected $columnComparator;

    /** @var \Fridge\DBAL\Schema\Column */
    protected $oldColumn;

    /** @var \Fridge\DBAL\Schema\Column */
    protected $newColumn;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->columnComparator = new ColumnComparator();

        $this->oldColumn = new Column('foo', Type::getType(Type::STRING));
        $this->newColumn = clone $this->oldColumn;
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->columnComparator);
        unset($this->oldColumn);
        unset($this->newColumn);
    }

    public function testCompareWithoutDifference()
    {
        $diff = $this->columnComparator->compare($this->oldColumn, $this->newColumn);

        $this->assertSame($this->oldColumn, $diff->getOldAsset());
        $this->assertSame($this->newColumn, $diff->getNewAsset());
    }

    public function testCompareWithNameDifference()
    {
        $this->newColumn->setName('bar');
        $diff = $this->columnComparator->compare($this->oldColumn, $this->newColumn);

        $this->assertSame($this->oldColumn, $diff->getOldAsset());
        $this->assertSame($this->newColumn, $diff->getNewAsset());
    }

    public function testCompareWithTypeDifference()
    {
        $this->newColumn->setType(Type::getType(Type::TEXT));
        $diff = $this->columnComparator->compare($this->oldColumn, $this->newColumn);

        $this->assertSame($this->oldColumn, $diff->getOldAsset());
        $this->assertSame($this->newColumn, $diff->getNewAsset());
        $this->assertSame(array('type'), $diff->getDifferences());
    }

    public function testCompareWithLengthDifference()
    {
        $this->newColumn->setLength(10);
        $diff = $this->columnComparator->compare($this->oldColumn, $this->newColumn);

        $this->assertSame($this->oldColumn, $diff->getOldAsset());
        $this->assertSame($this->newColumn, $diff->getNewAsset());
        $this->assertSame(array('length'), $diff->getDifferences());
    }

    public function testCompareWithPrecisionDifference()
    {
        $this->newColumn->setPrecision(10);
        $diff = $this->columnComparator->compare($this->oldColumn, $this->newColumn);

        $this->assertSame($this->oldColumn, $diff->getOldAsset());
        $this->assertSame($this->newColumn, $diff->getNewAsset());
        $this->assertSame(array('precision'), $diff->getDifferences());
    }

    public function testCompareWithScaleDifference()
    {
        $this->newColumn->setScale(10);
        $diff = $this->columnComparator->compare($this->oldColumn, $this->newColumn);

        $this->assertSame($this->oldColumn, $diff->getOldAsset());
        $this->assertSame($this->newColumn, $diff->getNewAsset());
        $this->assertSame(array('scale'), $diff->getDifferences());
    }

    public function testCompareWithUnsignedDifference()
    {
        $this->newColumn->setUnsigned(true);
        $diff = $this->columnComparator->compare($this->oldColumn, $this->newColumn);

        $this->assertSame($this->oldColumn, $diff->getOldAsset());
        $this->assertSame($this->newColumn, $diff->getNewAsset());
        $this->assertSame(array('unsigned'), $diff->getDifferences());
    }

    public function testCompareWithFixedDifference()
    {
        $this->newColumn->setFixed(true);
        $diff = $this->columnComparator->compare($this->oldColumn, $this->newColumn);

        $this->assertSame($this->oldColumn, $diff->getOldAsset());
        $this->assertSame($this->newColumn, $diff->getNewAsset());
        $this->assertSame(array('fixed'), $diff->getDifferences());
    }

    public function testCompareWithNotNullDifference()
    {
        $this->newColumn->setNotNull(true);
        $diff = $this->columnComparator->compare($this->oldColumn, $this->newColumn);

        $this->assertSame($this->oldColumn, $diff->getOldAsset());
        $this->assertSame($this->newColumn, $diff->getNewAsset());
        $this->assertSame(array('not_null'), $diff->getDifferences());
    }

    public function testCompareWithDefaultDifference()
    {
        $this->newColumn->setDefault('foo');
        $diff = $this->columnComparator->compare($this->oldColumn, $this->newColumn);

        $this->assertSame($this->oldColumn, $diff->getOldAsset());
        $this->assertSame($this->newColumn, $diff->getNewAsset());
        $this->assertSame(array('default'), $diff->getDifferences());
    }

    public function testCompareWithAutoIncrementDifference()
    {
        $this->newColumn->setAutoIncrement(true);
        $diff = $this->columnComparator->compare($this->oldColumn, $this->newColumn);

        $this->assertSame($this->oldColumn, $diff->getOldAsset());
        $this->assertSame($this->newColumn, $diff->getNewAsset());
        $this->assertSame(array('auto_increment'), $diff->getDifferences());
    }

    public function testCompareWithCommentDifference()
    {
        $this->newColumn->setComment('foo');
        $diff = $this->columnComparator->compare($this->oldColumn, $this->newColumn);

        $this->assertSame($this->oldColumn, $diff->getOldAsset());
        $this->assertSame($this->newColumn, $diff->getNewAsset());
        $this->assertSame(array('comment'), $diff->getDifferences());
    }
}
