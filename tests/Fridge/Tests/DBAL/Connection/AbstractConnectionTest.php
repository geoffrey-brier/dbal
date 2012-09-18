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

use \Exception,
    \PDO;

use Fridge\DBAL\Connection\Connection,
    Fridge\DBAL\Event\Events,
    Fridge\DBAL\Type\Type;

/**
 * Executes the functional connection test suite on a specific database.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractConnectionTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\Tests\Fixture\FixtureInterface */
    static protected $fixture;

    /** @var \Fridge\DBAL\Connection\ConnectionInterface */
    protected $connection;

    /**
     * {@inheritdoc}
     */
    static public function setUpBeforeClass()
    {
        if (self::$fixture !== null) {
            self::$fixture->createSchema();
        }
    }

    /**
     * {@inheritdoc}
     */
    static public function tearDownAfterClass()
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
        if (self::$fixture === null) {
            $this->markTestSkipped();
        }

        self::$fixture->createDatas();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        if ($this->connection !== null) {
            $this->connection->close();

            unset($this->connection);
        }
    }

    public function testConnectAndClose()
    {
        $this->assertTrue($this->connection->connect());
        $this->assertTrue($this->connection->isConnected());

        $this->connection->close();
        $this->assertFalse($this->connection->isConnected());
    }

    public function testConnectIfConnectionIsAlreadyEstablished()
    {
        $this->connection->connect();
        $this->assertTrue($this->connection->connect());
    }

    public function testConnectDispatchEvent()
    {
        $eventDispatcherMock = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcher');
        $eventDispatcherMock
            ->expects($this->any())
            ->method('hasListeners')
            ->with($this->equalTo(Events::POST_CONNECT))
            ->will($this->returnValue(true));

        $eventDispatcherMock
            ->expects($this->once())
            ->method('dispatch');

        $this->connection->getConfiguration()->setEventDispatcher($eventDispatcherMock);

        $this->connection->connect();
    }

    public function testAdapter()
    {
        $this->assertInstanceOf('Fridge\DBAL\Adapter\ConnectionInterface', $this->connection->getAdapter());
    }

    public function testTransactionIsolation()
    {
        $this->assertSame(
            $this->connection->getPlatform()->getDefaultTransactionIsolation(),
            $this->connection->getTransactionIsolation()
        );

        if (!$this->connection->getPlatform()->supportTransactionIsolation()) {
            $this->setExpectedException('Fridge\DBAL\Exception\ConnectionException');
        }

        $this->connection->setTransactionIsolation(Connection::TRANSACTION_READ_COMMITTED);
    }

    public function testCharset()
    {
        $this->connection->setCharset('utf8');
    }

    public function testExecuteQueryWithoutParameters()
    {
        $this->assertEquals(
            array(self::$fixture->getQueryResult()),
            $this->connection->executeQuery(self::$fixture->getQuery())->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function testExecuteQueryWithNamedParameters()
    {
        $this->assertEquals(
            array(self::$fixture->getQueryResult()),
            $this->connection->executeQuery(
                self::$fixture->getQueryWithNamedParameters(),
                self::$fixture->getNamedQueryParameters()
            )->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function testExecuteQueryWithNamedTypedParameters()
    {
        $this->assertEquals(
            array(self::$fixture->getQueryResult()),
            $this->connection->executeQuery(
                self::$fixture->getQueryWithNamedParameters(),
                self::$fixture->getNamedTypedQueryParameters(),
                self::$fixture->getNamedQueryTypes()
            )->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function testExecuteQueryWithPartialNamedTypedParameters()
    {
        $this->assertEquals(
            array(self::$fixture->getQueryResult()),
            $this->connection->executeQuery(
                self::$fixture->getQueryWithNamedParameters(),
                self::$fixture->getNamedTypedQueryParameters(),
                self::$fixture->getPartialNamedQueryTypes()
            )->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function testExecuteQueryWithPositionalParameters()
    {
        $this->assertEquals(
            array(self::$fixture->getQueryResult()),
            $this->connection->executeQuery(
                self::$fixture->getQueryWithPositionalParameters(),
                self::$fixture->getPositionalQueryParameters()
            )->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function testExecuteQueryWithPositionalTypedParameters()
    {
        $this->assertEquals(
            array(self::$fixture->getQueryResult()),
            $this->connection->executeQuery(
                self::$fixture->getQueryWithPositionalParameters(),
                self::$fixture->getPositionalTypedQueryParameters(),
                self::$fixture->getPositionalQueryTypes()
            )->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function testExecuteQueryWithPartialPositionalTypedParameters()
    {
        $this->assertEquals(
            array(self::$fixture->getQueryResult()),
            $this->connection->executeQuery(
                self::$fixture->getQueryWithPositionalParameters(),
                self::$fixture->getPositionalTypedQueryParameters(),
                self::$fixture->getPartialPositionalQueryTypes()
            )->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function testExecuteQueryWithLogger()
    {
        $loggerMock = $this->getMock('Monolog\Logger', array(), array('foo'));
        $loggerMock
            ->expects($this->once())
            ->method('isHandling')
            ->will($this->returnValue(true));
        $loggerMock
            ->expects($this->once())
            ->method('addInfo');

        $this->connection->getConfiguration()->setLogger($loggerMock);

        $this->connection->executeQuery(self::$fixture->getQuery());
    }

    public function testExecuteUpdateWithoutParameters()
    {
        $this->assertSame(0, $this->connection->executeUpdate(self::$fixture->getUpdateQuery()));
    }

    public function testExecuteUpdateWithNamedParameters()
    {
        $this->assertSame(1, $this->connection->executeUpdate(
            self::$fixture->getUpdateQueryWithNamedParameters(),
            self::$fixture->getNamedQueryParameters()
        ));
    }

    public function testExecuteUpdateWithNamedTypedParameters()
    {
        $this->assertSame(1, $this->connection->executeUpdate(
            self::$fixture->getUpdateQueryWithNamedParameters(),
            self::$fixture->getNamedTypedQueryParameters(),
            self::$fixture->getNamedQueryTypes()
        ));
    }

    public function testExecuteUpdateWithPositionalParameters()
    {
        $this->assertSame(1, $this->connection->executeUpdate(
            self::$fixture->getUpdateQueryWithPositionalParameters(),
            self::$fixture->getPositionalQueryParameters()
        ));
    }

    public function testExecuteUpdateWithPositionalTypedParameters()
    {
        $this->assertSame(1, $this->connection->executeUpdate(
            self::$fixture->getUpdateQueryWithPositionalParameters(),
            self::$fixture->getPositionalTypedQueryParameters(),
            self::$fixture->getPositionalQueryTypes()
        ));
    }

    public function testExecuteUpdateWithLogger()
    {
        $loggerMock = $this->getMock('Monolog\Logger', array(), array('foo'));
        $loggerMock
            ->expects($this->once())
            ->method('isHandling')
            ->will($this->returnValue(true));
        $loggerMock
            ->expects($this->once())
            ->method('addInfo');

        $this->connection->getConfiguration()->setLogger($loggerMock);

        $this->connection->executeUpdate(
            self::$fixture->getUpdateQueryWithNamedParameters(),
            self::$fixture->getNamedQueryParameters()
        );
    }

    public function testFetchAll()
    {
        $this->assertEquals(
            array(self::$fixture->getQueryResult()),
            $this->connection->fetchAll(
                self::$fixture->getQueryWithNamedParameters(),
                self::$fixture->getNamedTypedQueryParameters(),
                self::$fixture->getNamedQueryTypes()
            )
        );
    }

    public function testFetchArray()
    {
        $this->assertEquals(
            array_values(self::$fixture->getQueryResult()),
            $this->connection->fetchArray(
                self::$fixture->getQueryWithNamedParameters(),
                self::$fixture->getNamedTypedQueryParameters(),
                self::$fixture->getNamedQueryTypes()
            )
        );
    }

    public function testFetchAssoc()
    {
        $this->assertEquals(
            self::$fixture->getQueryResult(),
            $this->connection->fetchAssoc(
                self::$fixture->getQueryWithNamedParameters(),
                self::$fixture->getNamedTypedQueryParameters(),
                self::$fixture->getNamedQueryTypes()
            )
        );
    }

    public function testFetchColumn()
    {
        $queryResult = self::$fixture->getQueryResult();

        $this->assertSame(
            $queryResult['carray'],
            $this->connection->fetchColumn(
                self::$fixture->getQueryWithNamedParameters(),
                self::$fixture->getNamedTypedQueryParameters(),
                self::$fixture->getNamedQueryTypes()
            )
        );
    }

    public function testInsertWithTypedParameters()
    {
        $this->assertSame(1, $this->connection->insert(
            'tcolumns',
            self::$fixture->getNamedTypedQueryParameters(),
            self::$fixture->getNamedQueryTypes()
        ));
    }

    public function testInsertWithPartialTypedParameters()
    {
        $this->assertSame(1, $this->connection->insert(
            'tcolumns',
            self::$fixture->getNamedTypedQueryParameters(),
            self::$fixture->getPartialNamedQueryTypes()
        ));
    }

    public function testUpdateWithoutExpression()
    {
        $datas = array_merge(
            self::$fixture->getNamedTypedQueryParameters(),
            array('carray' => array('bar' => 'foo'))
        );

        $this->assertSame(1, $this->connection->update(
            'tcolumns',
            $datas,
            self::$fixture->getNamedQueryTypes()
        ));
    }

    public function testUpdateWithTypedPositionalExpressionParameters()
    {
        $originalDatas = self::$fixture->getNamedTypedQueryParameters();

        $datas = array_merge(
            $originalDatas,
            array('carray' => array('bar' => 'foo'))
        );

        $this->assertSame(1, $this->connection->update(
            'tcolumns',
            $datas,
            self::$fixture->getNamedQueryTypes(),
            'carray = ?',
            array($originalDatas['carray']),
            array(Type::TARRAY)
        ));
    }

    public function testUpdateWithTypedNamedExpressionParameters()
    {
        $originalDatas = self::$fixture->getNamedTypedQueryParameters();

        $datas = array_merge(
            $originalDatas,
            array('carray' => array('bar' => 'foo'))
        );

        $this->assertSame(1, $this->connection->update(
            'tcolumns',
            $datas,
            self::$fixture->getNamedQueryTypes(),
            'carray = :carrayParameter',
            array('carrayParameter' => $originalDatas['carray']),
            array('carrayParameter' => Type::TARRAY)
        ));
    }

    public function testDeleteWithoutExpression()
    {
        $this->assertSame(1, $this->connection->delete('tcolumns'));
    }

    public function testDeleteWithTypedExpressionParameters()
    {
        $this->assertSame(1, $this->connection->delete(
            'tcolumns',
            'carray = :carrayParameter',
            array('carrayParameter' => array('foo' => 'bar')),
            array('carrayParameter' => Type::TARRAY)
        ));
    }

    public function testBeginTransaction()
    {
        $this->assertFalse($this->connection->inTransaction());
        $this->assertSame(0, $this->connection->getTransactionLevel());

        $this->connection->beginTransaction();

        $this->assertTrue($this->connection->inTransaction());
        $this->assertSame(1, $this->connection->getTransactionLevel());
    }

    public function testTransactionWithCommit()
    {
        $this->connection->beginTransaction();

        try {
            $this->connection->commit();
        } catch (Exception $e) {
            $this->connection->rollBack();

            $this->fail($e->getMessage());
        }

        $this->assertFalse($this->connection->inTransaction());
        $this->assertSame(0, $this->connection->getTransactionLevel());
    }

    public function testTransactionWithRollback()
    {
        $this->connection->beginTransaction();

        try {
            throw new Exception();
        } catch (Exception $e) {
            $this->connection->rollBack();
        }

        $this->assertFalse($this->connection->inTransaction());
        $this->assertSame(0, $this->connection->getTransactionLevel());
    }

    public function testNestedTransactionWithCommit()
    {
        $this->connection->beginTransaction();

        try {
            $this->connection->beginTransaction();
            $this->assertSame(2, $this->connection->getTransactionLevel());

            try {
                $this->connection->commit();
            } catch (Exception $e) {
                $this->connection->rollBack();

                $this->fail($e->getMessage());
            }

            $this->assertSame(1, $this->connection->getTransactionLevel());

            $this->connection->commit();
        } catch (Exception $e) {
            $this->connection->rollBack();

            if ($this->connection->getPlatform()->supportSavepoint()) {
                $this->fail($e->getMessage());
            }
        }

        $this->assertFalse($this->connection->inTransaction());
    }

    public function testNestedTransactionWithRollback()
    {
        $this->connection->beginTransaction();

        try {
            $this->connection->beginTransaction();
            $this->assertSame(2, $this->connection->getTransactionLevel());

            try {
                throw new Exception();
            } catch (Exception $e) {
                $this->connection->rollBack();
            }

            $this->assertSame(1, $this->connection->getTransactionLevel());

            throw new Exception();
        } catch (Exception $e) {
            $this->connection->rollBack();
        }

        $this->assertFalse($this->connection->inTransaction());
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\ConnectionException
     * @expectedExceptionMessage The connection does not have an active transaction.
     */
    public function testCommitWithoutTransaction()
    {
        $this->connection->commit();
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\ConnectionException
     */
    public function testRollbackWithoutTransaction()
    {
        $this->connection->rollback();
    }

    public function testQuoteWithDBALType()
    {
        $this->assertSame('\'foo\'', $this->connection->quote('foo', Type::STRING));
    }

    public function testQuoteWithPDOType()
    {
        $this->assertSame('\'foo\'', $this->connection->quote('foo', PDO::PARAM_STR));
    }

    public function testQuery()
    {
        $this->assertEquals(
            array(self::$fixture->getQueryResult()),
            $this->connection->query(self::$fixture->getQuery())->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function testPrepare()
    {
        $this->assertInstanceOf(
            'Fridge\DBAL\Statement\Statement',
            $this->connection->prepare(self::$fixture->getQueryWithNamedParameters())
        );
    }

    public function testExec()
    {
        $this->assertSame(0, $this->connection->exec(self::$fixture->getUpdateQuery()));
    }

    public function testLastInsertId()
    {
        $this->connection->lastInsertId();
    }

    public function testErrorCode()
    {
        try {
            $this->connection->exec('foo');

            $this->fail();
        } catch (Exception $e) {
            $this->assertSame($e->getCode(), $this->connection->errorCode());
        }
    }

    public function testErrorInfo()
    {
        try {
            $this->connection->exec('foo');

            $this->fail();
        } catch (Exception $e) {
            $errorInfo = $this->connection->errorInfo();

            $this->assertArrayHasKey(0, $errorInfo);
            $this->assertSame($e->getCode(), $errorInfo[0]);

            $this->assertArrayHasKey(1, $errorInfo);
            $this->assertInternalType('int', $errorInfo[1]);

            $this->assertArrayHasKey(2, $errorInfo);
            $this->assertInternalType('string', $errorInfo[2]);
        }
    }
}
