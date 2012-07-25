<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL;

use Monolog\Logger,
    Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Describes the connection configuration. It wraps a powerfull logger (Monolog)
 * and an event dispatcher (Symfony2 EventDispatcher component).
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Configuration
{
    /** @var \Monolog\Logger */
    protected $logger;

    /** @var \Symfony\Component\EventDispatcher\EventDispatcher */
    protected $eventDispatcher;

    /**
     * Creates a configuration.
     *
     * @param \Monolog\Logger                                    $logger          The logger.
     * @param \Symfony\Component\EventDispatcher\EventDispatcher $eventDispatcher The event dispatcher.
     */
    public function __construct(Logger $logger = null, EventDispatcher $eventDispatcher = null)
    {
        if ($logger === null) {
            $logger = new Logger('Fridge DBAL');
        }

        if ($eventDispatcher === null) {
            $eventDispatcher = new EventDispatcher();
        }

        $this->logger = $logger;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Gets the logger.
     *
     * @return \Monolog\Logger The logger.
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Sets the logger.
     *
     * @param \Monolog\Logger $logger The logger.
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Gets the event dispatcher.
     *
     * @return \Symfony\Component\EventDispatcher\EventDispatcher The event dispatcher.
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * Sets the event dispatcher.
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcher $eventDispatcher The event dispatcher.
     */
    public function setEventDispatcher(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }
}
