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

use Fridge\DBAL\Schema\Index;

/**
 * Index test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class IndexTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\Schema\Index */
    protected $index;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->index = new Index('foo');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->index);
    }

    public function testInitialState()
    {
        $this->assertSame('foo', $this->index->getName());
        $this->assertEmpty($this->index->getColumnNames());
        $this->assertFalse($this->index->isUnique());
    }

    public function testGenerateName()
    {
        $index = new Index(null, array(), false);
        $this->assertRegExp('/^idx_[a-z0-9]{16}$/', $index->getName());
    }

    public function testColumnNamesWithValidValues()
    {
        $columnNames = array('foo', 'bar');
        $this->index->setColumnNames($columnNames);

        $this->assertSame($columnNames, $this->index->getColumnNames());
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The column name of the index "foo" must be a string.
     */
    public function testColumnNamesWithInvalidValues()
    {
        $this->index->setColumnNames(array(true));
    }

    public function testUniqueFlagWithValidValue()
    {
        $this->index->setUnique(true);
        $this->assertTrue($this->index->isUnique());

        $this->index->setUnique(false);
        $this->assertFalse($this->index->isUnique());
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The unique flag of the index "foo" must be a boolean.
     */
    public function testUniqueFlagWithInvalidValue()
    {
        $this->index->setUnique('foo');
    }

    public function testHasSameColumnsWithSameColumns()
    {
        $columnNames = array('foo', 'bar');

        $this->index->setColumnNames($columnNames);
        $this->assertTrue($this->index->hasSameColumnNames($columnNames));
    }

    public function testHasSameColumnNamesWithoutSameColumnNames()
    {
        $this->index->setColumnNames(array('foo'));
        $this->assertFalse($this->index->hasSameColumnNames(array('foo', 'bar')));
    }

    public function testHasSameColumnsWithSameColumnsInversed()
    {
        $columnNames = array('foo', 'bar');
        $this->index->setColumnNames($columnNames);

        $this->assertFalse($this->index->hasSameColumnNames(array_reverse($columnNames, false)));
    }

    public function testIsBetterThanWhenIndexIsBetter()
    {
        $columnNames = array('foo', 'bar');

        $this->index->setColumnNames($columnNames);
        $this->index->setUnique(true);

        $index = new Index(null, $columnNames);

        $this->assertTrue($this->index->isBetterThan($index));
    }

    public function testIsBetterThanWithInvalidColumnsCount()
    {
        $this->index->setColumnNames(array('foo'));
        $index = new Index(null, array('foo', 'bar'));

        $this->assertFalse($this->index->isBetterThan($index));
    }

    public function testIsBetterThanWithSameNonUniqueIndex()
    {
        $this->index->setColumnNames(array('foo'));
        $this->assertFalse($this->index->isBetterThan($this->index));
    }

    public function testIsBetterThanWithSameUniqueIndex()
    {
        $this->index->setColumnNames(array('foo'));
        $this->index->setUnique(true);

        $this->assertFalse($this->index->isBetterThan($this->index));
    }
}
