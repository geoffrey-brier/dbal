<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\Fixture;

/**
 * A fixture describes a database schema representing with a DBAL schema objects graph and an SQL script.
 * This script is used to build the schema on your database and the objects graph is used by some test cases
 * in order to compare the builded database schema with the expected database schema.
 *
 * All fixtures must implement this interface.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface FixtureInterface
{
    /**
     * Creates the fixture.
     */
    function create();

    /**
     * Drops the fixture.
     */
    function drop();

    /**
     * Creates the fixture database.
     */
    function createDatabase();

    /**
     * Drops the fixture database.
     */
    function dropDatabase();

    /**
     * Creates the fixture schema.
     */
    function createSchema();

    /**
     * Drops the fixture schema.
     */
    function dropSchema();

    /**
     * Creates the fixture datas.
     */
    function createDatas();

    /**
     * Drops the fixture datas.
     */
    function dropDatas();

    /**
     * Gets the PHPUnit settings.
     *
     * @return array The PHPUnit settings.
     */
    function getSettings();

    /**
     * Gets the database.
     *
     * @return string The database.
     */
    function getDatabase();

    /**
     * Gets the schema.
     *
     * @return Fridge\DBAL\Schema\Schema The schema.
     */
    function getSchema();

    /**
     * Gets the sequences.
     *
     * @return array The sequences.
     */
    function getSequences();

    /**
     * Gets the views.
     *
     * @return array The views.
     */
    function getViews();

    /**
     * Gets the table names.
     *
     * @return array The table names.
     */
    function getTableNames();

    /**
     * Gets a table.
     *
     * @param string $name The table name.
     *
     * @return array The table.
     */
    function getTable($name);

    /**
     * Gets the table columns.
     *
     * @param string $table The table name.
     *
     * @return array The table columns.
     */
    function getTableColumns($table);

    /**
     * Gets the table primary key.
     *
     * @param string $table The table name.
     *
     * @return \Fridge\DBAL\Schema\PrimaryKey|null The table primary key.
     */
    function getTablePrimaryKey($table);

    /**
     * Gets the table foreign keys.
     *
     * @param string $table The table name.
     *
     * @return array The table foreign keys.
     */
    function getTableForeignKeys($table);

    /**
     * Gets the table indexes.
     *
     * @param string $table The table name.
     *
     * @return array The table indexes.
     */
    function getTableIndexes($table);

    /**
     * Gets the table checks.
     *
     * @param string $table The table name.
     *
     * @return array The table checks.
     */
    function getTableChecks($table);

    /**
     * Gets a query that can be executed on the database.
     *
     * @return string The query.
     */
    function getQuery();

    /**
     * Gets a query with named parameters.
     *
     * @return string The query with named parameters.
     */
    function getQueryWithNamedParameters();

    /**
     * Gets a query with positional parameters.
     *
     * @return string The query with positional parameters.
     */
    function getQueryWithPositionalParameters();

    /**
     * Gets an update query.
     *
     * @return string The update query.
     */
    function getUpdateQuery();

    /**
     * Gets an update query with named parameters.
     *
     * @return string The update query with named parameters.
     */
    function getUpdateQueryWithNamedParameters();

    /**
     * Gets an update query with positional parameters.
     *
     * @return string The update query with positional parameters.
     */
    function getUpdateQueryWithPositionalParameters();

    /**
     * Gets the named query parameters.
     *
     * @return array The named query parameters.
     */
    function getNamedQueryParameters();

    /**
     * Gets the positional query parameters.
     *
     * @return array The positional query parameters.
     */
    function getPositionalQueryParameters();

    /**
     * Gets the named typed query parameters.
     *
     * @return array The named typed query parameters.
     */
    function getNamedTypedQueryParameters();

    /**
     * Gets the positional typed query parameters.
     *
     * @return array The positional typed query parameters.
     */
    function getPositionalTypedQueryParameters();

    /**
     * Gets the named query types.
     *
     * @return array The named query types.
     */
    function getNamedQueryTypes();

    /**
     * Gets the positional query types.
     *
     * @return array The positional query types.
     */
    function getPositionalQueryTypes();

    /**
     * Gets the partial named query types.
     *
     * @return array The partial named query types.
     */
    function getPartialNamedQueryTypes();

    /**
     * Gets the partial positional query types.
     *
     * @return array The partial positional query types.
     */
    function getPartialPositionalQueryTypes();

    /**
     * Gets the query result.
     *
     * @return array The query result.
     */
    function getQueryResult();
}
