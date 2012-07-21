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

use \DateTime;

use Fridge\DBAL\Connection\Connection,
    Fridge\DBAL\Query\QueryRewriter,
    Fridge\DBAL\Type\Type;

/**
 * Query rewriter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class QueryRewriterTest extends \PHPUnit_Framework_TestCase
{
    public function testRewriteWithoutTypes()
    {
        $query = 'SELECT * FROM foo WHERE foo = ?';
        $parameters = array(1);
        $types = array();

        list($query, $parameters, $types) = QueryRewriter::rewrite($query, $parameters, $types);

        $this->assertEquals('SELECT * FROM foo WHERE foo = ?', $query);
        $this->assertEquals(array(1), $parameters);
        $this->assertEquals(array(), $types);
    }

    public function testRewriteWithPositionalTypes()
    {
        $query = 'SELECT * FROM foo WHERE foo IN (?)';
        $parameters = array(array(1, 2));
        $types = array(Type::INTEGER.Connection::PARAM_ARRAY);

        list($query, $parameters, $types) = QueryRewriter::rewrite($query, $parameters, $types);

        $this->assertEquals('SELECT * FROM foo WHERE foo IN (?, ?)', $query);
        $this->assertEquals(array(1, 2), $parameters);
        $this->assertEquals(array(Type::INTEGER, Type::INTEGER), $types);
    }

    public function testRewriteWithPartialPositionalTypes()
    {
        $date = new DateTime();

        $query = 'SELECT * FROM foo WHERE foo IN (?) AND bar = ? AND baz < ?';
        $parameters = array(array(1, 2), 'bar', $date);
        $types = array(0 => Type::INTEGER.Connection::PARAM_ARRAY, 2 => Type::DATETIME);

        list($query, $parameters, $types) = QueryRewriter::rewrite($query, $parameters, $types);

        $this->assertEquals('SELECT * FROM foo WHERE foo IN (?, ?) AND bar = ? AND baz < ?', $query);
        $this->assertEquals(array(1, 2, 'bar', $date), $parameters);
        $this->assertEquals(array(0 => Type::INTEGER, 1 => Type::INTEGER, 3 => Type::DATETIME), $types);
    }

    public function testRewriteWithMultiplePartialPositionalTypes()
    {
        $date = new DateTime();

        $query = 'SELECT * FROM foo WHERE foo IN (?) AND bar = ? AND baz IN (?) AND bat < ?';
        $parameters = array(array(1, 2), 'bar', array('published', 'draft'), $date);
        $types = array(
            0 => Type::INTEGER.Connection::PARAM_ARRAY,
            2 => Type::STRING.Connection::PARAM_ARRAY,
            3 => Type::DATETIME,
        );

        list($query, $parameters, $types) = QueryRewriter::rewrite($query, $parameters, $types);

        $this->assertEquals('SELECT * FROM foo WHERE foo IN (?, ?) AND bar = ? AND baz IN (?, ?) AND bat < ?', $query);
        $this->assertEquals(array(1, 2, 'bar', 'published', 'draft', $date), $parameters);
        $this->assertEquals(
            array(
                0 => Type::INTEGER,
                1 => Type::INTEGER,
                3 => Type::STRING,
                4 => Type::STRING,
                5 => Type::DATETIME,
            ),
            $types
        );
    }

    public function testRewriteWithNamedTypes()
    {
        $query = 'SELECT * FROM foo WHERE foo IN (:foo)';
        $parameters = array('foo' => array(1, 2));
        $types = array('foo' => Type::INTEGER.Connection::PARAM_ARRAY);

        list($query, $parameters, $types) = QueryRewriter::rewrite($query, $parameters, $types);

        $this->assertEquals('SELECT * FROM foo WHERE foo IN (:foo1, :foo2)', $query);
        $this->assertEquals(array('foo1' => 1, 'foo2' => 2), $parameters);
        $this->assertEquals(array('foo1' => Type::INTEGER, 'foo2' => Type::INTEGER), $types);
    }

    public function testRewriteWithPartialNamedTypes()
    {
        $date = new DateTime();

        $query = 'SELECT * FROM foo WHERE foo IN (:foo) AND bar = :bar AND baz < :baz';
        $parameters = array('foo' => array(1, 2), 'bar' => 'bar', 'baz' => $date);
        $types = array('foo' => Type::INTEGER.Connection::PARAM_ARRAY, 'baz' => Type::DATETIME);

        list($query, $parameters, $types) = QueryRewriter::rewrite($query, $parameters, $types);

        $this->assertEquals('SELECT * FROM foo WHERE foo IN (:foo1, :foo2) AND bar = :bar AND baz < :baz', $query);

        $this->assertEquals(
            array(
                'foo1' => 1,
                'foo2' => 2,
                'bar'  => 'bar',
                'baz'  => $date,
            ),
            $parameters
        );

        $this->assertEquals(
            array(
                'foo1' => Type::INTEGER,
                'foo2' => Type::INTEGER,
                'baz'  => Type::DATETIME,
            ),
            $types
        );
    }

    public function testRewriteWithMultiplePartialNamedTypes()
    {
        $date = new DateTime();

        $query = 'SELECT * FROM foo WHERE foo IN (:foo) AND bar = :bar AND baz IN (:baz) AND bat < :bat';

        $parameters = array(
            'foo' => array(1, 2),
            'bar' => 'bar',
            'baz' => array('published', 'draft'),
            'bat' => $date,
        );

        $types = array(
            'foo' => Type::INTEGER.Connection::PARAM_ARRAY,
            'baz' => Type::STRING.Connection::PARAM_ARRAY,
            'bat' => Type::DATETIME,
        );

        list($query, $parameters, $types) = QueryRewriter::rewrite($query, $parameters, $types);

        $this->assertEquals(
            'SELECT * FROM foo WHERE foo IN (:foo1, :foo2) AND bar = :bar AND baz IN (:baz1, :baz2) AND bat < :bat',
            $query
        );

        $this->assertEquals(
            array(
                'foo1' => 1,
                'foo2' => 2,
                'bar'  => 'bar',
                'baz1' => 'published',
                'baz2' => 'draft',
                'bat'  => $date
            ),
            $parameters
        );

        $this->assertEquals(
            array(
                'foo1' => Type::INTEGER,
                'foo2' => Type::INTEGER,
                'baz1' => Type::STRING,
                'baz2' => Type::STRING,
                'bat'  => Type::DATETIME,
            ),
            $types
        );
    }
}
