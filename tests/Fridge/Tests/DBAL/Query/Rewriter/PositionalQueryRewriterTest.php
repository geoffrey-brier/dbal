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
    Fridge\DBAL\Query\Rewriter\PositionalQueryRewriter,
    Fridge\DBAL\Type\Type;

/**
 * Positional query rewriter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PositionalQueryRewriterTest extends \PHPUnit_Framework_TestCase
{
    public function testRewriteWithoutTypes()
    {
        $query = 'SELECT * FROM foo WHERE foo = ?';
        $parameters = array(1);
        $types = array();

        list($query, $parameters, $types) = PositionalQueryRewriter::rewrite($query, $parameters, $types);

        $this->assertSame('SELECT * FROM foo WHERE foo = ?', $query);
        $this->assertSame(array(1), $parameters);
        $this->assertSame(array(), $types);
    }

    public function testRewriteWithPositionalTypes()
    {
        $query = 'SELECT * FROM foo WHERE foo IN (?)';
        $parameters = array(array(1, 2));
        $types = array(Type::INTEGER.Connection::PARAM_ARRAY);

        list($query, $parameters, $types) = PositionalQueryRewriter::rewrite($query, $parameters, $types);

        $this->assertSame('SELECT * FROM foo WHERE foo IN (?, ?)', $query);
        $this->assertSame(array(1, 2), $parameters);
        $this->assertSame(array(Type::INTEGER, Type::INTEGER), $types);
    }

    public function testRewriteWithPositionalTypesAndSimpleQuoteLiteralDelimiter()
    {
        $query = 'SELECT * FROM foo WHERE foo = \'?\' OR foo IN (?)';
        $parameters = array(array(1, 2));
        $types = array(Type::INTEGER.Connection::PARAM_ARRAY);

        list($query, $parameters, $types) = PositionalQueryRewriter::rewrite($query, $parameters, $types);

        $this->assertSame('SELECT * FROM foo WHERE foo = \'?\' OR foo IN (?, ?)', $query);
        $this->assertSame(array(1, 2), $parameters);
        $this->assertSame(array(Type::INTEGER, Type::INTEGER), $types);
    }

    public function testRewriteWithPositionalTypesAndDoubleQuoteLiteralDelimiter()
    {
        $query = 'SELECT * FROM foo WHERE foo = "?" OR foo IN (?)';
        $parameters = array(array(1, 2));
        $types = array(Type::INTEGER.Connection::PARAM_ARRAY);

        list($query, $parameters, $types) = PositionalQueryRewriter::rewrite($query, $parameters, $types);

        $this->assertSame('SELECT * FROM foo WHERE foo = "?" OR foo IN (?, ?)', $query);
        $this->assertSame(array(1, 2), $parameters);
        $this->assertSame(array(Type::INTEGER, Type::INTEGER), $types);
    }

    public function testRewriteWithPartialPositionalTypes()
    {
        $date = new DateTime();

        $query = 'SELECT * FROM foo WHERE foo IN (?) AND bar = ? AND baz < ?';
        $parameters = array(array(1, 2), 'bar', $date);
        $types = array(0 => Type::INTEGER.Connection::PARAM_ARRAY, 2 => Type::DATETIME);

        list($query, $parameters, $types) = PositionalQueryRewriter::rewrite($query, $parameters, $types);

        $this->assertSame('SELECT * FROM foo WHERE foo IN (?, ?) AND bar = ? AND baz < ?', $query);
        $this->assertSame(array(1, 2, 'bar', $date), $parameters);
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

        list($query, $parameters, $types) = PositionalQueryRewriter::rewrite($query, $parameters, $types);

        $this->assertSame('SELECT * FROM foo WHERE foo IN (?, ?) AND bar = ? AND baz IN (?, ?) AND bat < ?', $query);
        $this->assertSame(array(1, 2, 'bar', 'published', 'draft', $date), $parameters);
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

    /**
     * @expectedException \Fridge\DBAL\Exception\Query\Rewriter\QueryRewriterException
     * @expectedExceptionMessage The positional placeholder (0) does not exist in the query: "SELECT * FROM foo".
     */
    public function testRewriteWithInvalidPlaceholder()
    {
        $query = 'SELECT * FROM foo';
        $parameters = array(array(1, 2));
        $types = array(Type::INTEGER.Connection::PARAM_ARRAY);

        PositionalQueryRewriter::rewrite($query, $parameters, $types);
    }
}
