<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Driver\PDO;

use Fridge\DBAL\Connection\ConnectionInterface,
    Fridge\DBAL\Platform\PostgreSQLPlatform,
    Fridge\DBAL\SchemaManager\PostgreSQLSchemaManager;

/**
 * PDO PostgreSQL driver.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PostgreSQLDriver extends AbstractDriver
{
    /**
     * {@inheritdoc}
     */
    public function getPlatform()
    {
        if ($this->platform === null) {
            $this->platform = new PostgreSQLPlatform();
        }

        return $this->platform;
    }

    /**
     * {@inheritdoc}
     */
    public function getSchemaManager(ConnectionInterface $connection)
    {
        if (($this->schemaManager === null) || ($this->schemaManager->getConnection() !== $connection)) {
            $this->schemaManager = new PostgreSQLSchemaManager($connection);
        }

        return $this->schemaManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function generateDSN(array $parameters)
    {
        $dsnOptions = array();

        if (isset($parameters['dbname']) && !empty($parameters['dbname'])) {
            $dsnOptions[] = 'dbname='.$parameters['dbname'];
        }

        if (isset($parameters['host']) && !empty($parameters['host'])) {
            $dsnOptions[] = 'host='.$parameters['host'];
        }

        if (isset($parameters['port']) && !empty($parameters['port'])) {
            $dsnOptions[] = 'port='.$parameters['port'];
        }

        return 'pgsql:'.implode(';', $dsnOptions);
    }
}
