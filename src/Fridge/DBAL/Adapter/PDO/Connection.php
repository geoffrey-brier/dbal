<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Adapter\PDO;

use \PDO;

use Fridge\DBAL\Adapter\ConnectionInterface;

/**
 * {@inheritdoc}
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Connection extends PDO implements ConnectionInterface
{
    /**
     * Creates a PDO connection.
     *
     * @param string $dsn           The database DSN.
     * @param string $username      The database username.
     * @param string $password      The database passord.
     * @param array  $driverOptions The database driver options.
     */
    public function __construct($dsn, $username = null, $password = null, array $driverOptions = array())
    {
        parent::__construct($dsn, $username, $password, $driverOptions);

        $this->setAttribute(self::ATTR_ERRMODE, self::ERRMODE_EXCEPTION);
        $this->setAttribute(self::ATTR_STATEMENT_CLASS, array('Fridge\DBAL\Adapter\PDO\Statement', array()));
    }
}
