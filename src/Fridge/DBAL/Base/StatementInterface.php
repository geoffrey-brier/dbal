<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Base;

use Fridge\DBAL\Base\PDO;

/**
 * A base statement is a low-level class representing a prepared SQL statement.
 *
 * All base statements must implement this interface.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface StatementInterface
{
    /**
     * Binds a column to a PHP variable.
     *
     * @param integer|string $column        The column index or name.
     * @param mixed          &$variable     The PHP variable.
     * @param integer        $type          The column type.
     * @param integer        $length        The pre-allocation size.
     * @param array          $driverOptions The driver options.
     *
     * @return boolean TRUE if the column has been binded to the PHP variable else FALSE.
     */
    function bindColumn($column, &$variable, $type = null, $length = null, $driverOptions = null);

    /**
     * Binds a parameter to a PHP variable.
     *
     * @param integer|string $parameter     The parameter index or name.
     * @param mixed          &$variable     The PHP variable.
     * @param integer        $type          The parameter type.
     * @param integer        $length        The pre-allocation size.
     * @param array          $driverOptions The driver options.
     *
     * @return boolean TRUE if the parameter has been binded to the PHP variable else FALSE.
     */
    function bindParam($parameter, &$variable, $type = null, $length = null, $driverOptions = null);

    /**
     * Binds a value to a parameter.
     *
     * @param integer|string $parameter The parameter index or name.
     * @param mixed          $value     The value to bind.
     * @param integer        $type      The parameter type.
     *
     * @return boolean TRUE if the value has been binded to the parameter else FALSE.
     */
    function bindValue($parameter, $value, $type = null);

    /**
     * Executes the statement.
     *
     * @param array $parameters The statement parameters.
     *
     * @return boolean TRUE if the statement has been excuted else FALSE.
     */
    function execute($parameters = array());

    /**
     * Gets the number of rows affected by the last SQL statement.
     *
     * @return integer The number of rows affected by the last SQL statement.
     */
    function rowCount();

    /**
     * Fetches all rows from the result set.
     *
     * @param integer $fetchStyle           Controls how the next row will be returned.
     * @param mixed   $fetchArgument        Controls how the next row will be returned
     *                                      (Additional $fetchStyle parameter).
     * @param array   $constructorArguments Arguments of custom class constructor.
     *
     * @return array All rows from the result set.
     */
    function fetchAll($fetchStyle = PDO::FETCH_BOTH, $fetchArgument = null, $constructorArguments = array());

    /**
     * Fetches the next row from the result set.
     *
     * @param integer $fetchStyle        Controls how rows will be returned.
     * @param integer $cursorOrientation For a PDOStatement object representing a scrollable cursor,
     *                                   this value determines which row will be returned.
     * @param integer $cursorOffset      The cursor offset.
     *
     * @return mixed The next row from the result set.
     */
    function fetch($fetchStyle = PDO::FETCH_BOTH, $cursorOrientation = PDO::FETCH_ORI_NEXT, $cursorOffset = 0);

    /**
     * Fetches the value of a single column from the next row of the result set.
     *
     * @param integer $columnIndex The column index.
     *
     * @return mixed The value of the single column from the next row of the result set.
     */
    function fetchColumn($columnIndex = 0);

    /**
     * Fetches the next row as an object.
     *
     * @param string $className            The class name.
     * @param array  $constructorArguments The constructor arguments.
     *
     * @return mixed The nex row as an object.
     */
    function fetchObject($className = 'stdClass', $constructorArguments = array());

    /**
     * Sets the default fetch mode.
     *
     * @param integer $mode The default fetch mode.
     *
     * @return boolean TRUE if the default fetch mode has been setted else FALSE.
     */
    function setFetchMode($mode);

    /**
     * Returns the number of columns in the result set.
     *
     * @return integer The number of columns in the result set.
     */
    function columnCount();

    /**
     * Advances to the next rowset.
     *
     * @return boolean TRUE if the rowest has been advanced to the next row else FALSE.
     */
    function nextRowset();

    /**
     * Closes the cursor in order to be able to execute the statement again.
     *
     * @return boolean TRUE if the cursor has been closed else FALSE.
     */
    function closeCursor();

    /**
     * Gets the last error code associated with the last operation on the statement.
     *
     * @return string The last error code associated with the last operation on the statement.
     */
    function errorCode();

    /**
     * Gets the last error info associated with the last operation on the statement.
     *
     * @return string The last error info associated with the last operation on the statement.
     */
    function errorInfo();

    /**
     * Dumps the informations contained in the statement directly on the output.
     */
    function debugDumpParams();

    /**
     * Gets a statement attribute value.
     *
     * @param integer $attribute The statement attribute
     *
     * @return mixed The statement attribute value.
     */
    function getAttribute($attribute);

    /**
     * Sets a statement attribute.
     *
     * @param integer $attribute The statement attribute.
     * @param mixed   $value     The statement attribute value.
     *
     * @return boolean TRUE if the statement attribute has been setted else FALSE.
     */
    function setAttribute($attribute, $value);
}
