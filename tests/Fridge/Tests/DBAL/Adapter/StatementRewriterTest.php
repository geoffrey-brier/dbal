<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\Adapter;

use Fridge\DBAL\Adapter\StatementRewriter;

/**
 * Statement rewriter adapter tests.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class StatementRewriterTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\Adapter\Mysqli\StatementRewriter */
    protected $statementRewriter;

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->statementRewriter);
    }

    public function testDelimiters()
    {
        $this->statementRewriter = new StatementRewriter('SELECT * FROM foo');

        $this->assertSame(array('\'', '"'), $this->statementRewriter->getDelimiters());

        $this->statementRewriter->setDelimiters(array('\'\''));
        $this->assertSame(array('\'\''), $this->statementRewriter->getDelimiters());
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\Adapter\StatementRewriterException
     * @expectedExceptionMessage The parameter "foo" does not exist.
     */
    public function testRewriteWithoutParameter()
    {
        $this->statementRewriter = new StatementRewriter('SELECT * FROM foo');

        $this->assertSame('SELECT * FROM foo', $this->statementRewriter->getRewritedStatement());

        $this->statementRewriter->getRewritedParameters('foo');
    }

    public function testRewriteWithOneParameter()
    {
        $this->statementRewriter = new StatementRewriter('SELECT * FROM foo WHERE foo = :foo');

        $this->assertSame('SELECT * FROM foo WHERE foo = ?', $this->statementRewriter->getRewritedStatement());

        $this->assertSame(array(1), $this->statementRewriter->getRewritedParameters(':foo'));
    }

    public function testRewriteWithMultipleParameters()
    {
        $this->statementRewriter = new StatementRewriter('SELECT * FROM foo WHERE foo = :foo AND bar = :bar');

        $this->assertSame(
            'SELECT * FROM foo WHERE foo = ? AND bar = ?',
            $this->statementRewriter->getRewritedStatement()
        );

        $this->assertSame(array(1), $this->statementRewriter->getRewritedParameters(':foo'));
        $this->assertSame(array(2), $this->statementRewriter->getRewritedParameters(':bar'));
    }

    public function testRewriteWithMultipleSameParameters()
    {
        $this->statementRewriter = new StatementRewriter(
            'SELECT * FROM foo WHERE foo = :foo AND bar = :bar AND baz = :foo'
        );

        $this->assertSame(
            'SELECT * FROM foo WHERE foo = ? AND bar = ? AND baz = ?',
            $this->statementRewriter->getRewritedStatement()
        );

        $this->assertSame(array(1, 3), $this->statementRewriter->getRewritedParameters(':foo'));
        $this->assertSame(array(2), $this->statementRewriter->getRewritedParameters(':bar'));
    }

    public function testRewriteWithPositionalStatement()
    {
        $this->statementRewriter = new StatementRewriter('SELECT * FROM foo WHERE foo = ?');

        $this->assertSame('SELECT * FROM foo WHERE foo = ?', $this->statementRewriter->getRewritedStatement());
        $this->assertSame(array(1), $this->statementRewriter->getRewritedParameters(1));
    }

    public function testRewriteWithSimpleQuoteLiteralDelimiter()
    {
        $this->statementRewriter = new StatementRewriter(
            'SELECT * FROM foo WHERE foo = :foo AND bar = \':bar\' AND baz = :baz'
        );

        $this->assertSame(
            'SELECT * FROM foo WHERE foo = ? AND bar = \':bar\' AND baz = ?',
            $this->statementRewriter->getRewritedStatement()
        );

        $this->assertSame(array(1), $this->statementRewriter->getRewritedParameters(':foo'));
        $this->assertSame(array(2), $this->statementRewriter->getRewritedParameters(':baz'));
    }

    public function testRewriteWithDoubleQuoteLiteralDelimiter()
    {
        $this->statementRewriter = new StatementRewriter(
            'SELECT * FROM foo WHERE foo = :foo AND bar = ":bar" AND baz = :baz'
        );

        $this->assertSame(
            'SELECT * FROM foo WHERE foo = ? AND bar = ":bar" AND baz = ?',
            $this->statementRewriter->getRewritedStatement()
        );

        $this->assertSame(array(1), $this->statementRewriter->getRewritedParameters(':foo'));
        $this->assertSame(array(2), $this->statementRewriter->getRewritedParameters(':baz'));
    }
}
