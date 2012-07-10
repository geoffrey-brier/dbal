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

use \IteratorAggregate;

use Fridge\DBAL\Base\PDO,
    Fridge\DBAL\Connection\ConnectionInterface,
    Fridge\DBAL\Type\Type;

/**
 * {@inheritdoc}
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Statement implements StatementInterface, IteratorAggregate
{
    /** @var \Fridge\DBAL\Base\StatementInterface */
    protected $base;

    /** @var \Fridge\DBAL\Connection\ConnectionInterface */
    protected $connection;

    /** @var string */
    protected $sql;

    /** @var array */
    protected $options;

    /**
     * Creates a statement
     *
     * @param string                                     $sql        The SQL of the statement.
     * @param \Fridge\DBAL\Connection\ConnectionInterface $connection The connection linked to the statement.
     * @param array                                      $options    The PDO driver options.
     */
    public function __construct($sql, ConnectionInterface $connection, array $options = array())
    {
        $this->sql = $sql;
        $this->connection = $connection;
        $this->options = $options;

        $this->base = $this->connection->getBase()->prepare($this->sql, $this->options);
    }

    /**
     * {@inheritdoc}
     */
    public function getBase()
    {
        return $this->base;
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
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return $this->base;
    }

    /**
     * {@inheritdoc}
     *
     * This method only suppports PDO type.
     */
    public function bindColumn($column, &$variable, $type = null, $length = null, $driverOptions = null)
    {
        return $this->base->bindColumn($column, $variable, $type, $length, $driverOptions);
    }

    /**
     * {@inheritdoc}
     *
     * This method only suppports PDO type.
     */
    public function bindParam($parameter, &$variable, $type = null, $length = null, $driverOptions = null)
    {
        return $this->base->bindParam($parameter, $variable, $type, $length, $driverOptions);
    }

    /**
     * {@inheritdoc}
     *
     * This method supports PDO or DBAL type.
     */
    public function bindValue($parameter, $value, $type = null)
    {
        list($parameter, $type) = Type::getTypeInfo($parameter, $type, $this->connection->getDriver()->getPlatform());

        return $this->base->bindValue($parameter, $value, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function closeCursor()
    {
        return $this->base->closeCursor();
    }

    /**
     * {@inheritdoc}
     */
    public function columnCount()
    {
        return $this->base->columnCount();
    }

    /**
     * {@inheritdoc}
     */
    public function debugDumpParams()
    {
        return $this->base->debugDumpParams();
    }

    /**
     * {@inheritdoc}
     */
    public function errorCode()
    {
        return $this->base->errorCode();
    }

    /**
     * {@inheritdoc}
     */
    public function errorInfo()
    {
        return $this->base->errorInfo();
    }

    /**
     * {@inheritdoc}
     */
    public function execute($parameters = array())
    {
        return $this->base->execute($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($fetchStyle = PDO::FETCH_BOTH, $cursorOrientation = PDO::FETCH_ORI_NEXT, $cursorOffset = 0)
    {
        return $this->base->fetch($fetchStyle, $cursorOrientation, $cursorOffset);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAll($fetchStyle = PDO::FETCH_BOTH, $fetchArgument = null, $constructorArguments = array())
    {
        return $this->base->fetchAll($fetchStyle, $fetchArgument, $constructorArguments);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchColumn($columnIndex = 0)
    {
        return $this->base->fetchColumn($columnIndex);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchObject($className = 'stdClass', $constructorArguments = array())
    {
        return $this->base->fetchObject($className, $constructorArguments);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($attribute)
    {
        return $this->getAttribute($attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function nextRowset()
    {
        return $this->base->nextRowset();
    }

    /**
     * {@inheritdoc}
     */
    public function rowCount()
    {
        return $this->base->rowCount();
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute($attribute, $value)
    {
        return $this->base->setAttribute($attribute, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function setFetchMode($mode)
    {
        return call_user_func_array(array($this->base, 'setFetchMode'), fun_get_args());
    }
}
