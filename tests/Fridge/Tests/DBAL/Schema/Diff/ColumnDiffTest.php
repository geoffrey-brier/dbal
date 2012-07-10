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
    protected $columnMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->columnMock = $this->getMock('Fridge\DBAL\Schema\Column', array(), array(), '', false);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->columnMock);
    }

    public function testInitialState()
    {
        $columnDiff = new ColumnDiff('foo', 'bar', $this->columnMock);

        $this->assertEquals('foo', $columnDiff->getOldName());
        $this->assertEquals('bar', $columnDiff->getNewName());
        $this->assertEquals($this->columnMock, $columnDiff->getColumn());
    }

    public function testDifferenceWithoutDifference()
    {
        $columnDiff = new ColumnDiff('foo', 'foo');

        $this->assertFalse($columnDiff->hasDifference());
    }

    public function testDifferenceWithNameDifference()
    {
        $columnDiff = new ColumnDiff('foo', 'bar');

        $this->assertTrue($columnDiff->hasDifference());
    }

    public function testDIfferenceWithColumnDifference()
    {
        $columnDiff = new ColumnDiff('foo', 'foo', $this->columnMock);

        $this->assertTrue($columnDiff->hasDifference());
    }
}

