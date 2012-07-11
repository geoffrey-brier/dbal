<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Query;

use Fridge\DBAL\Connection\ConnectionInterface;

/**
 * A query builder allows to easily build a query.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class QueryBuilder
{
    /** @const The select query type */
    const SELECT = 0;

    /** @const The insert query type */
    const INSERT = 1;

    /** @const The update query type */
    const UPDATE = 2;

    /** @const The delete query type */
    const DELETE = 3;

    /** @var \Fridge\DBAL\Connection\ConnectionInterface */
    protected $connection;

    /** @var integer */
    protected $type;

    /** @var array */
    protected $parts;

    /** @var array */
    protected $parameters;

    /** @var array */
    protected $parameterTypes;

    /** @var array */
    protected $parameterCounters;

    /** @var \Fridge\DBAL\Query\Expression\ExpressionBuilder */
    protected $expressionBuilder;

    /**
     * Query builder constructor.
     *
     * @param \Fridge\DBAL\Connection\ConnectionInterface $connection The query builder connection.
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
        $this->expressionBuilder = new Expression\ExpressionBuilder();

        $this->type = self::SELECT;
        $this->parts = array(
            'select'  => array(),
            'from'    => array(),
            'join'    => array(),
            'set'     => array(),
            'where'   => null,
            'groupBy' => array(),
            'having'  => null,
            'orderBy' => array(),
            'offset'  => null,
            'limit'   => null,
        );

        $this->parameters = array();
        $this->parameterTypes = array();
        $this->parameterCounters = array(
            'positional' => 0,
            'named'      => array(),
        );
    }

    /**
     * Gets the expression builder.
     *
     * @return \Fridge\DBAL\Query\Expression\ExpressionBuilder The expression builder.
     */
    public function getExpressionBuilder()
    {
        return $this->expressionBuilder;
    }

    /**
     * Gets the query builder connection.
     *
     * @return \Fridge\DBAL\Connection\ConnectionInterface The query builder connection.
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Gets the query builder type.
     *
     * @return integer The query builder type.
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Gets the query parts.
     *
     * @return array The query parts.
     */
    public function getParts()
    {
        return $this->parts;
    }

    /**
     * Gets a query part.
     *
     * @param string $partName The query part name to retrieve.
     *
     * @return mixed The query part.
     */
    public function getPart($partName)
    {
        return $this->parts[$partName];
    }

    /**
     * Resets query parts.
     *
     * @param array $partNames The query part names to reset.
     *
     * @return \Fridge\DBAL\Query\QueryBuilder The query builder.
     */
    public function resetParts(array $partNames = array())
    {
        if (empty($partNames)) {
            $partNames = array_keys($this->parts);
        }

        foreach ($partNames as $partName) {
            $this->resetPart($partName);
        }

        return $this;
    }

    /**
     * Resets a query part.
     *
     * @param string $partName The query part name to reset.
     *
     * @return \Fridge\DBAL\Query\QueryBuilder The query builder.
     */
    public function resetPart($partName)
    {
        $this->parts[$partName] = is_array($this->parts[$partName]) ? array() : null;

        return $this;
    }

    /**
     * Sets the select query mode.
     *
     * @param string|array $selects The fields to select.
     *
     * @return \Fridge\DBAL\Query\QueryBuilder The query builder.
     */
    public function select($selects = array())
    {
        $this->type = self::SELECT;

        if (!empty($selects)) {
            $this->parts['select'] = array_merge($this->parts['select'], (array) $selects);
        }

        return $this;
    }

    /**
     * Sets the insert query mode for a specific table.
     *
     * @param string $insert The table name.
     *
     * @return \Fridge\DBAL\Query\QueryBuilder The query builder.
     */
    public function insert($insert)
    {
        $this->type = self::INSERT;

        $this->from($insert);

        return $this;
    }

    /**
     * Sets the update query mode for a specific table.
     *
     * @param string $update The table name.
     * @param string $alias  The table alias.
     *
     * @return \Fridge\DBAL\Query\QueryBuilder The query builder.
     */
    public function update($update, $alias = null)
    {
        $this->type = self::UPDATE;

        $this->from($update, $alias);

        return $this;
    }

    /**
     * Sets the delete query mode for a specific table.
     *
     * @param string $delete The table name.
     * @param string $alias  The table alias.
     *
     * @return \Fridge\DBAL\Query\QueryBuilder The query builder.
     */
    public function delete($delete, $alias = null)
    {
        $this->type = self::DELETE;

        $this->from($delete, $alias);

        return $this;
    }

    /**
     * Adds a "FROM" clause to the query.
     *
     * @param string $from  The table name.
     * @param string $alias The table alias.
     *
     * @return \Fridge\DBAL\Query\QueryBuilder The query builder.
     */
    public function from($from, $alias = null)
    {
        $this->parts['from'][] = array(
            'table' => $from,
            'alias' => $alias,
        );

        return $this;
    }

    /**
     * Adds an "INNER JOIN" clause to the query.
     *
     * @param string $fromAlias The from table alias.
     * @param string $join      The join table name.
     * @param string $alias     The join table alias.
     * @param string $condition The join condition.
     *
     * @return \Fridge\DBAL\Query\QueryBuilder The query builder.
     */
    public function innerJoin($fromAlias, $join, $alias, $condition = null)
    {
        return $this->join($fromAlias, 'inner', $join, $alias, $condition);
    }

    /**
     * Adds a "LEFT JOIN" clause to the query.
     *
     * @param string $fromAlias The from table alias.
     * @param string $join      The join table name.
     * @param string $alias     The join table alias.
     * @param string $condition The join condition.
     *
     * @return \Fridge\DBAL\Query\QueryBuilder The query builder.
     */
    public function leftJoin($fromAlias, $join, $alias, $condition = null)
    {
        return $this->join($fromAlias, 'left', $join, $alias, $condition);
    }

    /**
     * Adds a "RIGHT JOIN" clause to the query.
     *
     * @param string $fromAlias The from table alias.
     * @param string $join      The join table name.
     * @param string $alias     The join table alias.
     * @param string $condition The join condition.
     *
     * @return \Fridge\DBAL\Query\QueryBuilder The query builder.
     */
    public function rightJoin($fromAlias, $join, $alias, $condition = null)
    {
        return $this->join($fromAlias, 'right', $join, $alias, $condition);
    }

    /**
     * Adds a "JOIN" clause to the query.
     *
     * @param string $fromAlias  The from table alias.
     * @param string $type       The join type (inner, left, right).
     * @param string $table      The join table name.
     * @param string $alias      The join table alias.
     * @param string $expression The join table expression.
     *
     * @return \Fridge\DBAL\Query\QueryBuilder The query builder.
     */
    public function join($fromAlias, $type, $table, $alias, $expression = null)
    {
        if (!isset($this->parts['join'][$fromAlias])) {
            $this->parts['join'][$fromAlias] = array();
        }

        $this->parts['join'][$fromAlias][] = array(
            'type'       => $type,
            'table'      => $table,
            'alias'      => $alias,
            'expression' => $expression,
        );

        return $this;
    }

    /**
     * Sets a new field value for an insert/update query.
     *
     * @param string $identifier The identifier.
     * @param mixed  $value      The value.
     *
     * @return \Fridge\DBAL\Query\QueryBuilder The query builder.
     */
    public function set($identifier, $value)
    {
        $this->parts['set'][$identifier] = $value;

        return $this;
    }

    /**
     * Adds a "WHERE" clause to the query.
     *
     * @param string|array|\Fridge\DBAL\Query\Expression\CompositeExpression $expression The expression.
     * @param string                                                         $type       The expression type (AND, OR).
     *
     * @return \Fridge\DBAL\Query\QueryBuilder The query builder.
     */
    public function where($expression, $type = Expression\CompositeExpression::TYPE_AND)
    {
        return $this->addCompositeExpression('where', $type, $expression);
    }

    /**
     * Adds an "AND (WHERE)" clause to the query.
     *
     * @param string|array|\Fridge\DBAL\Query\Expression\CompositeExpression $expression The expression.
     *
     * @return \Fridge\DBAL\Query\QueryBuilder The query builder.
     */
    public function andWhere($expression)
    {
        return $this->addCompositeExpression('where', Expression\CompositeExpression::TYPE_AND, $expression);
    }

    /**
     * Adds an "OR (WHERE)" clause to the query.
     *
     * @param string|array|\Fridge\DBAL\Query\Expression\CompositeExpression $expression The expression.
     *
     * @return \Fridge\DBAL\Query\QueryBuilder The query builder.
     */
    public function orWhere($expression)
    {
        return $this->addCompositeExpression('where', Expression\CompositeExpression::TYPE_OR, $expression);
    }

    /**
     * Adds a "GROUP BY" clause to the query.
     *
     * @param string|array $groupBy The group by clauses.
     *
     * @return \Fridge\DBAL\Query\QueryBuilder The query builder.
     */
    public function groupBy($groupBy)
    {
        $this->parts['groupBy'] = array_merge($this->parts['groupBy'], (array) $groupBy);

        return $this;
    }

    /**
     * Adds an "HAVING" clause to the query.
     *
     * @param string|array|\Fridge\DBAL\Query\Expression\CompositeExpression $expression The expression.
     * @param string                                                         $type       The expression type (AND, OR).
     *
     * @return \Fridge\DBAL\Query\QueryBuilder The query builder.
     */
    public function having($expression, $type = Expression\CompositeExpression::TYPE_AND)
    {
        return $this->addCompositeExpression('having', $type, $expression);
    }

    /**
     * Adds an "AND (HAVING)" clause to the query.
     *
     * @param string|array|\Fridge\DBAL\Query\Expression\CompositeExpression $expression The expression.
     *
     * @return \Fridge\DBAL\Query\QueryBuilder The query builder.
     */
    public function andHaving($expression)
    {
        return $this->addCompositeExpression('having', Expression\CompositeExpression::TYPE_AND, $expression);
    }

    /**
     * Adds an "OR (HAVING)" clause to the query.
     *
     * @param string|array|\Fridge\DBAL\Query\Expression\CompositeExpression $expression The expression.
     *
     * @return \Fridge\DBAL\Query\QueryBuilder The query builder.
     */
    public function orHaving($expression)
    {
        return $this->addCompositeExpression('having', Expression\CompositeExpression::TYPE_OR, $expression);
    }

    /**
     * Adds an "ORDER BY" clause to the query.
     *
     * @param string|array $orderBy The order by clauses.
     *
     * @return \Fridge\DBAL\Query\QueryBuilder The query builder.
     */
    public function orderBy($orderBy)
    {
        $this->parts['orderBy'] = array_merge($this->parts['orderBy'], (array) $orderBy);

        return $this;
    }

    /**
     * Sets the query offset.
     *
     * @param integer $offset The offset.
     *
     * @return \Fridge\DBAL\Query\QueryBuilder The query builder.
     */
    public function offset($offset)
    {
        $this->parts['offset'] = $offset;

        return $this;
    }

    /**
     * Sets the query limit.
     *
     * @param integer $limit The limit.
     *
     * @return \Fridge\DBAL\Query\QueryBuilder The query builder.
     */
    public function limit($limit)
    {
        $this->parts['limit'] = $limit;

        return $this;
    }

    /**
     * Sets query parameters.
     *
     * @param array $parameters The query parameters.
     * @param array $types      The query parameter types.
     *
     * @return \Fridge\DBAL\Query\QueryBuilder The query builder.
     */
    public function setParameters(array $parameters, array $types = array())
    {
        foreach ($parameters as $parameter => $value) {
            if (isset($types[$parameter])) {
                $this->setParameter($parameter, $value, $types[$parameter]);
            } else {
                $this->setParameter($parameter, $value);
            }
        }

        return $this;
    }

    /**
     * Sets a query parameter.
     *
     * @param string $parameter The parameter.
     * @param mixed  $value     The value.
     * @param mixed  $type      The type.
     *
     * @return \Fridge\DBAL\Query\QueryBuilder The query builder.
     */
    public function setParameter($parameter, $value, $type = null)
    {
        $this->parameters[$parameter] = $value;

        if ($type !== null) {
            $this->parameterTypes[$parameter] = $type;
        }

        if (is_int($parameter)) {
            $this->parameterCounters['positional']++;
        }

        return $this;
    }

    /**
     * Creates and sets a parameter.
     *
     * @param mixed $value The value.
     * @param mixed $type  The type.
     *
     * @return string The parameter placeholder.
     */
    public function createParameter($value, $type = null)
    {
        if (empty($this->parameters) || is_int(key($this->parameters))) {
            return $this->createPositionalParameter($value, $type);
        }

        return $this->createNamedParameter($value, $type);
    }

    /**
     * Creates and sets a positional parameter.
     *
     * @param mixed $value The value.
     * @param mixed $type  The type.
     *
     * @return string The positional parameter placeholder.
     */
    public function createPositionalParameter($value, $type = null)
    {
        $this->setParameter($this->parameterCounters['positional'], $value, $type);

        return '?';
    }

    /**
     * Creates and sets a named parameter.
     *
     * @param mixed  $value       The value.
     * @param mixed  $type        The type
     * @param string $placeholder The placeholder.
     *
     * @return string The named parameter placeholder.
     */
    public function createNamedParameter($value, $type = null, $placeholder = null)
    {
        if ($placeholder === null) {
            $placeholder = ':fridge';
        }

        $parameter = substr($placeholder, 1);

        if (!isset($this->parameterCounters['named'][$parameter])) {
            $this->parameterCounters['named'][$parameter] = 0;
        }

        $placeholder = $placeholder.$this->parameterCounters['named'][$parameter];
        $this->setParameter($parameter.$this->parameterCounters['named'][$parameter], $value, $type);

        $this->parameterCounters['named'][$parameter]++;

        return $placeholder;
    }

    /**
     * Gets the query parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Gets a query parameter.
     *
     * @param string $identifier The identifier.
     *
     * @return mixed The query parameter.
     */
    public function getParameter($identifier)
    {
        return isset($this->parameters[$identifier]) ? $this->parameters[$identifier] : null;
    }

    /**
     * Gets the query parameter types.
     *
     * @return array The query parameter types.
     */
    public function getParameterTypes()
    {
        return $this->parameterTypes;
    }

    /**
     * Gets a query parameter type.
     *
     * @param string $identifier The identifier.
     *
     * @return mixed The query parameter type.
     */
    public function getParameterType($identifier)
    {
        return isset($this->parameterTypes[$identifier]) ? $this->parameterTypes[$identifier] : null;
    }

    /**
     * Gets the generated query.
     *
     * @return string The generated query.
     */
    public function getQuery()
    {
        $query = null;

        switch ($this->type) {
            case self::SELECT:
                $query = $this->getSelectQuery();
                break;

            case self::INSERT:
                $query = $this->getInsertQuery();
                break;

            case self::UPDATE:
                $query = $this->getUpdateQuery();
                break;

            case self::DELETE:
                $query = $this->getDeleteQuery();
                break;
        }

        return $query;
    }

    /**
     * Executes the query.
     *
     * @return mixed The result set in case of a "SELECT" query else the number of effected rows.
     */
    public function execute()
    {
        if ($this->type === self::SELECT) {
            return $this->connection->executeQuery($this->getQuery(), $this->parameters, $this->parameterTypes);
        }

        return $this->connection->executeUpdate($this->getQuery(), $this->parameters, $this->parameterTypes);
    }

    /**
     * Adds a composite expression to the "WHERE" or "HAVING" clause.
     *
     * @param string                                                         $part       The query part.
     * @param string                                                         $type       The composite expression type (AND, OR)
     * @param string|array|\Fridge\DBAL\Query\Expression\CompositeExpression $expression The expression.
     *
     * @return \Fridge\DBAL\Query\QueryBuilder The query builder.
     */
    protected function addCompositeExpression($part, $type, $expression)
    {
        if (!($expression instanceof Expression\CompositeExpression)) {
            $expression = new Expression\CompositeExpression($type, (array) $expression);
        }

        if ($this->parts[$part] === null) {
            $this->parts[$part] = $expression;

            return $this;
        }

        if ($this->parts[$part]->getType() !== $type) {
            $this->parts[$part] = new Expression\CompositeExpression($type, array($this->parts[$part]));
        }

        foreach ($expression->getParts() as $expressionPart) {
            $this->parts[$part]->addPart($expressionPart);
        }

        return $this;
    }

    /**
     * Generates a "SELECT" query
     *
     * @return string The "SELECT" query.
     */
    protected function getSelectQuery()
    {
        return 'SELECT '.(empty($this->parts['select']) ? '*' : implode(', ', $this->parts['select'])).
               ' FROM '.$this->getFromClause().
               (($this->parts['where'] !== null) ? ' WHERE '.$this->parts['where'] : null).
               (!empty($this->parts['groupBy']) ? ' GROUP BY '.implode(', ', $this->parts['groupBy']) : null).
               (($this->parts['having'] !== null) ? ' HAVING '.$this->parts['having'] : null).
               (!empty($this->parts['orderBy']) ? ' ORDER BY '.implode(', ', $this->parts['orderBy']) : null).
               (($this->parts['limit'] !== null) ? ' LIMIT '.$this->parts['limit'] : null).
               (($this->parts['offset'] !== null) ? ' OFFSET '.$this->parts['offset'] : null);
    }

    /**
     * Generates an "INSERT" query.
     *
     * @return string The "INSERT" query.
     */
    protected function getInsertQuery()
    {
        return 'INSERT INTO '.$this->parts['from'][0]['table'].
               ' ('.implode(', ', array_keys($this->parts['set'])).')'.
               ' VALUES'.
               ' ('.implode(', ', $this->parts['set']).')';
    }

    /**
     * Generates an "UPDATE" query.
     *
     * @return string The "UPDATE" query.
     */
    protected function getUpdateQuery()
    {
        if (isset($this->parts['from'][0]['alias'])) {
            $fromClause = $this->parts['from'][0]['alias'].' FROM '.$this->getFromClause();
        } else {
            $fromClause = $this->parts['from'][0]['table'];
        }

        $setClause = array();

        foreach ($this->parts['set'] as $idenfier => $value) {
            $setClause[] = $this->getExpressionBuilder()->equal($idenfier, $value);
        }

        return 'UPDATE '.$fromClause.
               ' SET '.implode(', ', $setClause).
               (($this->parts['where'] !== null) ? ' WHERE '.$this->parts['where'] : null);
    }

    /**
     * Generates a "DELETE" query.
     *
     * @return string The "DELETE" query.
     */
    protected function getDeleteQuery()
    {
        $fromClause = null;

        if (isset($this->parts['from'][0]['alias'])) {
            $fromClause = $this->parts['from'][0]['alias'].' ';
        }

        $fromClause .= 'FROM '.$this->getFromClause();

        return 'DELETE '.$fromClause.
               (($this->parts['where'] !== null) ? ' WHERE '.$this->parts['where'] : null);
    }

    /**
     * Generates the "FROM" clause.
     *
     * @return string The "FROM" clause.
     */
    protected function getFromClause()
    {
        $fromClauses = array();

        foreach ($this->parts['from'] as $from) {
            $fromClause = $from['table'];

            if ($from['alias'] !== null) {
                $fromClause .= ' '.$from['alias'];
            }

            if (isset($this->parts['join'][$from['alias']])) {
                foreach ($this->parts['join'][$from['alias']] as $join) {
                    $fromClause .= ' '.strtoupper($join['type']).
                                   ' JOIN '.$join['table'].' '.$join['alias'].
                                   ' ON '.$join['expression'];
                }
            }

            $fromClauses[] = $fromClause;
        }

        return implode(', ', $fromClauses);
    }
}
