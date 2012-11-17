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

use Fridge\DBAL\SchemaManager\MySQLSchemaManager,
    Fridge\Tests\ConnectionUtility,
    Fridge\Tests\Fixture\MySQLFixture;

/**
 * PDO MySQL schema manager test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PDOMySQLSchemaManagerTest extends AbstractMySQLSchemaManagerTest
{
    /**
     * {@inheritdoc}
     */
    static public function setUpBeforeClass()
    {
        if (ConnectionUtility::hasConnection(ConnectionUtility::PDO_MYSQL)) {
            self::$fixture = new MySQLFixture(ConnectionUtility::PDO_MYSQL);
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
        if (ConnectionUtility::hasConnection(ConnectionUtility::PDO_MYSQL)) {
            $this->schemaManager = new MySQLSchemaManager(ConnectionUtility::getConnection(ConnectionUtility::PDO_MYSQL));
        }

        parent::setUp();
    }
}
