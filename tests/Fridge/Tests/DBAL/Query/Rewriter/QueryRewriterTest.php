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

use Fridge\DBAL\Connection\Connection,
    Fridge\DBAL\Query\Rewriter\QueryRewriter,
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

        $this->assertSame('SELECT * FROM foo WHERE foo = ?', $query);
        $this->assertSame(array(1), $parameters);
        $this->assertSame(array(), $types);
    }

    public function testRewriteWithPositionalTypes()
    {
        $query = 'SELECT * FROM foo WHERE foo IN (?)';
        $parameters = array(array(1, 2));
        $types = array(Type::INTEGER.Connection::PARAM_ARRAY);

        list($query, $parameters, $types) = QueryRewriter::rewrite($query, $parameters, $types);

        $this->assertSame('SELECT * FROM foo WHERE foo IN (?, ?)', $query);
        $this->assertSame(array(1, 2), $parameters);
        $this->assertSame(array(Type::INTEGER, Type::INTEGER), $types);
    }

    public function testRewriteWithNamedTypes()
    {
        $query = 'SELECT * FROM foo WHERE foo IN (:foo)';
        $parameters = array('foo' => array(1, 2));
        $types = array('foo' => Type::INTEGER.Connection::PARAM_ARRAY);

        list($query, $parameters, $types) = QueryRewriter::rewrite($query, $parameters, $types);

        $this->assertSame('SELECT * FROM foo WHERE foo IN (:foo1, :foo2)', $query);
        $this->assertSame(array('foo1' => 1, 'foo2' => 2), $parameters);
        $this->assertSame(array('foo1' => Type::INTEGER, 'foo2' => Type::INTEGER), $types);
    }
}
