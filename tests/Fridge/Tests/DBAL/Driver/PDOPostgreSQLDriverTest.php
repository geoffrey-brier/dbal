<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\Driver;

use Fridge\DBAL\Driver\PDO\PostgreSQLDriver,
    Fridge\Tests\PHPUnitUtility,
    Fridge\Tests\Fixture\PostgreSQLFixture;

/**
 * PDO PostgreSQL driver test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PDOPostgreSQLDriverTest extends AbstractDriverTest
{
    /**
     * {@inheritdoc}
     */
    static public function setUpBeforeClass()
    {
        if (PHPUnitUtility::hasSettings(PHPUnitUtility::PDO_PGSQL)) {
            self::$fixtures = new PostgreSQLFixture();
        } else {
            self::$fixtures = null;
        }

        parent::setUpBeforeClass();
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (PHPUnitUtility::hasSettings(PHPUnitUtility::PDO_PGSQL)) {
            $this->driver = new PostgreSQLDriver();
        }

        parent::setUp();
    }
}
