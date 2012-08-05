<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\Adapter\PDO;

use \PDO;

use Fridge\DBAL\Adapter\PDO\Connection,
    Fridge\Tests\PHPUnitUtility,
    Fridge\Tests\Fixture\MySQLFixture;

/**
 * PDO connection adapter tests.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\Tests\Fixture\FixtureInterface */
    static protected $fixture;

    /** @var \Fridge\DBAL\Adapter\PDO\Connection */
    protected $connection;

    /**
     * {@inheritdoc}
     */
    static public function setUpBeforeClass()
    {
        if (PHPUnitUtility::hasSettings(PHPUnitUtility::PDO_MYSQL)) {
            self::$fixture = new MySQLFixture();
            self::$fixture->createSchema();
        }
    }

    /**
     * {@inheritdoc}
     */
    static public function tearDownAfterCLass()
    {
        if (self::$fixture !== null) {
            self::$fixture->drop();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (!PHPUnitUtility::hasSettings(PHPUnitUtility::PDO_MYSQL)) {
            $this->markTestSkipped();
        }

        $setting = PHPUnitUtility::getSettings(PHPUnitUtility::PDO_MYSQL);

        $dsnOptions = array();

        foreach ($setting as $dsnKey => $dsnSetting) {
            if (in_array($dsnKey, array('dbname', 'host', 'port'))) {
                $dsnOptions[] = $dsnKey.'='.$dsnSetting;
            }
        }

        $dsn = substr($setting['driver'], 4).':'.implode(';', $dsnOptions);
        $username = $setting['username'];
        $password = $setting['password'];

        $this->connection = new Connection($dsn, $username, $password);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->connection);
    }

    public function testAttribute()
    {
        $this->assertEquals(PDO::ERRMODE_EXCEPTION, $this->connection->getAttribute(PDO::ATTR_ERRMODE));

        $this->assertEquals(
            array('Fridge\DBAL\Adapter\PDO\Statement', array()),
            $this->connection->getAttribute(PDO::ATTR_STATEMENT_CLASS)
        );
    }
}
