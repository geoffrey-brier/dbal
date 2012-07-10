<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\Connection;

use Fridge\Tests\ConnectionUtility,
    Fridge\Tests\Fixture\PostgreSQLFixture;

/**
 * PDO PostgreSQL functional connection test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PDOPostgreSQLConnectionTest extends AbstractConnectionTest
{
    /**
     * {@inheritdoc}
     */
    static public function setUpBeforeClass()
    {
        if (ConnectionUtility::hasConnection(ConnectionUtility::PDO_PGSQL)) {
            self::$fixture = new PostgreSQLFixture();
        }

        parent::setUpBeforeClass();
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (ConnectionUtility::hasConnection(ConnectionUtility::PDO_PGSQL)) {
            $this->connection = ConnectionUtility::getConnection(ConnectionUtility::PDO_PGSQL);
        }

        parent::setUp();
    }
}
