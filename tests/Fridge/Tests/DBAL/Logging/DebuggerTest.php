<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\Logging;

use Fridge\DBAL\Logging\Debugger;

/**
 * Debugger test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class DebuggerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Fridge\DBAl\Logging\Debugger */
    protected $debugger;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->debugger = new Debugger();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->debugger);
    }

    public function testDebug()
    {
        $this->debugger->start('foo');
        $this->debugger->stop();

        $this->assertSame($this->debugger->getQuery(), 'foo');
        $this->assertInternalType('float', $this->debugger->getTime());
    }

    public function testToString()
    {
        $this->debugger->start('foo');
        $this->debugger->stop();

        $this->assertRegExp('/^The query "foo" has been executed in [0-9]*.[0-9]* ms.$/', $this->debugger->toString());
    }

    public function testToArray()
    {
        $this->debugger->start('foo');
        $this->debugger->stop();

        $expected = array(
            'query' => $this->debugger->getQuery(),
            'time'  => $this->debugger->getTime()
        );

        $this->assertSame($expected, $this->debugger->toArray());
    }
}
