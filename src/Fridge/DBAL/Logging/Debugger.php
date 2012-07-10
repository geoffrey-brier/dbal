<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Logging;

/**
 * Debugs the execution time of a query.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Debugger
{
    /** @var string */
    protected $query;

    /** @var float */
    protected $time;

    /** @var float */
    protected $start;

    /**
     * Starts the debug.
     *
     * @param string $query The debugged query
     */
    public function start($query)
    {
        $this->start = microtime(true);
        $this->query = $query;
    }

    /**
     * Stops the debug.
     */
    public function stop()
    {
        $this->time = microtime(true) - $this->start;
    }

    /**
     * Gets the debugged query.
     *
     * @return string The debugged query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Gets the execution time of the query in ms.
     *
     * @return float The execution time of the query
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Converts the debugger result to a string.
     *
     * @return string The debugger result
     */
    public function toString()
    {
        return sprintf('The query "%s" has been executed in %f ms.', $this->getQuery(), $this->getTime());
    }

    /**
     * Converts the debugger result to an array.
     *
     * @return array The debugger result
     */
    public function toArray()
    {
        return array(
            'query' => $this->getQuery(),
            'time'  => $this->getTime()
        );
    }
}
