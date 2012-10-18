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

use Fridge\DBAL\Connection\Connection;

/**
 * Executes the unit connection tests.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\Driver\DriverInterface */
    protected $driverMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $platformMock = $this->getMock('Fridge\DBAL\Platform\PlatformInterface');
        $platformMock
            ->expects($this->any())
            ->method('getDefaultTransactionIsolation')
            ->will($this->returnValue('foo'));

        $this->driverMock = $this->getMock('Fridge\DBAL\Driver\DriverInterface');
        $this->driverMock
            ->expects($this->any())
            ->method('getPlatform')
            ->will($this->returnValue($platformMock));
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->driverMock);
    }

    public function testConnectionWithConfiguration()
    {
        $configurationMock = $this->getMock('Fridge\DBAL\Configuration');
        $connection = new Connection(array(), $this->driverMock, $configurationMock);

        $this->assertSame($configurationMock, $connection->getConfiguration());
    }

    public function testConnectionWithoutConfiguration()
    {
        $connection = new Connection(array(), $this->driverMock);

        $this->assertInstanceOf('Fridge\DBAL\Configuration', $connection->getConfiguration());
    }

    public function testDriver()
    {
        $connection = new Connection(array(), $this->driverMock);

        $this->assertSame($this->driverMock, $connection->getDriver());
    }

    public function testPlatform()
    {
        $connection = new Connection(array(), $this->driverMock);

        $this->driverMock
            ->expects($this->once())
            ->method('getPlatform');

        $connection->getPlatform();
    }

    public function testSchemaManager()
    {
        $connection = new Connection(array(), $this->driverMock);

        $this->driverMock
            ->expects($this->once())
            ->method('getSchemaManager')
            ->with($this->equalTo($connection));

        $connection->getSchemaManager();
    }

    public function testQueryBuilder()
    {
        $connection = new Connection(array(), $this->driverMock);
        $queryBuilder = $connection->createQueryBuilder();

        $this->assertInstanceOf('Fridge\DBAL\Query\QueryBuilder', $queryBuilder);
        $this->assertSame($connection, $queryBuilder->getConnection());
    }

    public function testExpressionBuilder()
    {
        $connection = new Connection(array(), $this->driverMock);
        $expressionBuilder = $connection->getExpressionBuilder();

        $this->assertInstanceOf('Fridge\DBAL\Query\Expression\ExpressionBuilder', $expressionBuilder);
    }

    public function testUsername()
    {
        $connection = new Connection(array('username' => 'foo'), $this->driverMock);

        $this->assertSame('foo', $connection->getUsername());
    }

    public function testSetUsername()
    {
        $connection = new Connection(array(), $this->driverMock);
        $connection->setUsername('foo');

        $this->assertSame('foo', $connection->getUsername());
    }

    public function testPassword()
    {
        $connection = new Connection(array('password' => 'foo'), $this->driverMock);

        $this->assertSame('foo', $connection->getPassword());
    }

    public function testSetPassword()
    {
        $connection = new Connection(array(), $this->driverMock);
        $connection->setPassword('foo');

        $this->assertSame('foo', $connection->getPassword());
    }

    public function testDatabase()
    {
        $schemaManagerMock = $this->getMock('\Fridge\DBAL\SchemaManager\SchemaManagerInterface');
        $schemaManagerMock
            ->expects($this->once())
            ->method('getDatabase');

        $this->driverMock
            ->expects($this->any())
            ->method('getSchemaManager')
            ->will($this->returnValue($schemaManagerMock));

        $connection = new Connection(array(), $this->driverMock);

        $connection->getDatabase();
    }

    public function testSetDatabase()
    {
        $connection = new Connection(array(), $this->driverMock);
        $connection->setDatabase('foo');

        $parameters = $connection->getParameters();

        $this->assertArrayHasKey('dbname', $parameters);
        $this->assertSame('foo', $parameters['dbname']);
    }

    public function testHost()
    {
        $connection = new Connection(array('host' => 'foo'), $this->driverMock);

        $this->assertSame('foo', $connection->getHost());
    }

    public function testSetHost()
    {
        $connection = new Connection(array(), $this->driverMock);
        $connection->setHost('foo');

        $this->assertSame('foo', $connection->getHost());
    }

    public function testPort()
    {
        $connection = new Connection(array('port' => 'foo'), $this->driverMock);

        $this->assertSame('foo', $connection->getPort());
    }

    public function testSetPort()
    {
        $connection = new Connection(array(), $this->driverMock);
        $connection->setPort(1000);

        $this->assertSame(1000, $connection->getPort());
    }

    public function testDriverOptions()
    {
        $connection = new Connection(array('driver_options' => array('foo' => 'bar')), $this->driverMock);

        $this->assertSame(array('foo' => 'bar'), $connection->getDriverOptions());
    }

    public function testSetDriverOptions()
    {
        $connection = new Connection(array(), $this->driverMock);

        $driverOptions = array('foo');
        $connection->setDriverOptions($driverOptions);

        $this->assertSame($driverOptions, $connection->getDriverOptions());
    }

    public function testParameters()
    {
        $connection = new Connection(array('foo' => 'bar'), $this->driverMock);

        $this->assertSame(array('foo' => 'bar'), $connection->getParameters());
    }

    public function testSetParameters()
    {
        $oldParameters = array(
            'foo' => 'bar',
            'bar' => 'foo',
        );

        $newParameters = array(
            'bar' => 'bar',
            'baz' => 'foo',
        );

        $expectedParameters = array(
            'foo' => 'bar',
            'bar' => 'bar',
            'baz' => 'foo',
        );

        $connection = new Connection($oldParameters, $this->driverMock);
        $connection->setParameters($newParameters);

        $this->assertSame($expectedParameters, $connection->getParameters());

    }

    /**
     * @expectedException \Fridge\DBAL\Exception\ConnectionException
     * @expectedExceptionMessage The connection does not support transaction isolation.
     */
    public function testTransationIsolationNotSupported()
    {
        $platformMock = $this->getMock('Fridge\DBAL\Platform\PlatformInterface');
        $platformMock
            ->expects($this->any())
            ->method('supportTransactionIsolation')
            ->will($this->returnValue(false));

        $driverMock = $this->getMock('Fridge\DBAL\Driver\DriverInterface');
        $driverMock
            ->expects($this->any())
            ->method('getPlatform')
            ->will($this->returnValue($platformMock));

        $connection = new Connection(array(), $driverMock);
        $connection->setTransactionIsolation(Connection::TRANSACTION_READ_COMMITTED);
    }
}
