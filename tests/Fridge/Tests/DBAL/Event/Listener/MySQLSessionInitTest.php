<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\Event\Listener;

use Fridge\DBAL\Event;

/**
 * MySQLSessionInit test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class MySQLSessionInitTest extends \PHPUnit_Framework_TestCase
{
    /** @var Fridge\DBAL\Event\Listener\MySQLSessionInit */
    protected $listener;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->listener = new Event\Listener\MySQLSessionInit();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->listener);
    }

    public function testSubscribedEvents()
    {
        $subscribedEvents = Event\Listener\MySQLSessionInit::getSubscribedEvents();

        $this->assertTrue(in_array(Event\Events::POST_CONNECT, array_keys($subscribedEvents)));
        $this->assertTrue(method_exists($this->listener, $subscribedEvents[Event\Events::POST_CONNECT]));
    }

    public function testCharset()
    {
        $this->assertEquals('utf8', $this->listener->getCharset());
    }

    public function testPostConnect()
    {
        $connectionMock = $this->getMock('Fridge\DBAL\Connection\ConnectionInterface');
        $connectionMock
            ->expects($this->once())
            ->method('setCharset')
            ->with($this->equalTo('utf8'));

        $event = new Event\PostConnectEvent($connectionMock);

        $this->listener->postConnect($event);
    }
}
