<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\SchemaManager;

use Fridge\DBAL\SchemaManager\PostgreSQLSchemaManager,
    Fridge\Tests\ConnectionUtility,
    Fridge\Tests\Fixture\PostgreSQLFixture;

/**
 * PDO PostgreSQL schema manager test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PDOPgSQLSchemaManagerTest extends AbstractSchemaManagerTest
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
            $this->schemaManager = new PostgreSQLSchemaManager(ConnectionUtility::getConnection(ConnectionUtility::PDO_PGSQL));
        }

        parent::setUp();
    }

    public function testGetDatabaseWithoutConfiguredDatabase()
    {
        $this->schemaManager->getConnection()->setDatabase(null);

        $this->assertEquals('postgres', $this->schemaManager->getDatabase());
    }
}
