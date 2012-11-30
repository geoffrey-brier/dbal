<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\Event\Subscriber;

use Fridge\DBAL\Event\Events,
    Fridge\DBAL\Event\PostConnectEvent,
    Fridge\DBAL\Event\Subscriber\SetCharsetSubscriber;

/**
 * MySQLSessionInit test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class SetCharsetSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /** @var Fridge\DBAL\Event\Subscriber\CharsetSubscriber */
    protected $subscriber;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->subscriber = new SetCharsetSubscriber();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->subscriber);
    }

    public function testSubscribedEvents()
    {
        $subscribedEvents = SetCharsetSubscriber::getSubscribedEvents();

        $this->assertTrue(in_array(Events::POST_CONNECT, array_keys($subscribedEvents)));
        $this->assertTrue(method_exists($this->subscriber, $subscribedEvents[Events::POST_CONNECT]));
    }

    public function testCharset()
    {
        $this->assertSame('utf8', $this->subscriber->getCharset());
    }

    public function testPostConnect()
    {
        $connectionMock = $this->getMock('Fridge\DBAL\Connection\ConnectionInterface');
        $connectionMock
            ->expects($this->once())
            ->method('setCharset')
            ->with($this->equalTo('utf8'));

        $event = new PostConnectEvent($connectionMock);

        $this->subscriber->postConnect($event);
    }
}
