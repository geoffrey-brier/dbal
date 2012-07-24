<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Adapter\Mysqli;

use \ArrayIterator,
    \IteratorAggregate,
    \mysqli,
    \PDO;

use Fridge\DBAL\Adapter\StatementInterface,
    Fridge\DBAL\Exception\Adapter\MysqliException;

/**
 * {@inheritdoc}
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Statement implements StatementInterface, IteratorAggregate
{
    /** @var array */
    static protected $mappedTypes = array(
        PDO::PARAM_NULL => 's',
        PDO::PARAM_INT  => 'i',
        PDO::PARAM_STR  => 's',
        PDO::PARAM_LOB  => 's',
        PDO::PARAM_BOOL => 'i',
    );

    /** @var \mysqli_stmt */
    protected $base;

    /** @var \Fridge\DBAL\Adapter\Mysqli\StatementRewriter */
    protected $statementRewriter;

    /** @var integer */
    protected $defaultFetchMode;

    /** @var array */
    protected $bindedParameters;

    /** @var array */
    protected $bindedTypes;

    /** @var array */
    protected $bindedValues;

    /** @var array */
    protected $resultFields;

    /** @var array */
    protected $result;

    /**
     * Mysqli statement constructor.
     *
     * @param string  $statement  The SQL statement.
     * @param \mysqli $connection The mysqli connection.
     */
    public function __construct($statement, mysqli $connection)
    {
        $this->statementRewriter = new StatementRewriter($statement);

        $this->base = $connection->prepare($this->statementRewriter->rewriteStatement());

        if ($this->base === false) {
            throw new MysqliException($connection->error, $connection->errno);
        }

        $this->defaultFetchMode = PDO::FETCH_BOTH;

        $this->bindedParameters = array();
        $this->bindedTypes = array();
        $this->bindedValues = array();
    }

    /**
     * Gets the mysqli low-level statement.
     *
     * @return \mysqli_stmt The mysqli low-level statement.
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        $datas = $this->fetchAll();

        return new ArrayIterator($datas);
    }

    /**
     * {@inheritdoc}
     */
    public function bindParam($parameter, &$variable, $type = null)
    {
        if ($type === null) {
            $type = PDO::PARAM_NULL;
        }

        $mappedType = self::getMappedType($type);

        if (is_string($parameter)) {
            if (substr($parameter, 0, 1) !== ':') {
                $parameter = ':'.$parameter;
            }

            $parameters = $this->statementRewriter->rewriteParameter($parameter);
        } else {
            $parameters = array($parameter);
        }

        foreach ($parameters as $parameter) {
            $this->bindedParameters[$parameter] = &$variable;
            $this->bindedTypes[$parameter - 1] = $mappedType;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function bindValue($parameter, $value, $type = null)
    {
        $this->bindedValues[$parameter] = $value;

        return $this->bindParam($parameter, $this->bindedValues[$parameter], $type);
    }

    /**
     * {@inheritdoc}
     */
    public function execute($parameters = array())
    {
        if (!empty($parameters)) {
            $this->bindValues($parameters);
        }

        if (!empty($this->bindedParameters)) {
            $this->bindParameters();
        }

        if ($this->base->execute() === false) {
            throw new MysqliException($this->base->error, $this->base->errno);
        }

        $this->base->store_result();

        if (empty($this->resultFields)) {
            $this->bindResultFields();
        }

        if (!empty($this->resultFields)) {
            $this->bindResult();
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rowCount()
    {
        if (!empty($this->resultFields)) {
            return $this->base->num_rows;
        }

        return $this->base->affected_rows;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAll($fetchMode = PDO::FETCH_BOTH)
    {
        $results = array();

        while (($result = $this->fetch($fetchMode)) !== null) {
            $results[] = $result;
        }

        return $results;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($fetchMode = PDO::FETCH_BOTH)
    {
        $fetchResult = $this->base->fetch();

        if ($fetchResult === false) {
            throw new MysqliException($this->base->error, $this->base->errno);
        }

        if ($fetchResult === null) {
            return null;
        }

        $values = array();
        foreach ($this->result as $value) {
            $values[] = $value;
        }

        if ($fetchMode === null) {
            $fetchMode = $this->defaultFetchMode;
        }

        switch ($fetchMode) {
            case PDO::FETCH_NUM:
                return $values;
                break;

            case PDO::FETCH_ASSOC:
                return array_combine($this->resultFields, $values);
                break;

            case PDO::FETCH_BOTH:
                $result = array_combine($this->resultFields, $values);
                $result += $values;

                return $result;
                break;

            default:
                throw new MysqliException(sprintf('The fetch mode "%s" is not supported.', $fetchMode));
                break;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function fetchColumn($columnIndex = 0)
    {
        $result = $this->fetch(PDO::FETCH_NUM);

        if ($result === null) {
            return false;
        }

        return $result[$columnIndex];
    }

    /**
     * {@inheritdoc}
     */
    public function setFetchMode($fetchMode)
    {
        $this->defaultFetchMode = $fetchMode;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function columnCount()
    {
        return $this->base->field_count;
    }

    /**
     * {@inheritdoc}
     */
    public function closeCursor()
    {
        $this->base->free_result();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function errorCode()
    {
        return $this->base->errno;
    }

    /**
     * {@inheritdoc}
     */
    public function errorInfo()
    {
        return array($this->base->errno, $this->base->errno, $this->base->error);
    }

    /**
     * Gets the mapped type.
     *
     * @param integer $type The type (PDO::PARAM_*).
     *
     * @return string The mapped type.
     */
    static protected function getMappedType($type)
    {
        if (!isset(self::$mappedTypes[$type])) {
            throw MysqliException::mappedTypeDoesNotExist($type);
        }

        return self::$mappedTypes[$type];
    }

    /**
     * Binds values on the statement.
     *
     * @param array $values Associative array describing parameter => value pairs.
     */
    protected function bindValues(array $values)
    {
        $this->bindedParameters = array();
        $this->bindedTypes = array();
        $this->bindedValues = array();

        foreach ($values as $parameter => $value) {
            if (is_int($parameter)) {
                $parameter++;
            }

            $this->bindValue($parameter, $value, PDO::PARAM_STR);
        }
    }

    /**
     * Binds the parameters on the low-level statement.
     */
    protected function bindParameters()
    {
        $bindedParameterReferences = array();

        $bindedParameterReferences[0] = implode('', $this->bindedTypes);

        foreach ($this->bindedParameters as $key => &$parameter) {
            $bindedParameterReferences[$key] = &$parameter;
        }

        call_user_func_array(array($this->base, 'bind_param'), $bindedParameterReferences);
    }

    /**
     * Binds the low-level result fields.
     */
    protected function bindResultFields()
    {
        $resultMetadata = $this->base->result_metadata();

        if ($resultMetadata !== false) {
            $this->resultFields = array();

            foreach ($resultMetadata->fetch_fields() as $field) {
                $this->resultFields[] = $field->name;
            }

            $resultMetadata->free();
        }
    }

    /**
     * Binds the low-level statement result.
     */
    protected function bindResult()
    {
        $this->result = array_fill(0, count($this->resultFields), null);

        $resultReferences = array();
        foreach ($this->result as $key => &$result) {
            $resultReferences[$key] = &$result;
        }

        call_user_func_array(array($this->base, 'bind_result'), $resultReferences);

        $this->result = $resultReferences;
    }
}
