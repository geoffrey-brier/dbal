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
        if (ConnectionUtility::hasConnection(ConnectionUtility::PDO_PGSQL)) {
            $this->connection = ConnectionUtility::getConnection(ConnectionUtility::PDO_PGSQL);
        }

        parent::setUp();
    }

    /**
     * {@inheritdoc}
     */
    protected function assertQueryResult($expectedResult, $actualResult)
    {
        $cblobIndex = 'cblob';
        if (!array_key_exists($cblobIndex, $expectedResult[0])) {
            $cblobIndex = 2;
        }

        $expectedCblob = $expectedResult[0][$cblobIndex];
        unset($expectedResult[0][$cblobIndex]);

        $actualCblob = $actualResult[0][$cblobIndex];
        unset($actualResult[0][$cblobIndex]);

        $this->assertEquals($expectedResult, $actualResult);

        if (is_resource($actualCblob)) {
            rewind($actualCblob);
            $actualCblob = fread($actualCblob, mb_strlen($expectedCblob));
        }

        $this->assertSame($expectedCblob, $actualCblob);
    }
}
