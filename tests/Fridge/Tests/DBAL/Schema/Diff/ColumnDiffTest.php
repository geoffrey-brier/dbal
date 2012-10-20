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

use Fridge\DBAL\Schema\Diff\ColumnDiff;

/**
 * Column diff test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 * @author Benjamin Lazarecki <benjamin.lazarecki@gmail.com>
 */
class ColumnDiffTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\Schema\Column */
    protected $oldColumnMock;

    /** @var \Fridge\DBAL\Schema\Column */
    protected $newColumnMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->oldColumnMock = $this->getMock('Fridge\DBAL\Schema\Column', array(), array(), '', false);
        $this->oldColumnMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('foo'));

        $this->newColumnMock = $this->getMock('Fridge\DBAL\Schema\Column', array(), array(), '', false);
        $this->newColumnMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('foo'));
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->oldColumnMock);
        unset($this->newColumnMock);
    }

    public function testInitialState()
    {
        $differences = array('foo');
        $columnDiff = new ColumnDiff($this->oldColumnMock, $this->newColumnMock, $differences);

        $this->assertSame($this->oldColumnMock, $columnDiff->getOldAsset());
        $this->assertSame($this->newColumnMock, $columnDiff->getNewAsset());
        $this->assertSame($differences, $columnDiff->getDifferences());
    }

    public function testDifferenceWithoutDifference()
    {
        $columnDiff = new ColumnDiff($this->oldColumnMock, $this->newColumnMock, array());

        $this->assertFalse($columnDiff->hasDifference());
        $this->assertFalse($columnDiff->hasNameDifference());
        $this->assertFalse($columnDiff->hasNameDifferenceOnly());
    }

    public function testDifferenceWithNameDifference()
    {
        $this->newColumnMock = $this->getMock('Fridge\DBAL\Schema\Column', array(), array(), '', false);
        $this->newColumnMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('bar'));

        $columnDiff = new ColumnDiff($this->oldColumnMock, $this->newColumnMock, array());

        $this->assertTrue($columnDiff->hasDifference());
        $this->assertTrue($columnDiff->hasNameDifference());
        $this->assertTrue($columnDiff->hasNameDifferenceOnly());
    }

    public function testDifferenceWithoutNameDifferenceOnly()
    {
        $this->newColumnMock = $this->getMock('Fridge\DBAL\Schema\Column', array(), array(), '', false);
        $this->newColumnMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('bar'));

        $columnDiff = new ColumnDiff($this->oldColumnMock, $this->newColumnMock, array('foo'));

        $this->assertFalse($columnDiff->hasNameDifferenceOnly());
    }

    public function testDIfferenceWithColumnDifference()
    {
        $columnDiff = new ColumnDiff($this->oldColumnMock, $this->newColumnMock, array('foo'));

        $this->assertTrue($columnDiff->hasDifference());
    }
}

