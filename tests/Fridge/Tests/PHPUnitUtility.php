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

/**
 * Retrieves a group of settings from the PHPUnit XML configuration file.
 * The strategy used for identifying a group is to prefix all these settings with a unique identifier.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PHPUnitUtility
{
    /** @const The PDO MySQL constant */
    const PDO_MYSQL = 'PDO_MYSQL_';

    /** @const The PDO PgSQL constant */
    const PDO_PGSQL = 'PDO_PGSQL_';

    /** @var array */
    static protected $settings = array();

    /**
     * Checks if a group of settings exists in the PHPUnit XML configuration file.
     *
     * @param string $prefix The settings prefix.
     *
     * @return boolean TRUE if the group of settings exists else FALSE.
     */
    static public function hasSettings($prefix)
    {
        $settings = static::retrieveSettings($prefix);

        return !empty($settings);
    }

    /**
     * Gets a group of settings from the PHPUnit XML configuration file.
     *
     * @param string $prefix The settings prefix.
     *
     * @return array The group of settings.
     */
    static public function getSettings($prefix)
    {
        return static::retrieveSettings($prefix);
    }

    /**
     * Retrieves a group of settings from the PHPUnit XML configuration file.
     * The result is cached and will be reused for the further request.
     *
     * @param string $prefix The settings prefix.
     *
     * @return array The group of settings.
     */
    static protected function retrieveSettings($prefix)
    {
        if (!isset(static::$settings[$prefix])) {
            static::$settings[$prefix] = static::getDefaultSettings();
            $prefixLength = strlen($prefix);

            foreach ($_SERVER as $key => $value) {
                if (strpos($key, $prefix) === 0) {
                    static::$settings[$prefix][strtolower(substr($key, $prefixLength))] = $value;
                }
            }

            if (static::$settings[$prefix] === static::getDefaultSettings()) {
                static::$settings[$prefix] = array();
            }
        }

        return static::$settings[$prefix];
    }

    /**
     * Gets the default settings of a group of settings.
     *
     * @return array The default settings of a group of settings.
     */
    static protected function getDefaultSettings()
    {
        return array(
            'driver'   => null,
            'username' => null,
            'password' => null,
            'dbname'   => null,
            'host'     => null,
            'port'     => null,
        );
    }

    /**
     * Disabled constructor.
     */
    final private function __construct()
    {

    }
}
