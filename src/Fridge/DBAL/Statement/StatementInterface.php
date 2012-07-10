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

use Fridge\DBAL\Base\StatementInterface as BaseStatement;

/**
 * Statement.
 *
 * All statements must implement this interface.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface StatementInterface extends BaseStatement
{
    /**
     * Gets the base statement.
     *
     * @return \Fridge\DBAL\Base\StatementInterface The base statement.
     */
    function getBase();

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
