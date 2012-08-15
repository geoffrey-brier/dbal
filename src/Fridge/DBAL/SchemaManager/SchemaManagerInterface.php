<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\SchemaManager;

use Fridge\DBAL\Schema;

/**
 * A schema manager allows to fetch / create / drop schema entities.
 *
 * All schema managers must implement this interface.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface SchemaManagerInterface
{
    /**
     * Gets the schema manager connection.
     *
     * @return \Fridge\DBAL\Connection\ConnectionInterface The schema manager connection.
     */
    function getConnection();

    /**
     * Gets the databases.
     *
     * @return array The databases
     */
    function getDatabases();

    /**
     * Gets the database name.
     *
     * @return string The database name.
     */
    function getDatabase();

    /**
     * Gets the schema.
     *
     * @param string $database The database name.
     *
     * @return \Fridge\DBAL\Schema\Schema The schema.
     */
    function getSchema($database = null);

    /**
     * Gets the sequences.
     *
     * @param string $database The database name.
     *
     * @return array The sequences.
     */
    function getSequences($database = null);

    /**
     * Gets the views.
     *
     * @param string $database The database name.
     *
     * @return array The views.
     */
    function getViews($database = null);

    /**
     * Gets the table names.
     *
     * @param string $database The database name.
     *
     * @return array The table names.
     */
    function getTableNames($database = null);

    /**
     * Gets the tables
     *
     * @param string $database The database name.
     *
     * @return array The tables.
     */
    function getTables($database = null);

    /**
     * Gets a table.
     *
     * @param string $table    The table name.
     * @param string $database The database name.
     *
     * @return \Fridge\DBAL\Schema\Table The table.
     */
    function getTable($table, $database = null);

    /**
     * Gets the columns of a table.
     *
     * @param string $table    The table name.
     * @param string $database The database name.
     *
     * @return array The table columns.
     */
    function getTableColumns($table, $database = null);

    /**
     * Gets the primary key of a table.
     *
     * @param string $table    The table name.
     * @param string $database The database name.
     *
     * @return \Fridge\DBAL\Schema\PrimaryKey|null The table primary key.
     */
    function getTablePrimaryKey($table, $database = null);

    /**
     * Gets the foreign keys of a table.
     *
     * @param string $table    The table name.
     * @param string $database The database name.
     *
     * @return array The table foreign keys.
     */
    function getTableForeignKeys($table, $database = null);

    /**
     * Gets the indexes of a table.
     *
     * @param string $table    The table name.
     * @param string $database The database name.
     *
     * @return array The table indexes.
     */
    function getTableIndexes($table, $database = null);

    /**
     * Creates a database.
     *
     * @param string $database The database name.
     */
    function createDatabase($database);

    /**
     * Creates a schema.
     *
     * @param \Fridge\DBAL\Schema\Schema $schema The schema.
     */
    function createSchema(Schema\Schema $schema);

    /**
     * Creates a sequence.
     *
     * @param \Fridge\DBAL\Schema\Sequence $sequence The sequence.
     */
    function createSequence(Schema\Sequence $sequence);

    /**
     * Creates a view.
     *
     * @param \Fridge\DBAL\Schema\View $view The view.
     */
    function createView(Schema\View $view);

    /**
     * Creates a table.
     *
     * @param \Fridge\DBAL\Schema\Table $table The table.
     */
    function createTable(Schema\Table $table);

    /**
     * Creates a constraint.
     *
     * @param \Fridge\DBAL\Schema\ConstraintInterface $constraint The constraint.
     * @param string                                  $table      The table name of the constraint.
     */
    function createConstraint(Schema\ConstraintInterface $constraint, $table);

    /**
     * Creates a primary key.
     *
     * @param \Fridge\DBAL\Schema\PrimaryKey $primaryKey The primary key.
     * @param string                         $table      The table name of the primary key.
     */
    function createPrimaryKey(Schema\PrimaryKey $primaryKey, $table);

    /**
     * Creates a foreign key.
     *
     * @param \Fridge\DBAL\Schema\ForeignKey $foreignKey The foreign key.
     * @param string                         $table      The table name of the foreign key.
     */
    function createForeignKey(Schema\ForeignKey $foreignKey, $table);

    /**
     * Creates an index.
     *
     * @param \Fridge\DBAL\Schema\Index $index The index.
     * @param string                    $table The table name of the index.
     */
    function createIndex(Schema\Index $index, $table);

    /**
     * Drops a database.
     *
     * @param string $database The database name.
     */
    function dropDatabase($database);

    /**
     * Drops a schema.
     *
     * @param \Fridge\DBAL\Schema\Schema $schema The schema.
     */
    function dropSchema(Schema\Schema $schema);

    /**
     * Drops a sequence.
     *
     * @param \Fridge\DBAL\Schema\Sequence $sequence The sequence.
     */
    function dropSequence(Schema\Sequence $sequence);

    /**
     * Drops a view.
     *
     * @param \Fridge\DBAL\Schema\View $view The view.
     */
    function dropView(Schema\View $view);

    /**
     * Drops a table.
     *
     * @param \Fridge\DBAL\Schema\Table $table The table.
     */
    function dropTable(Schema\Table $table);

    /**
     * Drops a constraint.
     *
     * @param \Fridge\DBAL\Schema\ConstraintInterface $constraint The constraint.
     * @param string                                  $table      The table name of the constraint.
     */
    function dropConstraint(Schema\ConstraintInterface $constraint, $table);

    /**
     * Drops a primary key.
     *
     * @param \Fridge\DBAL\Schema\PrimaryKey $primaryKey The primary key.
     * @param string                         $table      The table name of the primary key.
     */
    function dropPrimaryKey(Schema\PrimaryKey $primaryKey, $table);

    /**
     * Drops a foreign key.
     *
     * @param \Fridge\DBAL\Schema\ForeignKey $foreignKey The foreign key.
     * @param string                         $table      The table name of the foreign key.
     */
    function dropForeignKey(Schema\ForeignKey $foreignKey, $table);

    /**
     * Drops an index.
     *
     * @param \Fridge\DBAL\Schema\Index $index The index.
     * @param string                    $table The table name of the index.
     */
    function dropIndex(Schema\Index $index, $table);

    /**
     * Drops & creates a database.
     *
     * @param string $database The database name.
     */
    function dropAndCreateDatabase($database);

    /**
     * Drops & creates a schema.
     *
     * @param \Fridge\DBAL\Schema\Schema $schema The schema.
     */
    function dropAndCreateSchema(Schema\Schema $schema);

    /**
     * Drops & creates a sequence.
     *
     * @param \Fridge\DBAL\Schema\Sequence $sequence The sequence.
     */
    function dropAndCreateSequence(Schema\Sequence $sequence);

    /**
     * Drops & creates a view.
     *
     * @param \Fridge\DBAL\Schema\View $view The view.
     */
    function dropAndCreateView(Schema\View $view);

    /**
     * Drops & creates a table.
     *
     * @param \Fridge\DBAL\Schema\Table $table The table.
     */
    function dropAndCreateTable(Schema\Table $table);

    /**
     * Drops & creates a constraint.
     *
     * @param \Fridge\DBAL\Schema\ConstraintInterface $constraint The constraint.
     * @param string                                  $table      The table name of the constraint.
     */
    function dropAndCreateConstraint(Schema\ConstraintInterface $constraint, $table);

    /**
     * Drops & creates a primary key.
     *
     * @param \Fridge\DBAL\Schema\PrimaryKey $primaryKey The primary key.
     * @param string                         $table      The table name of the primary key.
     */
    function dropAndCreatePrimaryKey(Schema\PrimaryKey $primaryKey, $table);

    /**
     * Drops & creates a foreign key.
     *
     * @param \Fridge\DBAL\Schema\ForeignKey $foreignKey The foreign key.
     * @param string                         $table      The table name of the foreign key.
     */
    function dropAndCreateForeignKey(Schema\ForeignKey $foreignKey, $table);

    /**
     * Drops & creates an index.
     *
     * @param \Fridge\DBAL\Schema\Index $index The index.
     * @param string                    $table The table name of the index.
     */
    function dropAndCreateIndex(Schema\Index $index, $table);
}
