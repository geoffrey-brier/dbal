<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Adapter;

/**
 * Low-level class representing a database connection.
 *
 * All low-level connections must implement this interface.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface ConnectionInterface
{
    /**
     * Starts a transaction.
     *
     * @return boolean TRUE if the transaction has been started else FALSE.
     */
    function beginTransaction();

    /**
     * Saves a transaction.
     *
     * @return boolean TRUE if the transaction has been saved else FALSE.
     */
    function commit();

    /**
     * Cancels a transaction.
     *
     * @return boolean TRUE if the transaction has been canceled else FALSE.
     */
    function rollBack();

    /**
     * Checks if a transaction has been started.
     *
     * @return boolean TRUE if a transaction has been started else FALSE.
     */
    function inTransaction();

    /**
     * Quotes a string.
     *
     * @param string  $string The string to quote.
     * @param integer $type   The PDO type.
     *
     * @return string The quoted string.
     */
    function quote($string, $type = null);

    /**
     * Executes an SQL query.
     *
     * @return \Fridge\DBAL\Adapter\StatementInterface The executed query.
     */
    function query();

    /**
     * Prepares an SQL statement in order to be executed.
     *
     * @param string $statement The statement to prepare.
     *
     * @return \Fridge\DBAL\Adapter\StatementInterface The prepared statement.
     */
    function prepare($statement);

    /**
     * Executes an SQL statement.
     *
     * @param string $statement The statement to execute.
     *
     * @return integer The number of affected rows.
     */
    function exec($statement);

    /**
     * Gets the last generated ID or sequence value.
     *
     * @param string $name The name of the sequence object from which the ID should be returned.
     *
     * @return string The last generated ID or sequence value.
     */
    function lastInsertId($name = null);

    /**
     * Gets the last error code associated with the last operation.
     *
     * @return string The last error code associated with the last operation.
     */
    function errorCode();

    /**
     * Gets the last error info associated with the last operation.
     *
     * @return string The last error code associated with the last operation.
     */
    function errorInfo();

    /**
     * Retrieve a connection attribute value.
     *
     * @param integer $attribute The connection attribute.
     *
     * @return mixed The connection attribute value.
     */
    function getAttribute($attribute);

    /**
     * Sets a connection attribute.
     *
     * @param integer $attribute The connection attribute.
     * @param mixed   $value     The connection attribute value.
     *
     * @return boolean TRUE if the connection attribute has been setted else FALSE.
     */
    function setAttribute($attribute, $value);
}
