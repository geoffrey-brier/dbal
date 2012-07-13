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

use Fridge\DBAL\Base\PDO,
    Fridge\DBAL\Statement\Statement,
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

    /** @var \Fridge\DBAL\Base\StatementInterface */
    protected $baseStatementMock;

    /** @var \Fridge\DBAL\Connection\ConnectionInterface */
    protected $connectionMock;

    /** @var \Fridge\DBAL\Base\ConnectionInterface */
    protected $baseConnectionMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->baseStatementMock = $this->getMock('\Fridge\DBAL\Base\StatementInterface');

        $this->baseConnectionMock = $this->getMock('\Fridge\DBAL\Base\ConnectionInterface');
        $this->baseConnectionMock
            ->expects($this->any())
            ->method('prepare')
            ->with($this->equalTo('foo'), $this->equalTo(array('foo')))
            ->will($this->returnValue($this->baseStatementMock));

        $this->connectionMock = $this->getMock('\Fridge\DBAL\Connection\ConnectionInterface');
        $this->connectionMock
            ->expects($this->any())
            ->method('getBase')
            ->will($this->returnValue($this->baseConnectionMock));

        $this->statement = new Statement('foo', $this->connectionMock, array('foo'));
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->baseConnectionMock);
        unset($this->connectionMock);
        unset($this->baseStatementMock);
        unset($this->statement);
    }

    public function testInitialState()
    {
        $this->assertEquals($this->baseStatementMock, $this->statement->getBase());
        $this->assertEquals($this->connectionMock, $this->statement->getConnection());
        $this->assertEquals('foo', $this->statement->getSQL());
        $this->assertEquals(array('foo'), $this->statement->getOptions());
    }

    public function testIterator()
    {
        $this->assertEquals($this->baseStatementMock, $this->statement->getIterator());
    }

    public function testBindColumn()
    {
        $column = 'foo';
        $variable = 'bar';
        $type = 'foobar';
        $length = 10;
        $driverOptions = array('foo');

        $this->baseStatementMock
            ->expects($this->once())
            ->method('bindColumn')
            ->with(
                $this->equalTo($column),
                $this->equalTo($variable),
                $this->equalTo($type),
                $this->equalTo($length),
                $this->equalTo($driverOptions)
            );

        $this->statement->bindColumn($column, $variable, $type, $length, $driverOptions);
    }

    public function testBindParam()
    {
        $parameter = 'foo';
        $variable = 'bar';
        $type = 'foobar';
        $length = 10;
        $driverOptions = array('foo');

        $this->baseStatementMock
            ->expects($this->once())
            ->method('bindParam')
            ->with(
                $this->equalTo($parameter),
                $this->equalTo($variable),
                $this->equalTo($type),
                $this->equalTo($length),
                $this->equalTo($driverOptions)
            );

        $this->statement->bindParam($parameter, $variable, $type, $length, $driverOptions);
    }

    public function testBindValueWithoutType()
    {
        $platformMock = $this->getMock('\Fridge\DBAL\Platform\PlatformInterface', array(), array(), '', false);

        $this->connectionMock
            ->expects($this->once())
            ->method('getPlatform')
            ->will($this->returnValue($platformMock));

        $this->baseStatementMock
            ->expects($this->once())
            ->method('bindValue')
            ->with($this->equalTo('foo'), $this->equalTo('bar'), $this->equalTo(null));

        $this->statement->bindValue('foo', 'bar');
    }

    public function testBindValueWithPDOType()
    {
        $platformMock = $this->getMock('\Fridge\DBAL\Platform\PlatformInterface', array(), array(), '', false);

        $this->connectionMock
            ->expects($this->once())
            ->method('getPlatform')
            ->will($this->returnValue($platformMock));

        $this->baseStatementMock
            ->expects($this->once())
            ->method('bindValue')
            ->with($this->equalTo('foo'), $this->equalTo('bar'), $this->equalTo(PDO::PARAM_INT));

        $this->statement->bindValue('foo', 'bar', PDO::PARAM_INT);
    }

    public function testBindValueWithFridgeType()
    {
        $platformMock = $this->getMock('\Fridge\DBAL\Platform\PlatformInterface', array(), array(), '', false);

        $this->connectionMock
            ->expects($this->once())
            ->method('getPlatform')
            ->will($this->returnValue($platformMock));

        $this->baseStatementMock
            ->expects($this->once())
            ->method('bindValue')
            ->with($this->equalTo('foo'), $this->equalTo(true), $this->equalTo(PDO::PARAM_BOOL));

        $this->statement->bindValue('foo', true, Type::BOOLEAN);
    }

    public function testCloseCursor()
    {
        $this->baseStatementMock
            ->expects($this->once())
            ->method('closeCursor');

        $this->statement->closeCursor();
    }

    public function testColumnCount()
    {
        $this->baseStatementMock
            ->expects($this->once())
            ->method('columnCount');

        $this->statement->columnCount();
    }

    public function testDebugDumpParams()
    {
        $this->baseStatementMock
            ->expects($this->once())
            ->method('debugDumpParams');

        $this->statement->debugDumpParams();
    }

    public function testErrorCode()
    {
        $this->baseStatementMock
            ->expects($this->once())
            ->method('errorCode');

        $this->statement->errorCode();
    }

    public function testErrorInfo()
    {
        $this->baseStatementMock
            ->expects($this->once())
            ->method('errorInfo');

        $this->statement->errorInfo();
    }

    public function testExecute()
    {
        $this->baseStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->equalTo(array('foo')));

        $this->statement->execute(array('foo'));
    }

    public function testFetch()
    {
        $this->baseStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo(1), $this->equalTo(2), $this->equalTo(3));

        $this->statement->fetch(1, 2, 3);
    }

    public function testFetchAll()
    {
        $this->baseStatementMock
            ->expects($this->once())
            ->method('fetchAll')
            ->with(
                $this->equalTo(1),
                $this->equalTo('foo'),
                $this->equalTo(array('bar'))
            );

        $this->statement->fetchAll(1, 'foo', array('bar'));
    }

    public function testFetchColumn()
    {
        $this->baseStatementMock
            ->expects($this->once())
            ->method('fetchColumn')
            ->with($this->equalTo(1));

        $this->statement->fetchColumn(1);
    }

    public function testFetchObject()
    {
        $this->baseStatementMock
            ->expects($this->once())
            ->method('fetchObject')
            ->with($this->equalTo('stdClass'), $this->equalTo(array('foo')));

        $this->statement->fetchObject('stdClass', array('foo'));
    }

    public function testGetAttribute()
    {
        $this->baseStatementMock
            ->expects($this->once())
            ->method('getAttribute')
            ->with($this->equalTo('foo'));

        $this->statement->getAttribute('foo');
    }

    public function testNextRowset()
    {
        $this->baseStatementMock
            ->expects($this->once())
            ->method('nextRowset');

        $this->statement->nextRowset();
    }

    public function testRowCount()
    {
        $this->baseStatementMock
            ->expects($this->once())
            ->method('rowCount');

        $this->statement->rowCount();
    }

    public function testSetAttribute()
    {
        $this->baseStatementMock
            ->expects($this->once())
            ->method('setAttribute')
            ->with($this->equalTo('foo'), $this->equalTo('bar'));

        $this->statement->setAttribute('foo', 'bar');
    }

    public function testSetFetchMode()
    {
        $this->baseStatementMock
            ->expects($this->once())
            ->method('setFetchMode')
            ->with(
                $this->equalTo('foo'),
                $this->equalTo('bar'),
                $this->equalTo('foobar')
            );

        $this->statement->setFetchMode('foo', 'bar', 'foobar');
    }
}
