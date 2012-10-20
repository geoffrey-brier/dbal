<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\SchemaManager\Alteration\Table;

use Fridge\Tests\ConnectionUtility,
    Fridge\Tests\Fixture\PostgreSQLFixture;

/**
 * PDO PostgreSQL table alteration test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PDOPostgreSQLAlterationTest extends AbstractAlterationTest
{
    /**
     * {@inheritdoc}
     */
    static public function setUpBeforeClass()
    {
        if (ConnectionUtility::hasConnection(ConnectionUtility::PDO_PGSQL)) {
            self::$fixture = new PostgreSQLFixture();
        } else {
            self::$fixture = null;
        }

        parent::setUpBeforeClass();
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (self::$fixture !== null) {
            $this->connection = ConnectionUtility::getConnection(ConnectionUtility::PDO_PGSQL);
        }

        parent::setUp();
    }
}
