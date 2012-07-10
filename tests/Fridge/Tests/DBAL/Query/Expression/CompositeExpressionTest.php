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

use Fridge\DBAL\Query\Expression\CompositeExpression;

/**
 * Composite expression test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CompositeExpressionTest extends \PHPUnit_Framework_TestCase
{
    public function testType()
    {
        $compositeExpression = new CompositeExpression(CompositeExpression::TYPE_AND);
        $this->assertEquals(CompositeExpression::TYPE_AND, $compositeExpression->getType());

        $compositeExpression->setType(CompositeExpression::TYPE_OR);
        $this->assertEquals(CompositeExpression::TYPE_OR, $compositeExpression->getType());
    }

    public function testEmptyCompositeExpression()
    {
        $compositeExpression = new CompositeExpression(CompositeExpression::TYPE_AND);

        $this->assertEmpty((string) $compositeExpression);
    }

    public function testSingleCompositeExpression()
    {
        $compositeExpression = new CompositeExpression(CompositeExpression::TYPE_AND, array('a = b'));
        $this->assertEquals('a = b', (string) $compositeExpression);

        $compositeExpression = new CompositeExpression(CompositeExpression::TYPE_OR, array('a = b'));
        $this->assertEquals('a = b', (string) $compositeExpression);

        $this->assertEquals(array('a = b'), $compositeExpression->getParts());
    }

    public function testAndCompositeExpression()
    {
        $compositeExpression = new CompositeExpression(CompositeExpression::TYPE_AND, array('a = b', 'c = d'));

        $this->assertEquals('(a = b) AND (c = d)', (string) $compositeExpression);
    }

    public function testOrCompositeExpression()
    {
        $compositeExpression = new CompositeExpression(CompositeExpression::TYPE_OR, array('a = b', 'c = d'));

        $this->assertEquals('(a = b) OR (c = d)', (string) $compositeExpression);
    }

    public function testAndOrCompositeExpression()
    {
        $orCompositeExpression = new CompositeExpression(CompositeExpression::TYPE_OR, array('a = b', 'c = d'));
        $andCompositeExpression = new CompositeExpression(CompositeExpression::TYPE_AND, array('e = f', $orCompositeExpression));

        $this->assertEquals('(e = f) AND ((a = b) OR (c = d))', (string) $andCompositeExpression);
    }

    public function testOrAndCompositeExpression()
    {
        $andCompositeExpression = new CompositeExpression(CompositeExpression::TYPE_AND, array('a = b', 'c = d'));
        $orCompositeExpression = new CompositeExpression(CompositeExpression::TYPE_OR, array('e = f', $andCompositeExpression));

        $this->assertEquals('(e = f) OR ((a = b) AND (c = d))', (string) $orCompositeExpression);
    }
}
