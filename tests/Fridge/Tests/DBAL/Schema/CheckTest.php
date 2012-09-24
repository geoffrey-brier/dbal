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

use Fridge\DBAL\Schema\Check;

/**
 * Check tests.
 *
 * @author Benjamin Lazarecki <benjamin.lazarecki@gmail.com>
 */
class CheckTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\Schema\Check */
    protected $check;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->check = new Check('foo');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->check);
    }

    public function testInitialState()
    {
        $this->assertSame('foo', $this->check->getName());
        $this->assertNull($this->check->getDefinition());
    }

    public function testGenerateName()
    {
        $check = new Check(null);
        $this->assertRegExp('/^cct_[a-z0-9]{16}$/', $check->getName());
    }

    public function testSetGetsDefinition()
    {
        $check = new Check(null);
        $check->setDefinition('foo');
        $this->assertSame('foo', $check->getDefinition());
    }
}
