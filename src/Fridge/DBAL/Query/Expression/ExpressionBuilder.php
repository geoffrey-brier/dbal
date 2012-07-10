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
    /** @const The equal comparaison constant */
    const EQ  = '=';

    /** @const The not equal comparaison constant */
    const NEQ = '<>';

    /** @const The greater than comparaison constant */
    const GT  = '>';

    /** @const The greater than or equal comparaison constant */
    const GTE = '>=';

    /** @const The lower than comparaison constant */
    const LT  = '<';

    /** @const The lower than or equal comparaison constant */
    const LTE = '<=';

    /**
     * Creates an "AND" composite expression.
     *
     * @param string|array $expression The expression.
     *
     * @return \Fridge\DBAL\Query\Expression\CompositeExpression The "AND" composite expression.
     */
    public function andX($expression = array())
    {
        return new CompositeExpression(CompositeExpression::TYPE_AND, (array) $expression);
    }

    /**
     * Creates an "OR" composite expression.
     *
     * @param string|array $expression The expression.
     *
     * @return \Fridge\DBAL\Query\Expression\CompositeExpression The "OR" composite expression.
     */
    public function orX($expression = array())
    {
        return new CompositeExpression(CompositeExpression::TYPE_OR, (array) $expression);
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
