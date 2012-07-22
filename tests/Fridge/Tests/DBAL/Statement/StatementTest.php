<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\Statement;

use \PDO;

use Fridge\DBAL\Statement\Statement,
    Fridge\DBAL\Type\Type;

/**
 * Statement test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class StatementTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\Statement\Statement */
    protected $statement;

    /** @var \Fridge\DBAL\Adapter\StatementInterface */
    protected $adapterStatementMock;

    /** @var \Fridge\DBAL\Connection\ConnectionInterface */
    protected $connectionMock;

    /** @var \Fridge\DBAL\Adapter\ConnectionInterface */
    protected $adapterConnectionMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->adapterStatementMock = $this->getMock('\Fridge\DBAL\Adapter\StatementInterface');

        $this->adapterConnectionMock = $this->getMock('\Fridge\DBAL\Adapter\ConnectionInterface');
        $this->adapterConnectionMock
            ->expects($this->any())
            ->method('prepare')
            ->with($this->equalTo('foo'))
            ->will($this->returnValue($this->adapterStatementMock));

        $this->connectionMock = $this->getMock('\Fridge\DBAL\Connection\ConnectionInterface');
        $this->connectionMock
            ->expects($this->any())
            ->method('getAdapter')
            ->will($this->returnValue($this->adapterConnectionMock));

        $this->statement = new Statement('foo', $this->connectionMock);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->adapterConnectionMock);
        unset($this->connectionMock);
        unset($this->adapterStatementMock);
        unset($this->statement);
    }

    public function testInitialState()
    {
        $this->assertEquals($this->adapterStatementMock, $this->statement->getAdapter());
        $this->assertEquals($this->connectionMock, $this->statement->getConnection());
        $this->assertEquals('foo', $this->statement->getSQL());
    }

    public function testIterator()
    {
        $this->assertEquals($this->adapterStatementMock, $this->statement->getIterator());
    }

    public function testBindColumn()
    {
        $column = 'foo';
        $variable = 'bar';
        $type = 'foobar';
        $length = 10;
        $driverOptions = array('foo');

        $this->adapterStatementMock
            ->expects($this->once())
            ->method('bindColumn')
            ->with(
                $this->equalTo($column),
                $this->equalTo($variable),
                $this->equalTo($type),
                $this->equalTo($length),
                $this->equalTo($driverOptions)
            )
            ->will($this->returnValue('bar'));

        $this->assertEquals(
            'bar',
            $this->statement->bindColumn($column, $variable, $type, $length, $driverOptions)
        );
    }

    public function testBindParam()
    {
        $parameter = 'foo';
        $variable = 'bar';
        $type = 'foobar';
        $length = 10;
        $driverOptions = array('foo');

        $this->adapterStatementMock
            ->expects($this->once())
            ->method('bindParam')
            ->with(
                $this->equalTo($parameter),
                $this->equalTo($variable),
                $this->equalTo($type),
                $this->equalTo($length),
                $this->equalTo($driverOptions)
            )
            ->will($this->returnValue('bar'));

        $this->assertEquals(
            'bar',
            $this->statement->bindParam($parameter, $variable, $type, $length, $driverOptions)
        );
    }

    public function testBindValueWithoutType()
    {
        $platformMock = $this->getMock('\Fridge\DBAL\Platform\PlatformInterface', array(), array(), '', false);

        $this->connectionMock
            ->expects($this->once())
            ->method('getPlatform')
            ->will($this->returnValue($platformMock));

        $this->adapterStatementMock
            ->expects($this->once())
            ->method('bindValue')
            ->with(
                $this->equalTo('foo'),
                $this->equalTo('bar'),
                $this->equalTo(null)
            )
            ->will($this->returnValue('bar'));

        $this->assertEquals('bar', $this->statement->bindValue('foo', 'bar'));
    }

    public function testBindValueWithPDOType()
    {
        $platformMock = $this->getMock('\Fridge\DBAL\Platform\PlatformInterface', array(), array(), '', false);

        $this->connectionMock
            ->expects($this->once())
            ->method('getPlatform')
            ->will($this->returnValue($platformMock));

        $this->adapterStatementMock
            ->expects($this->once())
            ->method('bindValue')
            ->with(
                $this->equalTo('foo'),
                $this->equalTo('bar'),
                $this->equalTo(PDO::PARAM_INT)
            )
            ->will($this->returnValue('bar'));

        $this->assertEquals(
            'bar',
            $this->statement->bindValue('foo', 'bar', PDO::PARAM_INT)
        );
    }

    public function testBindValueWithFridgeType()
    {
        $platformMock = $this->getMock('\Fridge\DBAL\Platform\PlatformInterface', array(), array(), '', false);

        $this->connectionMock
            ->expects($this->once())
            ->method('getPlatform')
            ->will($this->returnValue($platformMock));

        $this->adapterStatementMock
            ->expects($this->once())
            ->method('bindValue')
            ->with(
                $this->equalTo('foo'),
                $this->equalTo(true),
                $this->equalTo(PDO::PARAM_BOOL)
            )
            ->will($this->returnValue('bar'));

        $this->assertEquals(
            'bar',
            $this->statement->bindValue('foo', true, Type::BOOLEAN)
        );
    }

    public function testCloseCursor()
    {
        $this->adapterStatementMock
            ->expects($this->once())
            ->method('closeCursor')
            ->will($this->returnValue('bar'));

        $this->assertEquals('bar', $this->statement->closeCursor());
    }

    public function testColumnCount()
    {
        $this->adapterStatementMock
            ->expects($this->once())
            ->method('columnCount')
            ->will($this->returnValue('bar'));

        $this->assertEquals('bar', $this->statement->columnCount());
    }

    public function testDebugDumpParams()
    {
        $this->adapterStatementMock
            ->expects($this->once())
            ->method('debugDumpParams')
            ->will($this->returnValue('bar'));

        $this->assertEquals('bar', $this->statement->debugDumpParams());
    }

    public function testErrorCode()
    {
        $this->adapterStatementMock
            ->expects($this->once())
            ->method('errorCode')
            ->will($this->returnValue('bar'));

        $this->assertEquals('bar', $this->statement->errorCode());
    }

    public function testErrorInfo()
    {
        $this->adapterStatementMock
            ->expects($this->once())
            ->method('errorInfo')
            ->will($this->returnValue('bar'));

        $this->assertEquals('bar', $this->statement->errorInfo());
    }

    public function testExecute()
    {
        $this->adapterStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->equalTo(array('foo')))
            ->will($this->returnValue('bar'));

        $this->assertEquals('bar', $this->statement->execute(array('foo')));
    }

    public function testFetch()
    {
        $this->adapterStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo(1), $this->equalTo(2), $this->equalTo(3))
            ->will($this->returnValue('bar'));

        $this->assertEquals('bar', $this->statement->fetch(1, 2, 3));
    }

    public function testFetchAll()
    {
        $this->adapterStatementMock
            ->expects($this->once())
            ->method('fetchAll')
            ->with(
                $this->equalTo(1),
                $this->equalTo('foo'),
                $this->equalTo(array('bar'))
            )
            ->will($this->returnValue('bar'));

        $this->assertEquals(
            'bar',
            $this->statement->fetchAll(1, 'foo', array('bar'))
        );
    }

    public function testFetchColumn()
    {
        $this->adapterStatementMock
            ->expects($this->once())
            ->method('fetchColumn')
            ->with($this->equalTo(1))
            ->will($this->returnValue('bar'));

        $this->assertEquals('bar', $this->statement->fetchColumn(1));
    }

    public function testFetchObject()
    {
        $this->adapterStatementMock
            ->expects($this->once())
            ->method('fetchObject')
            ->with($this->equalTo('stdClass'), $this->equalTo(array('foo')))
            ->will($this->returnValue('bar'));

        $this->assertEquals('bar', $this->statement->fetchObject('stdClass', array('foo')));
    }

    public function testGetAttribute()
    {
        $this->adapterStatementMock
            ->expects($this->once())
            ->method('getAttribute')
            ->with($this->equalTo('foo'))
            ->will($this->returnValue('bar'));

        $this->assertEquals('bar', $this->statement->getAttribute('foo'));
    }

    public function testNextRowset()
    {
        $this->adapterStatementMock
            ->expects($this->once())
            ->method('nextRowset')
            ->will($this->returnValue('bar'));

        $this->assertEquals('bar', $this->statement->nextRowset());
    }

    public function testRowCount()
    {
        $this->adapterStatementMock
            ->expects($this->once())
            ->method('rowCount')
            ->will($this->returnValue('bar'));

        $this->assertEquals('bar', $this->statement->rowCount());
    }

    public function testSetAttribute()
    {
        $this->adapterStatementMock
            ->expects($this->once())
            ->method('setAttribute')
            ->with($this->equalTo('foo'), $this->equalTo('bar'))
            ->will($this->returnValue('bar'));

        $this->assertEquals(
            'bar',
            $this->statement->setAttribute('foo', 'bar')
        );
    }

    public function testSetFetchMode()
    {
        $this->adapterStatementMock
            ->expects($this->once())
            ->method('setFetchMode')
            ->with(
                $this->equalTo('foo'),
                $this->equalTo('bar'),
                $this->equalTo('foobar')
            )
            ->will($this->returnValue('bar'));

        $this->assertEquals(
            'bar',
            $this->statement->setFetchMode('foo', 'bar', 'foobar')
        );
    }
}
