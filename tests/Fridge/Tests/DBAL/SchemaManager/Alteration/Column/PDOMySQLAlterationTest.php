<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\SchemaManager\Alteration\Column;

use Fridge\Tests\ConnectionUtility,
    Fridge\Tests\Fixture\MySQLFixture;

/**
 * PDO MySQL column alteration test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PDOMySQLAlterationTest extends AbstractAlterationTest
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
        if (self::$fixture !== null) {
            $this->connection = ConnectionUtility::getConnection(ConnectionUtility::PDO_MYSQL);
        }

        parent::setUp();
    }
}
