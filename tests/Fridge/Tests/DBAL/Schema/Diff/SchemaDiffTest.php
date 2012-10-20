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
    /** @var \Fridge\DBAL\Schema\Schema */
    protected $oldSchemaMock;

    /** @var \Fridge\DBAL\Schema\Schema */
    protected $newSchemaMock;

    protected function setUp()
    {
        $this->oldSchemaMock = $this->getMock('Fridge\DBAL\Schema\Schema', array(), array(), '', false);
        $this->oldSchemaMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('foo'));

        $this->newSchemaMock = $this->getMock('Fridge\DBAL\Schema\Schema', array(), array(), '', false);
        $this->newSchemaMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('foo'));
    }

    protected function tearDown()
    {
        unset($this->oldSchemaMock);
        unset($this->newSchemaMock);
    }

    public function testInitialState()
    {
        $schemaDiff = new SchemaDiff(
            $this->oldSchemaMock,
            $this->newSchemaMock,
            array('createdTable'),
            array('alteredTable'),
            array('droppedTable'),
            array('createdSequence'),
            array('droppedSequence'),
            array('createdView'),
            array('droppedView')
        );

        $this->assertSame($this->oldSchemaMock, $schemaDiff->getOldAsset());
        $this->assertSame($this->newSchemaMock, $schemaDiff->getNewAsset());

        $this->assertSame(array('createdTable'), $schemaDiff->getCreatedTables());
        $this->assertSame(array('alteredTable'), $schemaDiff->getAlteredTables());
        $this->assertSame(array('droppedTable'), $schemaDiff->getDroppedTables());

        $this->assertSame(array('createdSequence'), $schemaDiff->getCreatedSequences());
        $this->assertSame(array('droppedSequence'), $schemaDiff->getDroppedSequences());

        $this->assertSame(array('createdView'), $schemaDiff->getCreatedViews());
        $this->assertSame(array('droppedView'), $schemaDiff->getDroppedViews());
    }

    public function testDifferenceWithoutDifference()
    {
        $schemaDiff = new SchemaDiff($this->oldSchemaMock, $this->newSchemaMock);

        $this->assertFalse($schemaDiff->hasDifference());
        $this->assertFalse($schemaDiff->hasNameDifference());
        $this->assertFalse($schemaDiff->hasNameDifferenceOnly());
    }

    public function testDifferenceWithNameDifference()
    {
        $this->newSchemaMock = $this->getMock('Fridge\DBAL\Schema\Schema', array(), array(), '', false);
        $this->newSchemaMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('bar'));

        $schemaDiff = new SchemaDiff($this->oldSchemaMock, $this->newSchemaMock);

        $this->assertTrue($schemaDiff->hasDifference());
        $this->assertTrue($schemaDiff->hasNameDifference());
        $this->assertTrue($schemaDiff->hasNameDifferenceOnly());
    }

    public function testDifferenceWithoutNameDifferenceOnly()
    {
        $this->newSchemaMock = $this->getMock('Fridge\DBAL\Schema\Schema', array(), array(), '', false);
        $this->newSchemaMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('bar'));

        $schemaDiff = new SchemaDiff($this->oldSchemaMock, $this->newSchemaMock, array('foo'));

        $this->assertFalse($schemaDiff->hasNameDifferenceOnly());
    }

    public function testDifferenceWithCreatedTablesDifference()
    {
        $schemaDiff = new SchemaDiff($this->oldSchemaMock, $this->newSchemaMock, array('foo'));

        $this->assertTrue($schemaDiff->hasDifference());
    }

    public function testDifferenceWithAlteredTablesDifference()
    {
        $schemaDiff = new SchemaDiff($this->oldSchemaMock, $this->newSchemaMock, array(), array('foo'));

        $this->assertTrue($schemaDiff->hasDifference());
    }

    public function testDifferenceWithDroppedTablesDifference()
    {
        $schemaDiff = new SchemaDiff($this->oldSchemaMock, $this->newSchemaMock, array(), array(), array('foo'));

        $this->assertTrue($schemaDiff->hasDifference());
    }

    public function testDifferenceWithCreatedSequencesDifference()
    {
        $schemaDiff = new SchemaDiff(
            $this->oldSchemaMock,
            $this->newSchemaMock,
            array(),
            array(),
            array(),
            array('foo')
        );

        $this->assertTrue($schemaDiff->hasDifference());
    }

    public function testDifferenceWithDroppedSequencesDifference()
    {
        $schemaDiff = new SchemaDiff(
            $this->oldSchemaMock,
            $this->newSchemaMock,
            array(),
            array(),
            array(),
            array(),
            array('foo')
        );

        $this->assertTrue($schemaDiff->hasDifference());
    }

    public function testDifferenceWithCreatedViewsDifference()
    {
        $schemaDiff = new SchemaDiff(
            $this->oldSchemaMock,
            $this->newSchemaMock,
            array(),
            array(),
            array(),
            array(),
            array(),
            array('foo')
        );

        $this->assertTrue($schemaDiff->hasDifference());
    }

    public function testDifferenceWithDroppedViewsDifference()
    {
        $schemaDiff = new SchemaDiff(
            $this->oldSchemaMock,
            $this->newSchemaMock,
            array(),
            array(),
            array(),
            array(),
            array(),
            array(),
            array('foo')
        );

        $this->assertTrue($schemaDiff->hasDifference());
    }
}
