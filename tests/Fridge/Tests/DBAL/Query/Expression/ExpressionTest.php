<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\Query\Expression;

use Fridge\DBAL\Query\Expression\Expression;

/**
 * Expression test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ExpressionTest extends \PHPUnit_Framework_TestCase
{
    public function testType()
    {
        $expression = new Expression(Expression::TYPE_AND);
        $this->assertSame(Expression::TYPE_AND, $expression->getType());

        $expression->setType(Expression::TYPE_OR);
        $this->assertSame(Expression::TYPE_OR, $expression->getType());
    }

    public function testEmptyExpression()
    {
        $expression = new Expression(Expression::TYPE_AND);

        $this->assertEmpty((string) $expression);
    }

    public function testSingleExpression()
    {
        $expression = new Expression(Expression::TYPE_AND, array('a = b'));
        $this->assertSame('a = b', (string) $expression);

        $expression = new Expression(Expression::TYPE_OR, array('a = b'));
        $this->assertSame('a = b', (string) $expression);

        $this->assertSame(array('a = b'), $expression->getParts());
    }

    public function testAndExpression()
    {
        $expression = new Expression(Expression::TYPE_AND, array('a = b', 'c = d'));

        $this->assertSame('(a = b) AND (c = d)', (string) $expression);
    }

    public function testOrExpression()
    {
        $expression = new Expression(Expression::TYPE_OR, array('a = b', 'c = d'));

        $this->assertSame('(a = b) OR (c = d)', (string) $expression);
    }

    public function testAndOrExpression()
    {
        $orExpression = new Expression(Expression::TYPE_OR, array('a = b', 'c = d'));
        $andExpression = new Expression(Expression::TYPE_AND, array('e = f', $orExpression));

        $this->assertSame('(e = f) AND ((a = b) OR (c = d))', (string) $andExpression);
    }

    public function testOrAndExpression()
    {
        $andExpression = new Expression(Expression::TYPE_AND, array('a = b', 'c = d'));
        $orExpression = new Expression(Expression::TYPE_OR, array('e = f', $andExpression));

        $this->assertSame('(e = f) OR ((a = b) AND (c = d))', (string) $orExpression);
    }
}
