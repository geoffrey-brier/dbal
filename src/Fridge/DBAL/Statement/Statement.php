<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Statement;

use \IteratorAggregate,
    \PDO;

use Fridge\DBAL\Connection\ConnectionInterface,
    Fridge\DBAL\Type\TypeUtility;

/**
 * {@inheritdoc}
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Statement implements StatementInterface, IteratorAggregate
{
    /** @var \Fridge\DBAL\Adapter\StatementInterface */
    protected $adapter;

    /** @var \Fridge\DBAL\Connection\ConnectionInterface */
    protected $connection;

    /** @var string */
    protected $sql;

    /**
     * Creates a statement
     *
     * @param string                                      $sql        The SQL of the statement.
     * @param \Fridge\DBAL\Connection\ConnectionInterface $connection The connection linked to the statement.
     */
    public function __construct($sql, ConnectionInterface $connection)
    {
        $this->sql = $sql;
        $this->connection = $connection;

        $this->adapter = $this->connection->getAdapter()->prepare($this->sql);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getSQL()
    {
        return $this->sql;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return $this->adapter;
    }

    /**
     * {@inheritdoc}
     *
     * This method only suppports PDO type.
     */
    public function bindColumn($column, &$variable, $type = null, $length = null, $driverOptions = null)
    {
        return $this->adapter->bindColumn($column, $variable, $type, $length, $driverOptions);
    }

    /**
     * {@inheritdoc}
     *
     * This method only suppports PDO type.
     */
    public function bindParam($parameter, &$variable, $type = null, $length = null, $driverOptions = null)
    {
        return $this->adapter->bindParam($parameter, $variable, $type, $length, $driverOptions);
    }

    /**
     * {@inheritdoc}
     *
     * This method supports PDO or DBAL type.
     */
    public function bindValue($parameter, $value, $type = null)
    {
        TypeUtility::bindTypedValue($value, $type, $this->connection->getPlatform());

        return $this->adapter->bindValue($parameter, $value, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function closeCursor()
    {
        return $this->adapter->closeCursor();
    }

    /**
     * {@inheritdoc}
     */
    public function columnCount()
    {
        return $this->adapter->columnCount();
    }

    /**
     * {@inheritdoc}
     */
    public function debugDumpParams()
    {
        return $this->adapter->debugDumpParams();
    }

    /**
     * {@inheritdoc}
     */
    public function errorCode()
    {
        return $this->adapter->errorCode();
    }

    /**
     * {@inheritdoc}
     */
    public function errorInfo()
    {
        return $this->adapter->errorInfo();
    }

    /**
     * {@inheritdoc}
     */
    public function execute($parameters = array())
    {
        return $this->adapter->execute($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($fetchStyle = PDO::FETCH_BOTH, $cursorOrientation = PDO::FETCH_ORI_NEXT, $cursorOffset = 0)
    {
        return $this->adapter->fetch($fetchStyle, $cursorOrientation, $cursorOffset);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAll($fetchStyle = PDO::FETCH_BOTH, $fetchArgument = null, $constructorArguments = array())
    {
        return $this->adapter->fetchAll($fetchStyle, $fetchArgument, $constructorArguments);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchColumn($columnIndex = 0)
    {
        return $this->adapter->fetchColumn($columnIndex);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchObject($className = 'stdClass', $constructorArguments = array())
    {
        return $this->adapter->fetchObject($className, $constructorArguments);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($attribute)
    {
        return $this->adapter->getAttribute($attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function nextRowset()
    {
        return $this->adapter->nextRowset();
    }

    /**
     * {@inheritdoc}
     */
    public function rowCount()
    {
        return $this->adapter->rowCount();
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute($attribute, $value)
    {
        return $this->adapter->setAttribute($attribute, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function setFetchMode($mode)
    {
        return call_user_func_array(array($this->adapter, 'setFetchMode'), func_get_args());
    }
}
