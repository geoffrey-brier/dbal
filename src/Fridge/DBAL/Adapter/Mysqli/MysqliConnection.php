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
class MysqliConnection implements ConnectionInterface
{
    /** @var \mysqli */
    protected $mysqli;

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

        $this->mysqli = @new mysqli($host, $username, $password, $database, $port, $unixSocket);

        if ($this->mysqli->connect_error !== null) {
            throw new MysqliException($this->mysqli->connect_error, $this->mysqli->connect_errno);
        }

        if (isset($parameters['charset']) && ($this->mysqli->set_charset($parameters['charset']) === false)) {
            throw new MysqliException($this->mysqli->error, $this->mysqli->errno);
        }

        $this->inTransaction = false;
    }

    /**
     * Gets the mysqli low-level connection.
     *
     * @return \mysqli The mysqli low-level connection.
     */
    public function getMysqli()
    {
        return $this->mysqli;
    }

    /**
     * {@inheritdoc}
     */
    public function beginTransaction()
    {
        $result = $this->mysqli->query('START TRANSACTION');

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

        return $this->mysqli->commit();
    }

    /**
     * {@inheritdoc}
     */
    public function rollBack()
    {
        $this->inTransaction = false;

        return $this->mysqli->rollback();
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
        return '\''.$this->mysqli->real_escape_string($string).'\'';
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
        return new MysqliStatement($statement, $this->mysqli);
    }

    /**
     * {@inheritdoc}
     */
    public function exec($statement)
    {
        $result = $this->mysqli->query($statement);

        if ($result === false) {
            throw new MysqliException($this->mysqli->error, $this->mysqli->errno);
        }

        return $this->mysqli->affected_rows;
    }

    /**
     * {@inheritdoc}
     */
    public function lastInsertId($name = null)
    {
        return $this->mysqli->insert_id;
    }

    /**
     * {@inheritdoc}
     */
    public function errorCode()
    {
        return $this->mysqli->errno;
    }

    /**
     * {@inheritdoc}
     */
    public function errorInfo()
    {
        return array($this->mysqli->errno, $this->mysqli->errno, $this->mysqli->error);
    }
}
