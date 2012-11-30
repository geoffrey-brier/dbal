<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Event\Subscriber;

use Fridge\DBAL\Event\Events,
    Fridge\DBAL\Event\PostConnectEvent,
    Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Sets the character sets of a connection after a database connection.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class SetCharsetSubscriber implements EventSubscriberInterface
{
    /** @var string */
    protected $charset;

    /**
     * Creates a MySQL Session initializer.
     *
     * @param string $charset The MySQL charset.
     */
    public function __construct($charset = 'utf8')
    {
        $this->charset = $charset;
    }

    /**
     * Gets the MySQL charset.
     *
     * @return string The MySQL charset.
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * Method used when the post connect event is triggered.
     *
     * @param Fridge\DBAL\Event\PostConnectEvent $event The post connect event.
     */
    public function postConnect(PostConnectEvent $event)
    {
        $event->getConnection()->setCharset($this->getCharset());
    }

    /**
     * {@inheritdoc}
     */
    static public function getSubscribedEvents()
    {
        return array(
            Events::POST_CONNECT => 'postConnect',
        );
    }
}
