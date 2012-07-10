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
        $this->newColumn = new Column('foo', Type::getType(Type::STRING));
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

    public function testCompareWithNameDifference()
    {
        $this->newColumn->setName('bar');
        $diff = $this->columnComparator->compare($this->oldColumn, $this->newColumn);

        $this->assertEquals('foo', $diff->getOldName());
        $this->assertEquals('bar', $diff->getNewName());
    }

    public function testCompareWithTypeDifference()
    {
        $this->newColumn->setType(Type::getType(Type::TEXT));
        $diff = $this->columnComparator->compare($this->oldColumn, $this->newColumn);

        $this->assertEquals($this->newColumn, $diff->getColumn());
    }

    public function testCompareWithLengthDifference()
    {
        $this->newColumn->setLength(10);
        $diff = $this->columnComparator->compare($this->oldColumn, $this->newColumn);

        $this->assertEquals($this->newColumn, $diff->getColumn());
    }

    public function testCompareWithPrecisionDifference()
    {
        $this->newColumn->setPrecision(10);
        $diff = $this->columnComparator->compare($this->oldColumn, $this->newColumn);

        $this->assertEquals($this->newColumn, $diff->getColumn());
    }

    public function testCompareWithScaleDifference()
    {
        $this->newColumn->setScale(10);
        $diff = $this->columnComparator->compare($this->oldColumn, $this->newColumn);

        $this->assertEquals($this->newColumn, $diff->getColumn());
    }

    public function testCompareWithUnsignedDifference()
    {
        $this->newColumn->setUnsigned(true);
        $diff = $this->columnComparator->compare($this->oldColumn, $this->newColumn);

        $this->assertEquals($this->newColumn, $diff->getColumn());
    }

    public function testCompareWithFixedDifference()
    {
        $this->newColumn->setFixed(true);
        $diff = $this->columnComparator->compare($this->oldColumn, $this->newColumn);

        $this->assertEquals($this->newColumn, $diff->getColumn());
    }

    public function testCompareWithNotNullDifference()
    {
        $this->newColumn->setNotNull(true);
        $diff = $this->columnComparator->compare($this->oldColumn, $this->newColumn);

        $this->assertEquals($this->newColumn, $diff->getColumn());
    }

    public function testCompareWithDefaultDifference()
    {
        $this->newColumn->setDefault('foo');
        $diff = $this->columnComparator->compare($this->oldColumn, $this->newColumn);

        $this->assertEquals($this->newColumn, $diff->getColumn());
    }

    public function testCompareWithAutoIncrementDifference()
    {
        $this->newColumn->setAutoIncrement(true);
        $diff = $this->columnComparator->compare($this->oldColumn, $this->newColumn);

        $this->assertEquals($this->newColumn, $diff->getColumn());
    }

    public function testCompareWithCommentDifference()
    {
        $this->newColumn->setComment('foo');
        $diff = $this->columnComparator->compare($this->oldColumn, $this->newColumn);

        $this->assertEquals($this->newColumn, $diff->getColumn());
    }
}
