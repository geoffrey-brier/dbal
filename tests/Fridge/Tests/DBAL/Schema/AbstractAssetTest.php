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

use \ReflectionMethod;

/**
 * Abstract asset test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class AbstractAssetTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\Schema\AbstractAsset */
    protected $asset;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->asset = $this->getMockForAbstractClass('Fridge\DBAL\Schema\AbstractAsset', array('foo'));
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->asset);
    }

    public function testInitialState()
    {
        $this->assertEquals('foo', $this->asset->getName());
    }

    public function testNameWithValidValue()
    {
        $this->asset->setName('bar');

        $this->assertEquals('bar', $this->asset->getName());
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The foo bar name must be a string.
     */
    public function testNameWithInvalidValue()
    {
        $asset = $this->getMockForAbstractClass('Fridge\DBAL\Schema\AbstractAsset', array('foo'), 'FooBar');

        $asset->setName(true);
    }

    public function testGenerateIdentifier()
    {
        $method = new ReflectionMethod('Fridge\DBAL\Schema\AbstractAsset', 'generateIdentifier');
        $method->setAccessible(true);

        $identifier = $method->invoke($this->asset, 'bar_', 10);
        $this->assertRegExp('/^bar_[a-z0-9]{6}$/', $identifier);
    }
}
