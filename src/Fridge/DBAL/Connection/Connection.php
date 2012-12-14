<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Connection;

use \PDO;

use Fridge\DBAL\Adapter\StatementInterface,
    Fridge\DBAL\Configuration,
    Fridge\DBAL\Driver\DriverInterface,
    Fridge\DBAL\Event\Events,
    Fridge\DBAL\Event\PostConnectEvent,
    Fridge\DBAL\Exception\ConnectionException,
    Fridge\DBAL\Logging\Debugger,
    Fridge\DBAL\Query\Expression\ExpressionBuilder,
    Fridge\DBAL\Query\QueryBuilder,
    Fridge\DBAL\Query\Rewriter\QueryRewriter,
    Fridge\DBAL\Statement\Statement,
    Fridge\DBAL\Type\TypeUtility,
    Monolog\Logger;

/**
 * {@inheritdoc}
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Connection implements ConnectionInterface
{
    /** @const Transaction read commited constant. */
    const TRANSACTION_READ_COMMITTED = 'READ COMMITTED';

    /** @const Transaction read uncommited constant. */
    const TRANSACTION_READ_UNCOMMITTED = 'READ UNCOMMITTED';

    /** @const Transaction repeatable read constant. */
    const TRANSACTION_REPEATABLE_READ = 'REPEATABLE READ';

    /** @const Transaction read commited constant. */
    const TRANSACTION_SERIALIZABLE = 'SERIALIZABLE';

    /** @const Array parameter constant which enables query rewritting. */
    const PARAM_ARRAY = '[]';

    /** @var \Fridge\DBAL\Adapter\ConnectionInterface */
    protected $adapter;

    /** @var \Fridge\DBAL\Driver\DriverInterface */
    protected $driver;

    /** @var \Fridge\DBAL\Query\Expression\ExpressionBuilder */
    protected $expressionBuilder;

    /** @var \Fridge\DBAL\Configuration */
    protected $configuration;

    /** @var array */
    protected $parameters;

    /** @var boolean */
    protected $isConnected;

    /** @var integer */
    protected $transactionLevel;

    /** @var string */
    protected $transactionIsolation;

    /**
     * Creates a connection.
     *
     * @param array                               $parameters    The connection parameters.
     * @param \Fridge\DBAL\Driver\DriverInterface $driver        The connection driver.
     * @param \Fridge\DBAL\Configuration          $configuration The connection configuration.
     */
    public function __construct(array $parameters, DriverInterface $driver, Configuration $configuration = null)
    {
        if ($configuration === null) {
            $configuration = new Configuration();
        }

        $this->parameters = $parameters;
        $this->driver = $driver;
        $this->expressionBuilder = new ExpressionBuilder();
        $this->configuration = $configuration;

        $this->isConnected = false;
        $this->transactionLevel = 0;
        $this->transactionIsolation = $this->getPlatform()->getDefaultTransactionIsolation();
    }

    /**
     * {@inheritdoc}
     */
    public function getAdapter()
    {
        $this->connect();

        return $this->adapter;
    }

    /**
     * {@inheritdoc}
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlatform()
    {
        return $this->getDriver()->getPlatform();
    }

    /**
     * {@inheritdoc}
     */
    public function getSchemaManager()
    {
        return $this->getDriver()->getSchemaManager($this);
    }

    /**
     * {@inheritdoc}
     */
    public function createQueryBuilder()
    {
        return new QueryBuilder($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getExpressionBuilder()
    {
        return $this->expressionBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters)
    {
        $this->close();

        $this->parameters = array_merge($this->parameters, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getParameter($parameter)
    {
        return isset($this->parameters[$parameter]) ? $this->parameters[$parameter] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function setParameter($parameter, $value)
    {
        $this->close();

        $this->parameters[$parameter] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->getParameter('username');
    }

    /**
     * {@inheritdoc}
     */
    public function setUsername($username)
    {
        $this->setParameter('username', $username);
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->getParameter('password');
    }

    /**
     * {@inheritdoc}
     */
    public function setPassword($password)
    {
        $this->setParameter('password', $password);
    }

    /**
     * {@inheritdoc}
     */
    public function getDatabase()
    {
        return $this->getSchemaManager()->getDatabase();
    }

    /**
     * {@inheritdoc}
     */
    public function setDatabase($database)
    {
        $this->setParameter('dbname', $database);
    }

    /**
     * {@inheritdoc}
     */
    public function getHost()
    {
        return $this->getParameter('host');
    }

    /**
     * {@inheritdoc}
     */
    public function setHost($host)
    {
        $this->setParameter('host', $host);
    }

    /**
     * {@inheritdoc}
     */
    public function getPort()
    {
        return $this->getParameter('port');
    }

    /**
     * {@inheritdoc}
     */
    public function setPort($port)
    {
        $this->setParameter('port', $port);
    }

    /**
     * {@inheritdoc}
     */
    public function getDriverOptions()
    {
        return isset($this->parameters['driver_options']) ? $this->parameters['driver_options'] : array();
    }

    /**
     * {@inheritdoc}
     */
    public function setDriverOptions(array $options)
    {
        $this->setParameter('driver_options', $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionLevel()
    {
        return $this->transactionLevel;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionIsolation()
    {
        return $this->transactionIsolation;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\ConnectionException If the platform does not support transaction isolation.
     */
    public function setTransactionIsolation($isolation)
    {
        if (!$this->getPlatform()->supportTransactionIsolation()) {
            throw ConnectionException::transactionIsolationNotSupported();
        }

        $this->executeUpdate($this->getPlatform()->getSetTransactionIsolationSQLQuery($isolation));
        $this->transactionIsolation = $isolation;
    }

    /**
     * {@inheritdoc}
     */
    public function setCharset($charset)
    {
        $this->executeUpdate($this->getPlatform()->getSetCharsetSQLQuery($charset));
    }

    /**
     * {@inheritdoc}
     */
    public function isConnected()
    {
        return $this->isConnected;
    }

    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        if ($this->isConnected()) {
            return true;
        }

        $this->adapter = $this->getDriver()->connect(
            $this->getParameters(),
            $this->getUsername(),
            $this->getPassword(),
            $this->getDriverOptions()
        );

        $this->isConnected = true;

        if ($this->getConfiguration()->getEventDispatcher()->hasListeners(Events::POST_CONNECT)) {
            $event = new PostConnectEvent($this);
            $this->getConfiguration()->getEventDispatcher()->dispatch(Events::POST_CONNECT, $event);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        unset($this->adapter);
        $this->isConnected = false;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAll($query, array $parameters = array(), array $types = array())
    {
        return $this->executeQuery($query, $parameters, $types)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchArray($query, array $parameters = array(), array $types = array())
    {
        return $this->executeQuery($query, $parameters, $types)->fetch(PDO::FETCH_NUM);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAssoc($query, array $parameters = array(), array $types = array())
    {
        return $this->executeQuery($query, $parameters, $types)->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchColumn($query, array $parameters = array(), array $types = array(), $column = 0)
    {
        return $this->executeQuery($query, $parameters, $types)->fetchColumn($column);
    }

    /**
     * {@inheritdoc}
     */
    public function executeQuery($query, array $parameters = array(), array $types = array())
    {
        $debugger = $this->getConfiguration()->getLogger()->isHandling(Logger::INFO) ? new Debugger() : null;

        if ($debugger !== null) {
            $debugger->start($query);
        }

        if (!empty($parameters)) {
            list($query, $parameters, $types) = QueryRewriter::rewrite($query, $parameters, $types);
            $statement = $this->getAdapter()->prepare($query);

            if (!empty($types)) {
                $this->bindStatementParameters($statement, $parameters, $types);
                $statement->execute();
            } else {
                $statement->execute($parameters);
            }
        } else {
            $statement = $this->getAdapter()->query($query);
        }

        if ($debugger !== null) {
            $debugger->stop();
            $this->getConfiguration()->getLogger()->addInfo($debugger->toString(), $debugger->toArray());
        }

        return $statement;
    }

    /**
     * {@inheritdoc}
     */
    public function insert($tableName, array $datas, array $types = array())
    {
        $queryBuilder = $this->createQueryBuilder()->insert($tableName);

        foreach ($datas as $identifier => $data) {
            $dataType = isset($types[$identifier]) ? $types[$identifier] : null;

            $queryBuilder->set($identifier, $queryBuilder->createPositionalParameter($data, $dataType));
        }

        return $queryBuilder->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function update(
        $tableName,
        array $datas,
        array $dataTypes = array(),
        $expression = null,
        array $expressionParameters = array(),
        array $expressionParameterTypes = array()
    )
    {
        $queryBuilder = $this->createQueryBuilder()->update($tableName);

        $isPositional = empty($expressionParameters) || is_int(key($expressionParameters));

        foreach ($datas as $identifier => $value) {
            $dataType = isset($dataTypes[$identifier]) ? $dataTypes[$identifier] : null;

            if ($isPositional) {
                $queryBuilder->set($identifier, $queryBuilder->createPositionalParameter($value, $dataType));
            } else {
                $queryBuilder->set($identifier, $queryBuilder->createNamedParameter($value, $dataType));
            }
        }

        if ($expression !== null) {
            $queryBuilder->where($expression);

            $datasCount = count($datas);

            foreach ($expressionParameters as $identifier => $value) {
                $expressionType = isset($expressionParameterTypes[$identifier])
                    ? $expressionParameterTypes[$identifier]
                    : null;

                if ($isPositional) {
                    $queryBuilder->setParameter($identifier + $datasCount, $value, $expressionType);
                } else {
                    $queryBuilder->setParameter($identifier, $value, $expressionType);
                }
            }
        }

        return $queryBuilder->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        $tableName,
        $expression = null,
        array $expressionParameters = array(),
        array $expressionParameterTypes = array()
    )
    {
        $queryBuilder = $this->createQueryBuilder()->delete($tableName);

        if ($expression !== null) {
            $queryBuilder
                ->where($expression)
                ->setParameters($expressionParameters, $expressionParameterTypes);
        }

        return $queryBuilder->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function executeUpdate($query, array $parameters = array(), array $types = array())
    {
        $debugger = $this->getConfiguration()->getLogger()->isHandling(Logger::INFO) ? new Debugger() : null;

        if ($debugger !== null) {
            $debugger->start($query);
        }

        if (!empty($parameters)) {
            list($query, $parameters, $types) = QueryRewriter::rewrite($query, $parameters, $types);
            $statement = $this->getAdapter()->prepare($query);

            if (!empty($types)) {
                $this->bindStatementParameters($statement, $parameters, $types);
                $statement->execute();
            } else {
                $statement->execute($parameters);
            }

            $affectedRows = $statement->rowCount();
        } else {
            $affectedRows = $this->getAdapter()->exec($query);
        }

        if ($debugger !== null) {
            $debugger->stop();
            $this->getConfiguration()->getLogger()->addInfo($debugger->toString(), $debugger->toArray());
        }

        return $affectedRows;
    }

    /**
     * {@inheritdoc}
     */
    public function beginTransaction()
    {
        $this->transactionLevel++;

        if ($this->transactionLevel === 1) {
            $this->getAdapter()->beginTransaction();
        } else {
            $this->getAdapter()->exec($this->getPlatform()->getCreateSavepointSQLQuery($this->generateSavepointName()));
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\ConnectionException If there is no active transaction.
     */
    public function commit()
    {
        if ($this->transactionLevel === 0) {
            throw ConnectionException::noActiveTransaction();
        } elseif ($this->transactionLevel === 1) {
            $this->getAdapter()->commit();
        } else {
            $this->getAdapter()->exec($this->getPlatform()->getReleaseSavepointSQLQuery($this->generateSavepointName()));
        }

        $this->transactionLevel--;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\ConnectionException If there is no active transaction.
     */
    public function rollBack()
    {
        if ($this->transactionLevel === 0) {
            throw ConnectionException::noActiveTransaction();
        } elseif ($this->transactionLevel === 1) {
            $this->getAdapter()->rollBack();
        } else {
            $this->getAdapter()->exec($this->getPlatform()->getRollbackSavepointSQLQuery($this->generateSavepointName()));
        }

        $this->transactionLevel--;
    }

     /**
     * {@inheritdoc}
     */
    public function inTransaction()
    {
        return $this->transactionLevel !== 0;
    }

    /**
     * {@inheritdoc}
     */
    public function quote($string, $type = PDO::PARAM_STR)
    {
        TypeUtility::bindTypedValue($string, $type, $this->getPlatform());

        return $this->getAdapter()->quote($string, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function query()
    {
        return call_user_func_array(array($this->getAdapter(), 'query'), func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function prepare($statement)
    {
        return new Statement($statement, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function exec($statement)
    {
        return $this->getAdapter()->exec($statement);
    }

    /**
     * {@inheritdoc}
     */
    public function lastInsertId($name = null)
    {
        return $this->getAdapter()->lastInsertId($name);
    }

    /**
     * {@inheritdoc}
     */
    public function errorCode()
    {
        return $this->getAdapter()->errorCode();
    }

    /**
     * {@inheritdoc}
     */
    public function errorInfo()
    {
        return $this->getAdapter()->errorInfo();
    }

    /**
     * Binds typed parameters to a statement.
     *
     * @param \Fridge\DBAL\Adapter\StatementInterface $statement  The statement to bind on.
     * @param array                                   $parameters The statement parameters.
     * @param array                                   $types      The statement parameter types.
     */
    protected function bindStatementParameters(StatementInterface $statement, array $parameters, array $types)
    {
        foreach ($parameters as $key => $parameter) {
            if (is_int($key)) {
                $placeholder = $key + 1;
            } else {
                $placeholder = ':'.$key;
            }

            if (isset($types[$key])) {
                TypeUtility::bindTypedValue($parameter, $types[$key], $this->getPlatform());
                $statement->bindValue($placeholder, $parameter, $types[$key]);
            } else {
                $statement->bindValue($placeholder, $parameter);
            }
        }
    }

    /**
     * Generates a savepoint name according to the current transaction level.
     *
     * @return string The current savepoint name.
     */
    protected function generateSavepointName()
    {
        return 'FRIDGE_SAVEPOINT_'.$this->getTransactionLevel();
    }
}
