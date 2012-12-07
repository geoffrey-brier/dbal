<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\Query\Rewriter;

use \DateTime;

use Fridge\DBAL\Connection\Connection,
    Fridge\DBAL\Query\Rewriter\NamedQueryRewriter,
    Fridge\DBAL\Type\Type;

/**
 * Named query rewriter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class NamedQueryRewriterTest extends \PHPUnit_Framework_TestCase
{
    public function testRewriteWithoutTypes()
    {
        $query = 'SELECT * FROM foo WHERE foo = ?';
        $parameters = array(1);
        $types = array();

        list($query, $parameters, $types) = NamedQueryRewriter::rewrite($query, $parameters, $types);

        $this->assertSame('SELECT * FROM foo WHERE foo = ?', $query);
        $this->assertSame(array(1), $parameters);
        $this->assertSame(array(), $types);
    }

    public function testRewriteWithNamedTypes()
    {
        $query = 'SELECT * FROM foo WHERE foo IN (:foo)';
        $parameters = array('foo' => array(1, 2));
        $types = array('foo' => Type::INTEGER.Connection::PARAM_ARRAY);

        list($query, $parameters, $types) = NamedQueryRewriter::rewrite($query, $parameters, $types);

        $this->assertSame('SELECT * FROM foo WHERE foo IN (:foo1, :foo2)', $query);
        $this->assertSame(array('foo1' => 1, 'foo2' => 2), $parameters);
        $this->assertSame(array('foo1' => Type::INTEGER, 'foo2' => Type::INTEGER), $types);
    }

    public function testRewriteWithMultipleNamedTypes()
    {
        $query = 'SELECT * FROM foo WHERE foo IN (:foo) AND bar IN (:foo)';
        $parameters = array('foo' => array(1, 2));
        $types = array('foo' => Type::INTEGER.Connection::PARAM_ARRAY);

        list($query, $parameters, $types) = NamedQueryRewriter::rewrite($query, $parameters, $types);

        $this->assertSame('SELECT * FROM foo WHERE foo IN (:foo1, :foo2) AND bar IN (:foo1, :foo2)', $query);
        $this->assertSame(array('foo1' => 1, 'foo2' => 2), $parameters);
        $this->assertSame(array('foo1' => Type::INTEGER, 'foo2' => Type::INTEGER), $types);
    }

    public function testRewriteWithNamedTypesAndSimpleQuoteLiteralDelimiter()
    {
        $query = 'SELECT * FROM foo WHERE foo = \':foo\' OR foo IN (:foo)';
        $parameters = array('foo' => array(1, 2));
        $types = array('foo' => Type::INTEGER.Connection::PARAM_ARRAY);

        list($query, $parameters, $types) = NamedQueryRewriter::rewrite($query, $parameters, $types);

        $this->assertSame('SELECT * FROM foo WHERE foo = \':foo\' OR foo IN (:foo1, :foo2)', $query);
        $this->assertSame(array('foo1' => 1, 'foo2' => 2), $parameters);
        $this->assertSame(array('foo1' => Type::INTEGER, 'foo2' => Type::INTEGER), $types);
    }

    public function testRewriteWithNamedTypesAndDoubleQuoteLiteralDelimiter()
    {
        $query = 'SELECT * FROM foo WHERE foo = ":foo" OR foo IN (:foo)';
        $parameters = array('foo' => array(1, 2));
        $types = array('foo' => Type::INTEGER.Connection::PARAM_ARRAY);

        list($query, $parameters, $types) = NamedQueryRewriter::rewrite($query, $parameters, $types);

        $this->assertSame('SELECT * FROM foo WHERE foo = ":foo" OR foo IN (:foo1, :foo2)', $query);
        $this->assertSame(array('foo1' => 1, 'foo2' => 2), $parameters);
        $this->assertSame(array('foo1' => Type::INTEGER, 'foo2' => Type::INTEGER), $types);
    }

    public function testRewriteWithPartialNamedTypes()
    {
        $date = new DateTime();

        $query = 'SELECT * FROM foo WHERE foo IN (:foo) AND bar = :bar AND baz < :baz';
        $parameters = array('foo' => array(1, 2), 'bar' => 'bar', 'baz' => $date);
        $types = array('foo' => Type::INTEGER.Connection::PARAM_ARRAY, 'baz' => Type::DATETIME);

        list($query, $parameters, $types) = NamedQueryRewriter::rewrite($query, $parameters, $types);

        $this->assertSame('SELECT * FROM foo WHERE foo IN (:foo1, :foo2) AND bar = :bar AND baz < :baz', $query);

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

        list($query, $parameters, $types) = NamedQueryRewriter::rewrite($query, $parameters, $types);

        $this->assertSame(
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

    /**
     * @expectedException \Fridge\DBAL\Exception\Query\Rewriter\QueryRewriterException
     * @expectedExceptionMessage The named placeholder ":foo" does not exist in the query: "SELECT * FROM foo".
     */
    public function testRewriteWithInvalidPlaceholder()
    {
        $query = 'SELECT * FROM foo';
        $parameters = array('foo' => array(1, 2));
        $types = array('foo' => Type::INTEGER.Connection::PARAM_ARRAY);

        NamedQueryRewriter::rewrite($query, $parameters, $types);
    }
}
