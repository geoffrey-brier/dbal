<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\Event;

use Fridge\DBAL\Event\PostConnectEvent;

/**
 * PostConnectEvent test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PostConnectEventTest extends \PHPUnit_Framework_TestCase
{
    public function testConnection()
    {
        $connectionMock = $this->getMock('Fridge\DBAL\Connection\ConnectionInterface');
        $event = new PostConnectEvent($connectionMock);

        $this->assertSame($connectionMock, $event->getConnection());
    }
}
