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

use \mysqli,
    \PDO;

use Fridge\DBAL\Adapter\ConnectionInterface,
    Fridge\DBAL\Exception\Adapter\MysqliException;

/**
 * {@inheritdoc}
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Connection implements ConnectionInterface
{
    /** @var \mysqli */
    protected $base;

    /** @var boolean */
    protected $inTransaction;

    /**
     * Mysqli connection constructor.
     *
     * $parameters can contain:
     *  - dbname
     *  - host
     *  - port
     *  - unix_socket
     *  - charset
     *
     * @param array  $parameters The database parameters.
     * @param string $username   The database username.
     * @param string $password   The database password.
     */
    public function __construct(array $parameters, $username, $password)
    {
        $host = isset($parameters['host']) ? $parameters['host'] : ini_get('mysqli.default_host');
        $database = isset($parameters['dbname']) ? $parameters['dbname'] : '';
        $port = isset($parameters['post']) ? $parameters['port'] : ini_get('mysqli.default_port');
        $unixSocket = isset($parameters['unix_socket']) ? $parameters['unix_socket'] : ini_get('mysqli.default_socket');

        $errorReporting = error_reporting(~E_ALL);
        $this->base = new mysqli($host, $username, $password, $database, $port, $unixSocket);
        error_reporting($errorReporting);

        if ($this->base->connect_error !== null) {
            throw new MysqliException($this->base->connect_error, $this->base->connect_errno);
        }

        if (isset($parameters['charset']) && ($this->base->set_charset($parameters['charset']) === false)) {
            throw new MysqliException($this->base->error, $this->base->errno);
        }

        $this->inTransaction = false;
    }

    /**
     * Gets the mysqli low-level connection.
     *
     * @return \mysqli The mysqli low-level connection.
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * {@inheritdoc}
     */
    public function beginTransaction()
    {
        $result = $this->base->query('START TRANSACTION');

        if ($result) {
            $this->inTransaction = true;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        $this->inTransaction = false;

        return $this->base->commit();
    }

    /**
     * {@inheritdoc}
     */
    public function rollBack()
    {
        $this->inTransaction = false;

        return $this->base->rollback();
    }

    /**
     * {@inheritdoc}
     */
    public function inTransaction()
    {
        return $this->inTransaction;
    }

    /**
     * {@inheritdoc}
     */
    public function quote($string, $type = PDO::PARAM_STR)
    {
        return '\''.$this->base->real_escape_string($string).'\'';
    }

    /**
     * {@inheritdoc}
     */
    public function query()
    {
        $args = func_get_args();

        $statement = $this->prepare($args[0]);
        $statement->execute();

        return $statement;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare($statement)
    {
        return new Statement($statement, $this->base);
    }

    /**
     * {@inheritdoc}
     */
    public function exec($statement)
    {
        $result = $this->base->query($statement);

        if ($result === false) {
            throw new MysqliException($this->base->error, $this->base->errno);
        }

        return $this->base->affected_rows;
    }

    /**
     * {@inheritdoc}
     */
    public function lastInsertId($name = null)
    {
        return $this->base->insert_id;
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
}
