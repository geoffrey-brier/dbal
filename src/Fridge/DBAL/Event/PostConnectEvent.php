<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Event;

use Fridge\DBAL\Connection\ConnectionInterface,
    Symfony\Component\EventDispatcher\Event;

/**
 * This event is dispatched after a connection has been established with the database.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PostConnectEvent extends Event
{
    /** @var Fridge\DBAL\Connection\ConnectionInterface */
    protected $connection;

    /**
     * Creates a post connect event.
     *
     * @param Fridge\DBAL\Connection\ConnectionInterface $connection The connection.
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Gets the connection
     *
     * @return Fridge\DBAL\Connection\ConnectionInterface The connection.
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
