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

/**
 * Discribes the available events.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Events
{
    /** @const The post connect event */
    const POST_CONNECT = 'POST_CONNECT';

    /**
     * Disabled constructor.
     */
    final private function __construct()
    {

    }
}
