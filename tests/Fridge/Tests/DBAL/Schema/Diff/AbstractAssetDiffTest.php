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

/**
 * Abstract asset diff test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 * @author Benjamin Lazarecki <benjamin.lazarecki@gmail.com>
 */
class AbstractAssetDiffTest extends \PHPUnit_Framework_TestCase
{
    public function testInitialState()
    {
        $assetDiff = $this->getMockForAbstractClass('Fridge\DBAL\Schema\Diff\AbstractAssetDiff', array('foo', 'bar'));

        $this->assertEquals('foo', $assetDiff->getOldName());
        $this->assertEquals('bar', $assetDiff->getNewName());
    }

    public function testDifferenceWithoutDifference()
    {
        $assetDiff = $this->getMockForAbstractClass('Fridge\DBAL\Schema\Diff\AbstractAssetDiff', array('foo', 'foo'));

        $this->assertFalse($assetDiff->hasDifference());
    }

    public function testDifferenceWithDifference()
    {
        $assetDiff = $this->getMockForAbstractClass('Fridge\DBAL\Schema\Diff\AbstractAssetDiff', array('foo', 'bar'));

        $this->assertTrue($assetDiff->hasDifference());
    }
}
