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

use \ReflectionMethod;

use Fridge\DBAL\Query\Expression\ExpressionBuilder;

/**
 * Expression builder test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ExpressionBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\Query\Expression\ExpressionBuilder */
    protected $expressionBuilder;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->expressionBuilder = new ExpressionBuilder();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->expressionBuilder);
    }

    public function testAndWithString()
    {
        $andExpression = $this->expressionBuilder->andX('a = b');
        $this->assertSame(array('a = b'), $andExpression->getParts());
    }

    public function testAndWithArray()
    {
        $andExpression = $this->expressionBuilder->andX(array('a = b', 'c = d'));
        $this->assertSame(array('a = b', 'c = d'), $andExpression->getParts());
    }

    public function testOrWithString()
    {
        $andExpression = $this->expressionBuilder->orX('a = b');
        $this->assertSame(array('a = b'), $andExpression->getParts());
    }

    public function testOrWithArray()
    {
        $orExpression = $this->expressionBuilder->orX(array('a = b', 'c = d'));
        $this->assertSame(array('a = b', 'c = d'), $orExpression->getParts());
    }

    public function testComparison()
    {
        $method = new ReflectionMethod('Fridge\DBAL\Query\Expression\ExpressionBuilder', 'comparison');
        $method->setAccessible(true);

        $comparison = $method->invoke($this->expressionBuilder, 'a', 'foo', 'b');
        $this->assertSame('a foo b', $comparison);
    }

    public function testEqual()
    {
        $this->assertSame('a = b', $this->expressionBuilder->equal('a', 'b'));
    }

    public function testNotEqual()
    {
        $this->assertSame('a <> b', $this->expressionBuilder->notEqual('a', 'b'));
    }

    public function testGreaterThan()
    {
        $this->assertSame('a > b', $this->expressionBuilder->greaterThan('a', 'b'));
    }

    public function testGreaterThanOrEqual()
    {
        $this->assertSame('a >= b', $this->expressionBuilder->greaterThanOrEqual('a', 'b'));
    }

    public function testLowerThan()
    {
        $this->assertSame('a < b', $this->expressionBuilder->lowerThan('a', 'b'));
    }

    public function testLowerThanOrEqual()
    {
        $this->assertSame('a <= b', $this->expressionBuilder->lowerThanOrEqual('a', 'b'));
    }

    public function testLike()
    {
        $this->assertSame('a LIKE b', $this->expressionBuilder->like('a', 'b'));
    }

    public function testisNull()
    {
        $this->assertSame('a IS NULL', $this->expressionBuilder->isNull('a'));
    }

    public function testisNotNull()
    {
        $this->assertSame('a IS NOT NULL', $this->expressionBuilder->isNotNull('a'));
    }
}
