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
 * Mysqli schema manager tests.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class MysqliSchemaManagerTest extends AbstractMySQLSchemaManagerTest
{
    /**
     * {@inheritdoc}
     */
    static public function setUpBeforeClass()
    {
        if (ConnectionUtility::hasConnection(ConnectionUtility::MYSQLI)) {
            self::$fixture = new MySQLFixture(ConnectionUtility::MYSQLI);
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
        if (ConnectionUtility::hasConnection(ConnectionUtility::MYSQLI)) {
            $this->schemaManager = new MySQLSchemaManager(ConnectionUtility::getConnection(ConnectionUtility::MYSQLI));
        }

        parent::setUp();
    }
}
