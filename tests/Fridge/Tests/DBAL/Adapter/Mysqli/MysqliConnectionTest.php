<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\Adapter\Mysqli;

use \Exception;

use Fridge\DBAL\Adapter\Mysqli\MysqliConnection,
    Fridge\Tests\PHPUnitUtility,
    Fridge\Tests\Fixture\MySQLFixture;

/**
 * Mysqli connection adapter tests.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class MysqliConnectionTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\Tests\Fixture\FixtureInterface */
    static protected $fixture;

    /** @var \Fridge\DBAL\Adapter\Mysqli\Connection */
    protected $connection;

    /**
     * {@inheritdoc}
     */
    static public function setUpBeforeClass()
    {
        if (PHPUnitUtility::hasSettings(PHPUnitUtility::MYSQLI)) {
            self::$fixture = new MySQLFixture(PHPUnitUtility::MYSQLI);
            self::$fixture->create();
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
    public function setUp()
    {
        if (self::$fixture === null) {
            $this->markTestSkipped();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        unset($this->connection);
    }

    /**
     * Sets up a mysqli connection adapter.
     */
    protected function setUpConnection()
    {
        $settings = self::$fixture->getSettings();
        $this->connection = new MysqliConnection($settings, $settings['username'], $settings['password']);
    }

    public function testConnectionWithUsernameAndPassword()
    {
        $settings = self::$fixture->getSettings();

        $this->connection = new MysqliConnection(array(), $settings['username'], $settings['password']);

        $this->assertInstanceOf('\mysqli', $this->connection->getMysqli());
    }

    public function testConnectionWithHost()
    {
        $settings = self::$fixture->getSettings();

        $this->connection = new MysqliConnection(
            array('host' => $settings['host']),
            $settings['username'],
            $settings['password']
        );

        $this->assertInstanceOf('\mysqli', $this->connection->getMysqli());
    }

    public function testConnectionWithDatabase()
    {
        $settings = self::$fixture->getSettings();

        $this->connection = new MysqliConnection(
            array('dbname' => $settings['dbname']),
            $settings['username'],
            $settings['password']
        );

        $this->assertInstanceOf('\mysqli', $this->connection->getMysqli());
    }

    public function testConnectionWithPort()
    {
        $settings = self::$fixture->getSettings();

        $this->connection = new MysqliConnection(
            array('port' => $settings['port']),
            $settings['username'],
            $settings['password']
        );

        $this->assertInstanceOf('\mysqli', $this->connection->getMysqli());
    }

    public function testConnectionWithUnixSocket()
    {
        $settings = self::$fixture->getSettings();

        $this->connection = new MysqliConnection(
            array('unix_socket' => ini_get('mysqli.default_socket')),
            $settings['username'],
            $settings['password']
        );

        $this->assertInstanceOf('\mysqli', $this->connection->getMysqli());
    }

    public function testConnectionWithValidCharset()
    {
        $settings = self::$fixture->getSettings();

        $this->connection = new MysqliConnection(array('charset' => 'utf8'), $settings['username'], $settings['password']);

        $this->assertInstanceOf('\mysqli', $this->connection->getMysqli());
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\Adapter\MysqliException
     */
    public function testConnectionWithInvalidCharset()
    {
        $settings = self::$fixture->getSettings();

        $this->connection = new MysqliConnection(array('charset' => 'foo'), $settings['username'], $settings['password']);
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\Adapter\MysqliException
     */
    public function testConnectionWithInvalidParameters()
    {
        $this->connection = new MysqliConnection(array(), 'foo', 'bar');
    }

    public function testBeginTransaction()
    {
        $this->setUpConnection();

        $this->assertTrue($this->connection->beginTransaction());
        $this->assertTrue($this->connection->inTransaction());
    }

    public function testCommit()
    {
        $this->setUpConnection();

        $this->connection->beginTransaction();

        $this->assertTrue($this->connection->commit());
        $this->assertFalse($this->connection->inTransaction());
    }

    public function testRollback()
    {
        $this->setUpConnection();

        $this->connection->beginTransaction();

        $this->assertTrue($this->connection->rollBack());
        $this->assertFalse($this->connection->inTransaction());
    }

    public function testQuote()
    {
        $this->setUpConnection();

        $this->assertSame('\'foo\'', $this->connection->quote('foo'));
    }

    public function testPrepare()
    {
        $this->setUpConnection();

        $this->assertInstanceOf(
            '\Fridge\DBAL\Adapter\Mysqli\MysqliStatement',
            $this->connection->query(self::$fixture->getQuery())
        );
    }

    public function testQuery()
    {
        $this->setUpConnection();

        $this->assertInstanceOf(
            '\Fridge\DBAL\Adapter\Mysqli\MysqliStatement',
            $this->connection->query(self::$fixture->getQuery())
        );
    }

    public function testExec()
    {
        $this->setUpConnection();

        $this->assertSame(1, $this->connection->exec(self::$fixture->getUpdateQuery()));
    }

    public function testLastInsertId()
    {
        $this->setUpConnection();

        $this->assertSame(0, $this->connection->lastInsertId());
    }

    public function testErrorCode()
    {
        $this->setUpConnection();

        try {
            $this->connection->exec('foo');

            $this->fail();
        } catch (Exception $e) {
            $this->assertSame($e->getCode(), $this->connection->errorCode());
        }
    }

    public function testErrorInfo()
    {
        $this->setUpConnection();

        try {
            $this->connection->exec('foo');

            $this->fail();
        } catch (Exception $e) {
            $errorInfo = $this->connection->errorInfo();

            $this->assertArrayHasKey(0, $errorInfo);
            $this->assertSame($e->getCode(), $errorInfo[0]);

            $this->assertArrayHasKey(1, $errorInfo);
            $this->assertSame($e->getCode(), $errorInfo[1]);

            $this->assertArrayHasKey(2, $errorInfo);
            $this->assertSame($e->getMessage(), $errorInfo[2]);
        }
    }
}
