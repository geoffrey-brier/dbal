<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests;

use Fridge\DBAL\ConnectionFactory;

/**
 * Builds a connection according to the PHPUnit XML configuration.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ConnectionUtility
{
    /** @const The PDO MySQL constant */
    const PDO_MYSQL = PHPUnitUtility::PDO_MYSQL;

    /** @const The PDO PgSQL constant */
    const PDO_PGSQL = PHPUnitUtility::PDO_PGSQL;

    /** @const The Mysqli constant */
    const MYSQLI = PHPUnitUtility::MYSQLI;

    /**
     * Checks if a connection can be tested.
     *
     * @param string $driver The driver name.
     *
     * @return boolean TRUE if the connection can be tested else FALSE.
     */
    static public function hasConnection($driver)
    {
        return PHPUnitUtility::hasSettings($driver);
    }

    /**
     * Gets a connection in order to be tested.
     *
     * @param string $driver The driver name.
     *
     * @return \Fridge\DBAL\Connection\ConnectionInterface The connection if it can be tested else NULL.
     */
    static public function getConnection($driver)
    {
        if (!self::hasConnection($driver)) {
            return null;
        }

        return ConnectionFactory::create(PHPUnitUtility::getSettings($driver));
    }

    /**
     * Disabled constructor.
     */
    final private function __construct()
    {

    }
}
