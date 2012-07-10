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

use Fridge\DBAL\Schema\Diff\SchemaDiff;

/**
 * Schema diff Test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class SchemaDiffTest extends \PHPUnit_Framework_TestCase
{
    public function testInitialState()
    {
        $schemaDiff = new SchemaDiff(
            'foo',
            'bar',
            array('createdTable'),
            array('alteredTable'),
            array('droppedTable'),
            array('createdSequence'),
            array('droppedSequence'),
            array('createdView'),
            array('droppedView')
        );

        $this->assertEquals('foo', $schemaDiff->getOldName());
        $this->assertEquals('bar', $schemaDiff->getNewName());

        $this->assertEquals(array('createdTable'), $schemaDiff->getCreatedTables());
        $this->assertEquals(array('alteredTable'), $schemaDiff->getAlteredTables());
        $this->assertEquals(array('droppedTable'), $schemaDiff->getDroppedTables());

        $this->assertEquals(array('createdSequence'), $schemaDiff->getCreatedSequences());
        $this->assertEquals(array('droppedSequence'), $schemaDiff->getDroppedSequences());

        $this->assertEquals(array('createdView'), $schemaDiff->getCreatedViews());
        $this->assertEquals(array('droppedView'), $schemaDiff->getDroppedViews());
    }

    public function testDifferenceWithoutDifference()
    {
        $schemaDiff = new SchemaDiff('foo', 'foo');

        $this->assertFalse($schemaDiff->hasDifference());
    }

    public function testDifferenceWithNameDifference()
    {
        $schemaDiff = new SchemaDiff('foo', 'bar');

        $this->assertTrue($schemaDiff->hasDifference());
    }

    public function testDifferenceWithCreatedTablesDifference()
    {
        $schemaDiff = new SchemaDiff('foo', 'foo', array('foo'));

        $this->assertTrue($schemaDiff->hasDifference());
    }

    public function testDifferenceWithAlteredTablesDifference()
    {
        $schemaDiff = new SchemaDiff('foo', 'foo', array(), array('foo'));

        $this->assertTrue($schemaDiff->hasDifference());
    }

    public function testDifferenceWithDroppedTablesDifference()
    {
        $schemaDiff = new SchemaDiff('foo', 'foo', array(), array(), array('foo'));

        $this->assertTrue($schemaDiff->hasDifference());
    }

    public function testDifferenceWithCreatedSequencesDifference()
    {
        $schemaDiff = new SchemaDiff('foo', 'foo', array(), array(), array(), array('foo'));

        $this->assertTrue($schemaDiff->hasDifference());
    }

    public function testDifferenceWithDroppedSequencesDifference()
    {
        $schemaDiff = new SchemaDiff('foo', 'foo', array(), array(), array(), array(), array('foo'));

        $this->assertTrue($schemaDiff->hasDifference());
    }

    public function testDifferenceWithCreatedViewsDifference()
    {
        $schemaDiff = new SchemaDiff('foo', 'foo', array(), array(), array(), array(), array(), array('foo'));

        $this->assertTrue($schemaDiff->hasDifference());
    }

    public function testDifferenceWithDroppedViewsDifference()
    {
        $schemaDiff = new SchemaDiff('foo', 'foo', array(), array(), array(), array(), array(), array(), array('foo'));

        $this->assertTrue($schemaDiff->hasDifference());
    }
}
