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
    /** @var \Fridge\DBAL\Schema\AbstractAsset */
    protected $oldAssetMock;

    /** @var \Fridge\DBAL\Schema\AbstractAsset */
    protected $newAssetMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->oldAsset = $this->getMockForAbstractClass('Fridge\DBAL\Schema\AbstractAsset', array('foo'));
        $this->newAsset = $this->getMockForAbstractClass('Fridge\DBAL\Schema\AbstractAsset', array('bar'));
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->oldAssetMock);
        unset($this->newAssetMock);
    }

    public function testInitialState()
    {
        $assetDiff = $this->getMockForAbstractClass(
            'Fridge\DBAL\Schema\Diff\AbstractAssetDiff',
            array($this->oldAsset, $this->newAsset)
        );

        $this->assertSame($this->oldAsset, $assetDiff->getOldAsset());
        $this->assertSame($this->newAsset, $assetDiff->getNewAsset());
    }

    public function testDifferenceWithoutDifference()
    {
        $oldAsset = $this->getMockForAbstractClass('Fridge\DBAL\Schema\AbstractAsset', array('foo'));
        $newAsset = $this->getMockForAbstractClass('Fridge\DBAL\Schema\AbstractAsset', array('foo'));

        $assetDiff = $this->getMockForAbstractClass(
            'Fridge\DBAL\Schema\Diff\AbstractAssetDiff',
            array($oldAsset, $newAsset)
        );

        $this->assertFalse($assetDiff->hasDifference());
        $this->assertFalse($assetDiff->hasNameDifference());
        $this->assertFalse($assetDiff->hasNameDifferenceOnly());
    }

    public function testDifferenceWithDifference()
    {
        $assetDiff = $this->getMockForAbstractClass(
            'Fridge\DBAL\Schema\Diff\AbstractAssetDiff',
            array($this->oldAsset, $this->newAsset)
        );

        $this->assertTrue($assetDiff->hasDifference());
        $this->assertTrue($assetDiff->hasNameDifference());
        $this->assertTrue($assetDiff->hasNameDifferenceOnly());
    }
}
