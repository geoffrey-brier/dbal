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

use Fridge\DBAL\Driver\PDO\MySQLDriver,
    Fridge\Tests\PHPUnitUtility,
    Fridge\Tests\Fixture\MySQLFixture;

/**
 * PDO MySQL driver test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PDOMySQLDriverTest extends AbstractDriverTest
{
    /**
     * {@inheritdoc}
     */
    static public function setUpBeforeClass()
    {
        if (PHPUnitUtility::hasSettings(PHPUnitUtility::PDO_MYSQL)) {
            self::$fixtures = new MySQLFixture();
        }

        parent::setUpBeforeClass();
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (PHPUnitUtility::hasSettings(PHPUnitUtility::PDO_MYSQL)) {
            $this->driver = new MySQLDriver();
        }

        parent::setUp();
    }

    public function testConnectWithUnixSocket()
    {
        $settings = self::$fixtures->getSettings();

        unset($settings['host']);
        unset($settings['port']);

        $settings['unix_socket'] = '/var/run/mysqld/mysqld.sock';

        $this->assertInstanceOf(
            'Fridge\DBAL\Adapter\ConnectionInterface',
            $this->driver->connect($settings, $settings['username'], $settings['password'])
        );
    }

    public function testConnectWithCharset()
    {
        $settings = self::$fixtures->getSettings();
        $settings['charset'] = 'utf8';

        $this->assertInstanceOf(
            'Fridge\DBAL\Adapter\ConnectionInterface',
            $this->driver->connect($settings, $settings['username'], $settings['password'])
        );
    }
}
