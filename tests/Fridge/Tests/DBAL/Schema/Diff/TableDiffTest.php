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
    public function testInitialState()
    {
        $createdPrimaryKey = $this->getMock('Fridge\DBAL\Schema\PrimaryKey', array(), array(), '', false);
        $droppedPrimaryKey = $this->getMock('Fridge\DBAL\Schema\PrimaryKey', array(), array(), '', false);

        $tableDiff = new TableDiff(
            'foo',
            'bar',
            array('createdColumn'),
            array('alteredColumn'),
            array('droppedColumn'),
            $createdPrimaryKey,
            $droppedPrimaryKey,
            array('createdForeignKey'),
            array('droppedForeignKey'),
            array('createdIndex'),
            array('droppedIndex')
        );

        $this->assertEquals('foo', $tableDiff->getOldName());
        $this->assertEquals('bar', $tableDiff->getNewName());

        $this->assertEquals(array('createdColumn'), $tableDiff->getCreatedColumns());
        $this->assertEquals(array('alteredColumn'), $tableDiff->getAlteredColumns());
        $this->assertEquals(array('droppedColumn'), $tableDiff->getDroppedColumns());

        $this->assertEquals($createdPrimaryKey, $tableDiff->getCreatedPrimaryKey());
        $this->assertEquals($droppedPrimaryKey, $tableDiff->getDroppedPrimaryKey());

        $this->assertEquals(array('createdForeignKey'), $tableDiff->getCreatedForeignKeys());
        $this->assertEquals(array('droppedForeignKey'), $tableDiff->getDroppedForeignKeys());

        $this->assertEquals(array('createdIndex'), $tableDiff->getCreatedIndexes());
        $this->assertEquals(array('droppedIndex'), $tableDiff->getDroppedIndexes());
    }

    public function testDifferenceWithoutDifference()
    {
        $tableDiff = new TableDiff('foo', 'foo');

        $this->assertFalse($tableDiff->hasDifference());
    }

    public function testDifferenceWithNameDifference()
    {
        $tableDiff = new TableDiff('foo', 'bar');

        $this->assertTrue($tableDiff->hasDifference());
    }

    public function testDifferenceWithCreatedColumnsDifference()
    {
        $tableDiff = new TableDiff('foo', 'foo', array('foo'));

        $this->assertTrue($tableDiff->hasDifference());
    }

    public function testDifferenceWithAlteredColumnsDifference()
    {
        $tableDiff = new TableDiff('foo', 'foo', array(), array('foo'));

        $this->assertTrue($tableDiff->hasDifference());
    }

    public function testDifferenceWithDroppedColumnsDifference()
    {
        $tableDiff = new TableDiff('foo', 'foo', array(), array(), array('foo'));

        $this->assertTrue($tableDiff->hasDifference());
    }

    public function testDifferenceWithCreatedPrimaryKey()
    {
        $primaryKey = $this->getMock('Fridge\DBAL\Schema\PrimaryKey', array(), array(), '', false);
        $tableDiff = new TableDiff('foo', 'foo', array(), array(), array(), $primaryKey);

        $this->assertTrue($tableDiff->hasDifference());
    }

    public function testDifferenceWithDroppedPrimaryKey()
    {
        $primaryKey = $this->getMock('Fridge\DBAL\Schema\PrimaryKey', array(), array(), '', false);
        $tableDiff = new TableDiff('foo', 'foo', array(), array(), array(), null, $primaryKey);

        $this->assertTrue($tableDiff->hasDifference());
    }

    public function testDifferenceWithCreatedForeignKeysDifference()
    {
        $tableDiff = new TableDiff('foo', 'foo', array(), array(), array(), null, null, array('foo'));

        $this->assertTrue($tableDiff->hasDifference());
    }

    public function testDifferenceWithDroppedForeignKeysDifference()
    {
        $tableDiff = new TableDiff('foo', 'foo', array(), array(), array(), null, null, array(), array('foo'));

        $this->assertTrue($tableDiff->hasDifference());
    }

    public function testDifferenceWithCreatedIndexesDifference()
    {
        $tableDiff = new TableDiff(
            'foo',
            'foo',
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
            'foo',
            'foo',
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
}
