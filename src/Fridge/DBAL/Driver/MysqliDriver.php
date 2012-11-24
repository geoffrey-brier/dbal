<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Driver;

use Fridge\DBAL\Adapter\Mysqli\MysqliConnection,
    Fridge\DBAL\Connection\ConnectionInterface,
    Fridge\DBAL\Platform\MySQLPlatform,
    Fridge\DBAL\SchemaManager\MySQLSchemaManager;

/**
 * The Mysqli driver.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class MysqliDriver extends AbstractDriver
{
    /**
     * {@inheritdoc}
     */
    public function connect(array $parameters, $username = null, $password = null, array $driverOptions = array())
    {
        return new MysqliConnection($parameters, $username, $password);
    }

    /**
     * {@inheritdoc}
     */
    public function getPlatform()
    {
        if ($this->platform === null) {
            $this->platform = new MySQLPlatform();
        }

        return $this->platform;
    }

    /**
     * {@inheritdoc}
     */
    public function getSchemaManager(ConnectionInterface $connection)
    {
        if (($this->schemaManager === null) || ($this->schemaManager->getConnection() !== $connection)) {
            $this->schemaManager = new MySQLSchemaManager($connection);
        }

        return $this->schemaManager;
    }
}
