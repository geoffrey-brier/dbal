<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\Query;

use Fridge\DBAL\Query\QueryBuilder,
    Fridge\DBAL\Query\Expression\CompositeExpression,
    Fridge\DBAL\Type\Type;

/**
 * Query builder test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class QueryBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\Query\QueryBuilder */
    protected $queryBuilder;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $connectionMock = $this->getMock('Fridge\DBAL\Connection\ConnectionInterface');

        $this->queryBuilder = new QueryBuilder($connectionMock);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->queryBuilder);
    }

    public function testConnection()
    {
        $connectionMock = $this->getMock('Fridge\DBAL\Connection\ConnectionInterface');
        $this->queryBuilder = new QueryBuilder($connectionMock);

        $this->assertEquals($connectionMock, $this->queryBuilder->getConnection());
    }

    public function testExpressionBuilder()
    {
        $this->assertInstanceOf('Fridge\DBAL\Query\Expression\ExpressionBuilder', $this->queryBuilder->getExpressionBuilder());
    }

    public function testType()
    {
        $this->queryBuilder->select();
        $this->assertEquals(QueryBuilder::SELECT, $this->queryBuilder->getType());

        $this->queryBuilder->insert('foo');
        $this->assertEquals(QueryBuilder::INSERT, $this->queryBuilder->getType());

        $this->queryBuilder->update('foo');
        $this->assertEquals(QueryBuilder::UPDATE, $this->queryBuilder->getType());

        $this->queryBuilder->delete('foo');
        $this->assertEquals(QueryBuilder::DELETE, $this->queryBuilder->getType());
    }

    public function testParts()
    {
        $this->assertEquals(array(
            'select'  => array(),
            'from'    => array(),
            'join'    => array(),
            'set'     => array(),
            'where'   => null,
            'groupBy' => array(),
            'having'  => null,
            'orderBy' => array(),
            'offset'  => null,
            'limit'   => null,
        ), $this->queryBuilder->getParts());
    }

    public function testEmptySelect()
    {
        $this->queryBuilder
            ->select()
            ->from('foo', 'f');

        $this->assertEquals('SELECT * FROM foo f', $this->queryBuilder->getQuery());
    }

    public function testNotEmptySelect()
    {
        $this->queryBuilder
            ->select('b.foo')
            ->from('bar', 'b');

        $this->assertEquals('SELECT b.foo FROM bar b', $this->queryBuilder->getQuery());
    }

    public function testSelectWithJoin()
    {
        $this->queryBuilder
            ->select()
            ->from('foo', 'f')
            ->join('f', 'inner', 'bar', 'b', 'f.id = b.foo_id');

        $this->assertEquals('SELECT * FROM foo f INNER JOIN bar b ON f.id = b.foo_id', $this->queryBuilder->getQuery());
    }

    public function testSelectWithInnerJoin()
    {
        $this->queryBuilder
            ->select()
            ->from('foo', 'f')
            ->innerJoin('f', 'bar', 'b', 'f.id = b.foo_id');

        $this->assertEquals('SELECT * FROM foo f INNER JOIN bar b ON f.id = b.foo_id', $this->queryBuilder->getQuery());
    }

    public function testSelectWithLeftJoin()
    {
        $this->queryBuilder
            ->select()
            ->from('foo', 'f')
            ->leftJoin('f', 'bar', 'b', 'f.id = b.foo_id');

        $this->assertEquals('SELECT * FROM foo f LEFT JOIN bar b ON f.id = b.foo_id', $this->queryBuilder->getQuery());
    }

    public function testSelectWithRightJoin()
    {
        $this->queryBuilder
            ->select()
            ->from('foo', 'f')
            ->rightJoin('f', 'bar', 'b', 'f.id = b.foo_id');

        $this->assertEquals('SELECT * FROM foo f RIGHT JOIN bar b ON f.id = b.foo_id', $this->queryBuilder->getQuery());
    }

    public function testSelectWithSimpleWhere()
    {
        $this->queryBuilder
            ->select()
            ->from('foo', 'f')
            ->where('f.a = f.b');

        $this->assertEquals('SELECT * FROM foo f WHERE f.a = f.b', $this->queryBuilder->getQuery());
    }

    public function testSelectWithCompositeWhere()
    {
        $expression = $this->queryBuilder->getExpressionBuilder()->andX();
        $expression->addPart('f.a = f.b');

        $this->queryBuilder
            ->select()
            ->from('foo', 'f')
            ->where($expression);

        $this->assertEquals('SELECT * FROM foo f WHERE f.a = f.b', $this->queryBuilder->getQuery());
    }

    public function testSelectWithAndWhere()
    {
        $this->queryBuilder
            ->select()
            ->from('foo', 'f')
            ->where('f.a = f.b')
            ->andWhere('f.a > 10');

        $this->assertEquals('SELECT * FROM foo f WHERE (f.a = f.b) AND (f.a > 10)', $this->queryBuilder->getQuery());
    }

    public function testSelectWithOrWhere()
    {
        $this->queryBuilder
            ->select()
            ->from('foo', 'f')
            ->where('f.a = f.b')
            ->orWhere('f.a > 10');

        $this->assertEquals('SELECT * FROM foo f WHERE (f.a = f.b) OR (f.a > 10)', $this->queryBuilder->getQuery());
    }

    public function testSelectWithAndOrWhere()
    {
        $this->queryBuilder
            ->select()
            ->from('foo', 'f')
            ->where('f.a = f.b')
            ->andWhere('f.a > 10')
            ->orWhere('f.b < 15');

        $this->assertEquals(
            'SELECT * FROM foo f WHERE ((f.a = f.b) AND (f.a > 10)) OR (f.b < 15)',
            $this->queryBuilder->getQuery()
        );
    }

    public function testSelectWithGroupBy()
    {
        $this->queryBuilder
            ->select()
            ->from('foo', 'f')
            ->groupBy('f.foo');

        $this->assertEquals('SELECT * FROM foo f GROUP BY f.foo', $this->queryBuilder->getQuery());
    }

    public function testSelectWithSimpleHaving()
    {
        $expression = $this->queryBuilder->getExpressionBuilder()->andX();
        $expression->addPart('f.foo > 10');

        $this->queryBuilder
            ->select()
            ->from('foo', 'f')
            ->groupBy('f.foo')
            ->having($expression);

        $this->assertEquals('SELECT * FROM foo f GROUP BY f.foo HAVING f.foo > 10', $this->queryBuilder->getQuery());
    }

    public function testSelectWithAndHaving()
    {
        $this->queryBuilder
            ->select()
            ->from('foo', 'f')
            ->groupBy('f.foo')
            ->having('f.foo > 10')
            ->andHaving('f.foo < 15');

        $this->assertEquals(
            'SELECT * FROM foo f GROUP BY f.foo HAVING (f.foo > 10) AND (f.foo < 15)',
            $this->queryBuilder->getQuery()
        );
    }

    public function testSelectWithOrHaving()
    {
        $this->queryBuilder
            ->select()
            ->from('foo', 'f')
            ->groupBy('f.foo')
            ->having('f.foo > 10')
            ->orHaving('f.foo = 9');

        $this->assertEquals(
            'SELECT * FROM foo f GROUP BY f.foo HAVING (f.foo > 10) OR (f.foo = 9)',
            $this->queryBuilder->getQuery()
        );
    }

    public function testSelectWithOrderBy()
    {
        $this->queryBuilder
            ->select()
            ->from('foo', 'f')
            ->orderBy('f.foo ASC');

        $this->assertEquals('SELECT * FROM foo f ORDER BY f.foo ASC', $this->queryBuilder->getQuery());
    }

    public function testSelectWithOffset()
    {
        $this->queryBuilder
            ->select()
            ->from('foo')
            ->offset(10);

        $this->assertEquals('SELECT * FROM foo OFFSET 10', $this->queryBuilder->getQuery());
    }

    public function testSelectWithLimit()
    {
        $this->queryBuilder
            ->select()
            ->from('foo')
            ->limit(10);

        $this->assertEquals('SELECT * FROM foo LIMIT 10', $this->queryBuilder->getQuery());
    }

    public function testSetParametersWithoutTypes()
    {
        $this->queryBuilder->setParameters(array('foo'));

        $this->assertEquals(array('foo'), $this->queryBuilder->getParameters());
        $this->assertEmpty($this->queryBuilder->getParameterTypes());
    }

    public function testSetParametersWithTypes()
    {
        $this->queryBuilder->setParameters(array('foo'), array(Type::STRING));

        $this->assertEquals(array('foo'), $this->queryBuilder->getParameters());
        $this->assertEquals(array(Type::STRING), $this->queryBuilder->getParameterTypes());
    }

    public function testSetParameterWithoutType()
    {
        $this->queryBuilder->setParameter(0, 'foo');

        $this->assertEquals('foo', $this->queryBuilder->getParameter(0));
        $this->assertNull($this->queryBuilder->getParameterType(0));
    }

    public function testSetParameterWithType()
    {
        $this->queryBuilder->setParameter(0, 'foo', Type::STRING);

        $this->assertEquals('foo', $this->queryBuilder->getParameter(0));
        $this->assertEquals(Type::STRING, $this->queryBuilder->getParameterType(0));

        $this->queryBuilder->setParameter(0, 'bar', Type::STRING);

        $this->assertEquals('bar', $this->queryBuilder->getParameter(0));
        $this->assertEquals(Type::STRING, $this->queryBuilder->getParameterType(0));
    }

    public function testCreatePositionalParameter()
    {
        $placeholder1 = $this->queryBuilder->createPositionalParameter('foo', Type::STRING);

        $this->assertEquals('?', $placeholder1);
        $this->assertEquals('foo', $this->queryBuilder->getParameter(0));
        $this->assertEquals(Type::STRING, $this->queryBuilder->getParameterType(0));

        $placeholder2 = $this->queryBuilder->createPositionalParameter('bar', Type::STRING);

        $this->assertEquals('?', $placeholder2);
        $this->assertEquals('bar', $this->queryBuilder->getParameter(1));
        $this->assertEquals(Type::STRING, $this->queryBuilder->getParameterType(1));
    }

    public function testCreateNamedParameterWithoutPlaceholder()
    {
        $placeholder1 = $this->queryBuilder->createNamedParameter('foo', Type::STRING);

        $this->assertEquals(':fridge0', $placeholder1);
        $this->assertEquals('foo', $this->queryBuilder->getParameter('fridge0'));
        $this->assertEquals(Type::STRING, $this->queryBuilder->getParameterType('fridge0'));

        $placeholder2 = $this->queryBuilder->createNamedParameter('bar', Type::STRING);

        $this->assertEquals(':fridge1', $placeholder2);
        $this->assertEquals('bar', $this->queryBuilder->getParameter('fridge1'));
        $this->assertEquals(Type::STRING, $this->queryBuilder->getParameterType('fridge1'));
    }

    public function testCreateNamedParameterWithlaceholder()
    {
        $placeholder1 = $this->queryBuilder->createNamedParameter('foo', Type::STRING, ':bar');

        $this->assertEquals(':bar0', $placeholder1);
        $this->assertEquals('foo', $this->queryBuilder->getParameter('bar0'));
        $this->assertEquals(Type::STRING, $this->queryBuilder->getParameterType('bar0'));

        $placeholder2 = $this->queryBuilder->createNamedParameter('bar', Type::STRING, ':bar');

        $this->assertEquals(':bar1', $placeholder2);
        $this->assertEquals('bar', $this->queryBuilder->getParameter('bar1'));
        $this->assertEquals(Type::STRING, $this->queryBuilder->getParameterType('bar1'));
    }

    public function testCreateParameterWithoutParameter()
    {
        $placeholder = $this->queryBuilder->createParameter('bar', Type::STRING);

        $this->assertEquals('?', $placeholder);
        $this->assertEquals('bar', $this->queryBuilder->getParameter(0));
        $this->assertEquals(Type::STRING, $this->queryBuilder->getParameterType(0));
    }

    public function testCreateParameterWithPositionalParameter()
    {
        $this->queryBuilder
            ->select()
            ->from('foo', 'f')
            ->where('f.a = ?')
            ->setParameter(0, 'foo', Type::STRING);

        $placeholder = $this->queryBuilder->createParameter('bar', Type::STRING);

        $this->assertEquals('?', $placeholder);
        $this->assertEquals('bar', $this->queryBuilder->getParameter(1));
        $this->assertEquals(Type::STRING, $this->queryBuilder->getParameterType(1));
    }

    public function testCreateParameterWithNamedParameter()
    {
        $this->queryBuilder
            ->select()
            ->from('foo', 'f')
            ->where('f.a = :a')
            ->setParameter('a', 'foo', Type::STRING);

        $placeholder = $this->queryBuilder->createParameter('bar', Type::STRING);

        $this->assertEquals(':fridge0', $placeholder);
        $this->assertEquals('bar', $this->queryBuilder->getParameter('fridge0'));
        $this->assertEquals(Type::STRING, $this->queryBuilder->getParameterType('fridge0'));
    }

    public function testInsert()
    {
        $this->queryBuilder
            ->insert('foo')
            ->set('foo', ':foo');

        $this->assertEquals('INSERT INTO foo (foo) VALUES (:foo)', $this->queryBuilder->getQuery());
    }

    public function testUpdateWithoutAlias()
    {
        $this->queryBuilder
            ->update('foo')
            ->set('foo', ':foo');

        $this->assertEquals('UPDATE foo SET foo = :foo', $this->queryBuilder->getQuery());
    }

    public function testUpdateWithAlias()
    {
        $this->queryBuilder
            ->update('foo', 'f')
            ->from('bar', 'b')
            ->set('f.foo', ':foo')
            ->where('f.a = b.b');

        $this->assertEquals('UPDATE f FROM foo f, bar b SET f.foo = :foo WHERE f.a = b.b', $this->queryBuilder->getQuery());
    }

    public function testDeleteWithAlias()
    {
        $this->queryBuilder
            ->delete('foo', 'f')
            ->from('bar', 'b')
            ->where('f.a = b.b');

        $this->assertEquals('DELETE f FROM foo f, bar b WHERE f.a = b.b', $this->queryBuilder->getQuery());
    }

    public function testDeleteWithoutAlias()
    {
        $this->queryBuilder
            ->delete('foo')
            ->where('a = b');

        $this->assertEquals('DELETE FROM foo WHERE a = b', $this->queryBuilder->getQuery());
    }

    public function testPart()
    {
        $this->queryBuilder
            ->select(array('foo'))
            ->from('foo', 'f')
            ->join('foo', 'left', 'bar', 'foo', 'bar')
            ->where('a = b', CompositeExpression::TYPE_OR)
            ->groupBy('foo')
            ->having('a = b', CompositeExpression::TYPE_OR)
            ->orderBy('foo ASC')
            ->offset(10)
            ->limit(10);

        $this->assertEquals(array('foo'), $this->queryBuilder->getPart('select'));
        $this->assertEquals(array(array('table' => 'foo', 'alias' => 'f')), $this->queryBuilder->getPart('from'));
        $this->assertEquals(
            array('foo' => array(array('type' => 'left', 'table' => 'bar', 'alias' => 'foo', 'expression' => 'bar'))),
            $this->queryBuilder->getPart('join')
        );
        $this->assertEquals(CompositeExpression::TYPE_OR, $this->queryBuilder->getPart('where')->getType());
        $this->assertEquals(array('a = b'), $this->queryBuilder->getPart('where')->getParts());
        $this->assertEquals(array('foo'), $this->queryBuilder->getPart('groupBy'));
        $this->assertEquals(CompositeExpression::TYPE_OR, $this->queryBuilder->getPart('having')->getType());
        $this->assertEquals(array('a = b'), $this->queryBuilder->getPart('having')->getParts());
        $this->assertEquals(array('foo ASC'), $this->queryBuilder->getPart('orderBy'));
        $this->assertEquals(10, $this->queryBuilder->getPart('offset'));
        $this->assertEquals(10, $this->queryBuilder->getPart('limit'));

        $this->queryBuilder
            ->insert('foo')
            ->set('foo', '?');

        $this->assertEquals(array('foo' => '?'), $this->queryBuilder->getPart('set'));
    }

    public function testResetAllParts()
    {
        $this->queryBuilder
            ->select('foo')
            ->from('foo')
            ->where('a = b')
            ->groupBy('foo')
            ->having('a > c')
            ->orderBy('foo ASC')
            ->offset(5)
            ->limit(10);

        $this->queryBuilder->resetParts();

        $this->assertEquals(array(
            'select'  => array(),
            'from'    => array(),
            'join'    => array(),
            'set'     => array(),
            'where'   => null,
            'groupBy' => array(),
            'having'  => null,
            'orderBy' => array(),
            'offset'  => null,
            'limit'   => null,
        ), $this->queryBuilder->getParts());
    }

    public function testResetParts()
    {
        $this->queryBuilder->select('foo');
        $this->queryBuilder->resetParts(array('select'));

        $this->assertEmpty($this->queryBuilder->getPart('select'));
    }

    public function testExecuteSelectQuery()
    {
        $connectionMock = $this->getMock('Fridge\DBAL\Connection\ConnectionInterface');
        $connectionMock
            ->expects($this->once())
            ->method('executeQuery')
            ->with(
                $this->equalTo('SELECT * FROM foo f WHERE f.a = ?'),
                $this->equalTo(array('bar')),
                $this->equalTo(array(Type::STRING))
            )
            ->will($this->returnValue('foo'));

        $this->queryBuilder = new QueryBuilder($connectionMock);
        $this->queryBuilder
            ->select()
            ->from('foo', 'f')
            ->where('f.a = ?')
            ->setParameter(0, 'bar', Type::STRING);

        $this->assertEquals('foo', $this->queryBuilder->execute());
    }

    public function testExecuteInsertQuery()
    {
        $connectionMock = $this->getMock('Fridge\DBAL\Connection\ConnectionInterface');
        $connectionMock
            ->expects($this->once())
            ->method('executeUpdate')
            ->with(
                $this->equalTo('INSERT INTO foo (bar) VALUES (?)'),
                $this->equalTo(array('foo')),
                $this->equalTo(array(Type::STRING))
            )
            ->will($this->returnValue('foo'));

        $this->queryBuilder = new QueryBuilder($connectionMock);
        $this->queryBuilder
            ->insert('foo')
            ->set('bar', '?')
            ->setParameter(0, 'foo', Type::STRING);

        $this->assertEquals('foo', $this->queryBuilder->execute());
    }
}
