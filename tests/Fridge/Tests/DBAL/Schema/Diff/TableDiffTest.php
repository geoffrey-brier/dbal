<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\Schema\Diff;

use Fridge\DBAL\Schema\Diff\TableDiff;

/**
 * Table diff test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class TableDiffTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\Schema\Table */
    protected $oldTableMock;

    /** @var \Fridge\DBAL\Schema\Table */
    protected $newTableMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->oldTableMock = $this->getMock('Fridge\DBAL\Schema\Table', array(), array(), '', false);
        $this->oldTableMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('foo'));

        $this->newTableMock = $this->getMock('Fridge\DBAL\Schema\Table', array(), array(), '', false);
        $this->newTableMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('foo'));
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->oldTableMock);
        unset($this->newTableMock);
    }

    public function testInitialState()
    {
        $createdPrimaryKey = $this->getMock('Fridge\DBAL\Schema\PrimaryKey', array(), array(), '', false);
        $droppedPrimaryKey = $this->getMock('Fridge\DBAL\Schema\PrimaryKey', array(), array(), '', false);

        $tableDiff = new TableDiff(
            $this->oldTableMock,
            $this->newTableMock,
            array('createdColumn'),
            array('alteredColumn'),
            array('droppedColumn'),
            $createdPrimaryKey,
            $droppedPrimaryKey,
            array('createdForeignKey'),
            array('droppedForeignKey'),
            array('createdIndex'),
            array('droppedIndex'),
            array('createdCheck'),
            array('droppedCheck')
        );

        $this->assertSame($this->oldTableMock, $tableDiff->getOldAsset());
        $this->assertSame($this->newTableMock, $tableDiff->getNewAsset());

        $this->assertSame(array('createdColumn'), $tableDiff->getCreatedColumns());
        $this->assertSame(array('alteredColumn'), $tableDiff->getAlteredColumns());
        $this->assertSame(array('droppedColumn'), $tableDiff->getDroppedColumns());

        $this->assertSame($createdPrimaryKey, $tableDiff->getCreatedPrimaryKey());
        $this->assertSame($droppedPrimaryKey, $tableDiff->getDroppedPrimaryKey());

        $this->assertSame(array('createdForeignKey'), $tableDiff->getCreatedForeignKeys());
        $this->assertSame(array('droppedForeignKey'), $tableDiff->getDroppedForeignKeys());

        $this->assertSame(array('createdIndex'), $tableDiff->getCreatedIndexes());
        $this->assertSame(array('droppedIndex'), $tableDiff->getDroppedIndexes());

        $this->assertSame(array('createdCheck'), $tableDiff->getCreatedChecks());
        $this->assertSame(array('droppedCheck'), $tableDiff->getDroppedChecks());
    }

    public function testDifferenceWithoutDifference()
    {
        $tableDiff = new TableDiff($this->oldTableMock, $this->newTableMock);

        $this->assertFalse($tableDiff->hasDifference());
        $this->assertFalse($tableDiff->hasNameDifference());
        $this->assertFalse($tableDiff->hasNameDifferenceOnly());
    }

    public function testDifferenceWithNameDifference()
    {
        $this->newTableMock = $this->getMock('Fridge\DBAL\Schema\Table', array(), array(), '', false);
        $this->newTableMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('bar'));

        $tableDiff = new TableDiff($this->oldTableMock, $this->newTableMock);

        $this->assertTrue($tableDiff->hasDifference());
        $this->assertTrue($tableDiff->hasNameDifference());
        $this->assertTrue($tableDiff->hasNameDifferenceOnly());
    }

    public function testDifferenceWithoutNameDifferenceOnly()
    {
        $this->newTableMock = $this->getMock('Fridge\DBAL\Schema\Table', array(), array(), '', false);
        $this->newTableMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('bar'));

        $tableDiff = new TableDiff($this->oldTableMock, $this->newTableMock, array('foo'));

        $this->assertfalse($tableDiff->hasNameDifferenceOnly());
    }

    public function testDifferenceWithCreatedColumnsDifference()
    {
        $tableDiff = new TableDiff($this->oldTableMock, $this->newTableMock, array('foo'));

        $this->assertTrue($tableDiff->hasDifference());
    }

    public function testDifferenceWithAlteredColumnsDifference()
    {
        $tableDiff = new TableDiff($this->oldTableMock, $this->newTableMock, array(), array('foo'));

        $this->assertTrue($tableDiff->hasDifference());
    }

    public function testDifferenceWithDroppedColumnsDifference()
    {
        $tableDiff = new TableDiff($this->oldTableMock, $this->newTableMock, array(), array(), array('foo'));

        $this->assertTrue($tableDiff->hasDifference());
    }

    public function testDifferenceWithCreatedPrimaryKey()
    {
        $primaryKey = $this->getMock('Fridge\DBAL\Schema\PrimaryKey', array(), array(), '', false);

        $tableDiff = new TableDiff($this->oldTableMock, $this->newTableMock, array(), array(), array(), $primaryKey);

        $this->assertTrue($tableDiff->hasDifference());
    }

    public function testDifferenceWithDroppedPrimaryKey()
    {
        $primaryKey = $this->getMock('Fridge\DBAL\Schema\PrimaryKey', array(), array(), '', false);

        $tableDiff = new TableDiff(
            $this->oldTableMock,
            $this->newTableMock,
            array(),
            array(),
            array(),
            null,
            $primaryKey
        );

        $this->assertTrue($tableDiff->hasDifference());
    }

    public function testDifferenceWithCreatedForeignKeysDifference()
    {
        $tableDiff = new TableDiff(
            $this->oldTableMock,
            $this->newTableMock,
            array(),
            array(),
            array(),
            null,
            null,
            array('foo')
        );

        $this->assertTrue($tableDiff->hasDifference());
    }

    public function testDifferenceWithDroppedForeignKeysDifference()
    {
        $tableDiff = new TableDiff(
            $this->oldTableMock,
            $this->newTableMock,
            array(),
            array(),
            array(),
            null,
            null,
            array(),
            array('foo')
        );

        $this->assertTrue($tableDiff->hasDifference());
    }

    public function testDifferenceWithCreatedIndexesDifference()
    {
        $tableDiff = new TableDiff(
            $this->oldTableMock,
            $this->newTableMock,
            array(),
            array(),
            array(),
            null,
            null,
            array(),
            array(),
            array('foo')
        );

        $this->assertTrue($tableDiff->hasDifference());
    }

    public function testDifferenceWithDroppedIndexesDifference()
    {
        $tableDiff = new TableDiff(
            $this->oldTableMock,
            $this->newTableMock,
            array(),
            array(),
            array(),
            null,
            null,
            array(),
            array(),
            array(),
            array('foo')
        );

        $this->assertTrue($tableDiff->hasDifference());
    }

    public function testDifferenceWithCreatedChecksDifference()
    {
        $tableDiff = new TableDiff(
            $this->oldTableMock,
            $this->newTableMock,
            array(),
            array(),
            array(),
            null,
            null,
            array(),
            array(),
            array(),
            array(),
            array('foo')
        );

        $this->assertTrue($tableDiff->hasDifference());
    }

    public function testDifferenceWithDroppedChecksDifference()
    {
        $tableDiff = new TableDiff(
            $this->oldTableMock,
            $this->newTableMock,
            array(),
            array(),
            array(),
            null,
            null,
            array(),
            array(),
            array(),
            array(),
            array(),
            array('foo')
        );

        $this->assertTrue($tableDiff->hasDifference());
    }
}
