<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Query\Expression;

/**
 * An expression builder allows to easily build "WHERE" or "HAVING" expressions.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ExpressionBuilder
{
    /** @const The equal comparison constant */
    const EQ  = '=';

    /** @const The not equal comparison constant */
    const NEQ = '<>';

    /** @const The greater than comparison constant */
    const GT  = '>';

    /** @const The greater than or equal comparison constant */
    const GTE = '>=';

    /** @const The lower than comparison constant */
    const LT  = '<';

    /** @const The lower than or equal comparison constant */
    const LTE = '<=';

    /**
     * Creates an "AND" expression.
     *
     * @param string|array $expression The expression.
     *
     * @return \Fridge\DBAL\Query\Expression\Expression The "AND" expression.
     */
    public function andX($expression = array())
    {
        return new Expression(Expression::TYPE_AND, (array) $expression);
    }

    /**
     * Creates an "OR" expression.
     *
     * @param string|array $expression The expression.
     *
     * @return \Fridge\DBAL\Query\Expression\Expression The "OR" expression.
     */
    public function orX($expression = array())
    {
        return new Expression(Expression::TYPE_OR, (array) $expression);
    }

    /**
     * Creates an "EQUAL" comparison.
     *
     * @param string $x The first parameter of the comparison.
     * @param string $y The second parameter of the comparison.
     *
     * @return string The "EQUAL" comparison.
     */
    public function equal($x, $y)
    {
        return $this->comparison($x, self::EQ, $y);
    }

    /**
     * Creates a "NOT EQUAL" comparison.
     *
     * @param string $x The first parameter of the comparison.
     * @param string $y The second parameter of the comparison.
     *
     * @return string The "NOT EQUAL" comparison.
     */
    public function notEqual($x, $y)
    {
        return $this->comparison($x, self::NEQ, $y);
    }

    /**
     * Creates a "GREATER THAN" comparison.
     *
     * @param string $x The first parameter of the comparison.
     * @param string $y The second parameter of the comparison.
     *
     * @return string The "GREATER THAN" comparison.
     */
    public function greaterThan($x, $y)
    {
        return $this->comparison($x, self::GT, $y);
    }

    /**
     * Creates a "GREATER THAN OR EQUAL" comparison.
     *
     * @param string $x The first parameter of the comparison.
     * @param string $y The second parameter of the comparison.
     *
     * @return string The "GREATER THAN OR EQUAL" comparison.
     */
    public function greaterThanOrEqual($x, $y)
    {
        return $this->comparison($x, self::GTE, $y);
    }

    /**
     * Creates a "LOWER THAN" comparison.
     *
     * @param string $x The first parameter of the comparison.
     * @param string $y The second parameter of the comparison.
     *
     * @return string The "LOWER THAN" comparison.
     */
    public function lowerThan($x, $y)
    {
        return $this->comparison($x, self::LT, $y);
    }

    /**
     * Creates a "LOWER THAN OR EQUAL" comparison.
     *
     * @param string $x The first parameter of the comparison.
     * @param string $y The second parameter of the comparison.
     *
     * @return string The "LOWER THAN OR EQUAL" comparison.
     */
    public function lowerThanOrEqual($x, $y)
    {
        return $this->comparison($x, self::LTE, $y);
    }

    /**
     * Creates a "LIKE" comparison.
     *
     * @param string $x The first parameter of the comparison.
     * @param string $y The second parameter of the comparison.
     *
     * @return string The "LIKE" comparison.
     */
    public function like($x, $y)
    {
        return $this->comparison($x, 'LIKE', $y);
    }

    /**
     * Creates a "NOT LIKE" comparison.
     *
     * @param string $x The first parameter of the comparison.
     * @param string $y The second parameter of the comparison.
     *
     * @return string The "NOT LIKE" comparison.
     */
    public function notLike($x, $y)
    {
        return $this->comparison($x, 'NOT LIKE', $y);
    }

    /**
     * Creates an "IS NULL" comparison.
     *
     * @param string $expression The expression.
     *
     * @return string The "IS NULL" comparison.
     */
    public function isNull($expression)
    {
        return $expression.' IS NULL';
    }

    /**
     * Creates an "IS NOT NULL" comparison.
     *
     * @param string $expression The expression.
     *
     * @return string The "IS NOT NULL" expression.
     */
    public function isNotNull($expression)
    {
        return $expression.' IS NOT NULL';
    }

    /**
     * Creates a comparison.
     *
     * @param string $x        The first parameter of the comparison.
     * @param string $operator The comparison operator (=, <>, >, >=, <, <=)
     * @param string $y        The second parameter of the comparison.
     *
     * @return string The comparison.
     */
    protected function comparison($x, $operator, $y)
    {
        return $x.' '.$operator.' '.$y;
    }
}
