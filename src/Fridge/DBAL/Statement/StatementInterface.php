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

use Fridge\DBAL\Adapter\StatementInterface as AdapterStatement;

/**
 * Statement.
 *
 * All statements must implement this interface.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface StatementInterface extends AdapterStatement
{
    /**
     * Gets the low-level statement.
     *
     * @return \Fridge\DBAL\Adapter\StatementInterface The low-level statement.
     */
    function getAdapter();

    /**
     * Gets the connection linked to the statement.
     *
     * @return \Fridge\DBAL\Connection\ConnectionInterface The connection linked to the statement.
     */
    function getConnection();

    /**
     * Gets the SQL statement.
     *
     * @return string The SQL statement.
     */
    function getSQL();

    /**
     * Gets the PDO driver options.
     *
     * @return array The PDO driver options.
     */
    function getOptions();
}
