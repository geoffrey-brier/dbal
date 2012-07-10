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

use Fridge\DBAL\Schema\Sequence;

/**
 * Sequence test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class SequenceTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\Schema\Sequence */
    protected $sequence;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->sequence = new Sequence('foo');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->sequence);
    }

    public function testInitialState()
    {
        $this->assertEquals('foo', $this->sequence->getName());
        $this->assertEquals(1, $this->sequence->getInitialValue());
        $this->assertEquals(1, $this->sequence->getIncrementSize());
    }

    public function testInitialValueWithValidValue()
    {
        $this->sequence->setInitialValue(3);
        $this->assertEquals(3, $this->sequence->getInitialValue());
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The initial value of the sequence "foo" must be a positive integer.
     */
    public function testInitialValueWithInvalidValue()
    {
        $this->sequence->setInitialValue(0);
    }

    public function testIncrementSizeWithValidValue()
    {
        $this->sequence->setIncrementSize(2);
        $this->assertEquals(2, $this->sequence->getIncrementSize());
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The increment size of the sequence "foo" must be a positive integer.
     */
    public function testIncrementSizeWithInvalidValue()
    {
        $this->sequence->setIncrementSize(0);
    }
}
