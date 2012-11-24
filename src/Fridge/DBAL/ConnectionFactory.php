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

use Fridge\DBAL\Exception\FactoryException;

/**
 * This class is the central point of the library.
 * It allows you to request all available DBAL connections and more.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ConnectionFactory
{
    /** @const The Fridge DBAL version */
    const VERSION = '1.0.0-dev';

    /** @var array */
    static protected $mappedDriverClasses = array(
        'pdo_mysql' => 'Fridge\DBAL\Driver\PDO\MySQLDriver',
        'pdo_pgsql' => 'Fridge\DBAL\Driver\PDO\PostgreSQLDriver',
        'mysqli'    => 'Fridge\DBAL\Driver\MysqliDriver',
    );

    /**
     * Gets the available drivers.
     *
     * @return array The available drivers.
     */
    static public function getAvailableDrivers()
    {
        return array_keys(static::$mappedDriverClasses);
    }

    /**
     * Creates a DBAL connection.
     *
     * $parameters must contain at least:
     *  - driver (string) (ex: pdo_mysql, pdo_pgsql)
     *  OR
     *  - driver_class (string) (ex: Fridge\DBAL\Driver\MySQLDriver)
     *
     * If you use driver & driver_class parameters simultaneously,
     * the driver_class will be used.
     *
     * $parameters can contain:
     *  - connection_class (string) (ex: Fridge\DBAL\Connection\Connection)
     *  - username (string)
     *  - password (string)
     *  - dbname (string)
     *  - host (string)
     *  - port (integer)
     *
     * If you don't use the connection_class parameter,
     * the class Fridge\DBAL\Connection\Connection will be used.
     *
     * $parameters can contain some specific database parameters:
     *  - pdo_mysql: unix_socket (string), charset (string), driver_options (array)
     *  - pdo_pgsql: driver_options (array)
     *  - mysqli: unix_socket (string), charset (string)
     *
     * @param array                      $parameters    The connection parameters.
     * @param \Fridge\DBAL\Configuration $configuration The connection configuration.
     *
     * @return \Fridge\DBAL\Connection\ConnectionInterface The DBAL Connection.
     */
    static public function create(array $parameters, Configuration $configuration = null)
    {
        if (isset($parameters['driver_class'])) {
            if (!in_array('Fridge\DBAL\Driver\DriverInterface', class_implements($parameters['driver_class']))) {
                throw FactoryException::driverMustImplementDriverInterface($parameters['driver_class']);
            }

            $driverClass = $parameters['driver_class'];
        } elseif (isset($parameters['driver'])) {
            if (!isset(static::$mappedDriverClasses[$parameters['driver']])) {
                throw FactoryException::driverDoesNotExist($parameters['driver'], static::getAvailableDrivers());
            }

            $driverClass = static::$mappedDriverClasses[$parameters['driver']];
        } else {
            throw FactoryException::driverRequired(static::getAvailableDrivers());
        }

        if (isset($parameters['connection_class'])) {
            if (!in_array('Fridge\DBAL\Connection\ConnectionInterface', class_implements($parameters['connection_class']))) {
                throw FactoryException::connectionMustImplementConnectionInterface($parameters['connection_class']);
            }

            $connectionClass = $parameters['connection_class'];
        } else {
            $connectionClass = 'Fridge\DBAL\Connection\Connection';
        }

        return new $connectionClass($parameters, new $driverClass(), $configuration);
    }

    /**
     * Disabled constructor.
     *
     * @codeCoverageIgnore
     */
    final private function __construct()
    {

    }
}
