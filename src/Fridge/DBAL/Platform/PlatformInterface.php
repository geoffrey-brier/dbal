<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Platform;

use Fridge\DBAL\Schema;

/**
 * A platform allows to know each specific database behaviors.
 *
 * All platforms must implement this interface.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface PlatformInterface
{
    /**
     * Checks if a mapped type exists.
     *
     * @param string $type The type.
     *
     * @return boolean TRUE if the mapped type exists else FALSE.
     */
    function hasMappedType($type);

    /**
     * Gets a mapped type.
     *
     * @param string $type The type.
     *
     * @return string The mapped type.
     */
    function getMappedType($type);

    /**
     * Adds a mapped type.
     *
     * @param string $databaseType The database type.
     * @param string $fridgeType   The fridge type.
     */
    function addMappedType($databaseType, $fridgeType);

    /**
     * Overrides a mapped type.
     *
     * @param string $databaseType The database type.
     * @param string $fridgeType   The fridge type.
     */
    function overrideMappedType($databaseType, $fridgeType);

    /**
     * Removes a mapped type.
     *
     * @param string $type The mapped type to remove.
     */
    function removeMappedType($type);

    /**
     * Checks/sets if the platform uses a strict mapped type strategy.
     *
     * @param boolean $strictMappedType TRUE if the platform uses stric mapped type strategy else FALSE.
     *
     * @return boolean TRUE if the platform uses stric mapped type strategy else FALSE.
     */
    function useStrictMappedType($strictMappedType = null);

    /**
     * Gets the fallback mapped type.
     *
     * @return string The fallback mapped type.
     */
    function getFallbackMappedType();

    /**
     * Sets the fallback mapped type.
     *
     * @param string $fallbackMappedType The fallback mapped type.
     */
    function setFallbackMappedType($fallbackMappedType);

    /**
     * Checks if a mandatory type exists.
     *
     * @param string $type The type.
     *
     * @return boolean TRUE if the mandatory type exists else FALSE.
     */
    function hasMandatoryType($type);

    /**
     * Adds a mandatory type.
     *
     * @param string $type The type.
     */
    function addMandatoryType($type);

    /**
     * Removes a mandatory type.
     *
     * @param string $type The type.
     */
    function removeMandatoryType($type);

    /**
     * Gets the big integer SQL declaration.
     *
     * @param array $options The big integer options.
     *
     * @return string The big integer SQL declaration.
     */
    function getBigIntegerSQLDeclaration(array $options);

    /**
     * Gets the boolean SQL declaration.
     *
     * @param array $options The boolean options.
     *
     * @return string The boolean SQL declaration.
     */
    function getBooleanSQLDeclaration(array $options);

    /**
     * Gets the clob SQL declaration.
     *
     * @param array $options The clob options.
     *
     * @return string The clob SQL declaration.
     */
    function getClobSQLDeclaration(array $options);

    /**
     * Gets the date SQL declaration.
     *
     * @param array $options The date options.
     *
     * @return string The date SQL declaration.
     */
    function getDateSQLDeclaration(array $options);

    /**
     * Gets the date time SQL declaration.
     *
     * @param array $options The date time options.
     *
     * @return string The date time SQL declaration.
     */
    function getDateTimeSQLDeclaration(array $options);

    /**
     * Gets the decimal SQL declaration.
     *
     * @param array $options The decimal options.
     *
     * @return string The decimal SQL declaration.
     */
    function getDecimalSQLDeclaration(array $options);

    /**
     * Gets the float SQL declaration.
     *
     * @param array $options The float options.
     *
     * @return string The float SQL declaration.
     */
    function getFloatSQLDeclaration(array $options);

    /**
     * Gets the integer SQL declaration.
     *
     * @param array $options The integer options.
     *
     * @return string The integer SQL declaration.
     */
    function getIntegerSQLDeclaration(array $options);

    /**
     * Gets the small integer SQL declaration.
     *
     * @param array $options The small integer options.
     *
     * @return string The small integer SQL declaration.
     */
    function getSmallIntegerSQLDeclaration(array $options);

    /**
     * Gets the time SQL declaration.
     *
     * @param array $options The time options.
     *
     * @return string The time SQL declaration.
     */
    function getTimeSQLDeclaration(array $options);

    /**
     * Gets the varchar SQL declaration.
     *
     * @param array $options The varchar options.
     *
     * @return string The varchar SQL declaration.
     */
    function getVarcharSQLDeclaration(array $options);

    /**
     * Gets the default decimal precision.
     *
     * @return integer The default decimal precision.
     */
    function getDefaultDecimalPrecision();

    /**
     * Gets the default decimal scale.
     *
     * @return integer The default decimal scale.
     */
    function getDefaultDecimalScale();

    /**
     * Gets the default varchar length.
     *
     * @return integer The default varchar length.
     */
    function getDefaultVarcharLength();

    /**
     * Gets the default platform transaction isolation.
     *
     * @return string The default platform transaction isolation.
     */
    function getDefaultTransactionIsolation();

    /**
     * Gets the max varchar length.
     *
     * @return integer The max varchar length.
     */
    function getMaxVarcharLength();

    /**
     * Gets the date format.
     *
     * @return string The date format.
     */
    function getDateFormat();

    /**
     * Gets the time format.
     *
     * @return string The time format.
     */
    function getTimeFormat();

    /**
     * Gets the date time format.
     *
     * @return string The date time format.
     */
    function getDateTimeFormat();

    /**
     * Checks if the platform supports savepoint.
     *
     * @return boolean TRUE if the platform supports savepoint else FALSE.
     */
    function supportSavepoint();

    /**
     * Checks if the platform supports transaction isolation.
     *
     * @return boolean TRUE if the platform supports transaction isolation else FALSE.
     */
    function supportTransactionIsolation();

    /**
     * Checks if the platform supports sequence.
     *
     * @return boolean TRUE if the platform supports sequence else FALSE
     */
    function supportSequence();

    /**
     * Checks if the platform supports inline table column comment.
     *
     * @return boolean TRUE if the platform supports inline table column comment else FALSE.
     */
    function supportInlineTableColumnComment();

    /**
     * Gets the set charset SQL query.
     *
     * @param string $charset The charset.
     *
     * @return string The set charset SQL query.
     */
    function getSetCharsetSQLQuery($charset);

    /**
     * Gets the create savepoint SQL query.
     *
     * @param string $savepoint The savepoint name.
     *
     * @return string The create savepoint SQL query.
     */
    function getCreateSavepointSQLQuery($savepoint);

    /**
     * Gets the release savepoint SQL query.
     *
     * @param string $savepoint The savepoint name.
     *
     * @return string The release savepoint SQL query.
     */
    function getReleaseSavepointSQLQuery($savepoint);

    /**
     * Gets the rollback savepoint SQL query.
     *
     * @param string $savepoint The savepoint name.
     *
     * @return string The rollback savepoint SQL query.
     */
    function getRollbackSavepointSQLQuery($savepoint);

    /**
     * Gets the set transaction isolation SQL query.
     *
     * @param string $isolation The transaction isolation.
     *
     * @return string The set transaction isolation SQL query.
     */
    function getSetTransactionIsolationSQLQuery($isolation);

    /**
     * Gets the select query to fetch the current database.
     *
     * @return string The select query to fetch the current database.
     */
    function getSelectDatabaseSQLQuery();

    /**
     * Gets the select query to fetch databases.
     *
     * @return string The select query to fetch databases.
     */
    function getSelectDatabasesSQLQuery();

    /**
     * Gets the select query to fetch sequences.
     *
     * @param string $database The database name.
     *
     * @return string The select query to fetch sequences.
     */
    function getSelectSequencesSQLQuery($database);

    /**
     * Gets the select views to fetch views.
     *
     * @param string $database The database name.
     *
     * @return string The select query to fetch views.
     */
    function getSelectViewsSQLQuery($database);

    /**
     * Gets the select query to fetch table names.
     *
     * @param string $database The database name.
     *
     * @return string The select query to fetch table names.
     */
    function getSelectTableNamesSQLQuery($database);

    /**
     * Gets the select query to fetch table columns.
     *
     * @param string $table    The table name.
     * @param string $database The database name.
     *
     * @return string The select query to fetch table columns.
     */
    function getSelectTableColumnsSQLQuery($table, $database);

    /**
     * Gets the select query to fetch table primary key.
     *
     * @param string $table    The table name.
     * @param string $database The database name.
     *
     * @return string The select query to fetch table primary key.
     */
    function getSelectTablePrimaryKeySQLQuery($table, $database);

    /**
     * Gets the select query to fetch table foreign keys.
     *
     * @param string $table    The table name.
     * @param string $database The database name.
     *
     * @return string The select query to fetch table foreign keys.
     */
    function getSelectTableForeignKeysSQLQuery($table, $database);

    /**
     * Gets the select query to fetch table indexes.
     *
     * @param string $table    The table name.
     * @param string $database The database name.
     *
     * @return string The select query to fetch table indexes.
     */
    function getSelectTableIndexesSQLQuery($table, $database);

    /**
     * Gets the create database SQL query.
     *
     * @param string $database The database name.
     *
     * @return string The create database SQL query.
     */
    function getCreateDatabaseSQLQuery($database);

    /**
     * Gets the create sequence SQL query.
     *
     * @param \Fridge\DBAL\Schema\Sequence $sequence The sequence.
     *
     * @return string The create sequence SQL query.
     */
    function getCreateSequenceSQLQuery(Schema\Sequence $sequence);

    /**
     * Gets the create view SQL query.
     *
     * @param \Fridge\DBAL\Schema\View $view The view.
     *
     * @return string The create view SQL query.
     */
    function getCreateViewSQLQuery(Schema\View $view);

    /**
     * Gets the create table SQL queries.
     *
     * The $flash parameters can contain:
     *  - primary_key: TRUE if queries include primary key else FALSE (default: TRUE).
     *  - index: TRUE if queries include indexes else FALSE (default: TRUE).
     *  - foreign_key: TRUE if queries include foreingn keys else FALSE (default: TRUE).
     *
     * @param \Fridge\DBAL\Schema\Table $table The table.
     * @param array                     $flags The create table flags.
     *
     * @return array The create table SQL queries.
     */
    function getCreateTableSQLQueries(Schema\Table $table, array $flags = array());

    /**
     * Gets the create table column SQL queries.
     *
     * @param \Fridge\DBAL\Schema\Column $column The column.
     * @param string                     $table  The table name.
     *
     * @return array The create table column SQL queries.
     */
    function getCreateColumnSQLQueries(Schema\Column $column, $table);

    /**
     * Gets the create constraint SQL query.
     *
     * @param \Fridge\DBAL\Schema\ConstraintInterface $constraint The constraint.
     * @param string                                  $table      The table name of the constraint.
     *
     * @return string The create constraint SQL query.
     */
    function getCreateConstraintSQLQuery(Schema\ConstraintInterface $constraint, $table);

    /**
     * Gets the create primary key SQL query.
     *
     * @param \Fridge\DBAL\Schema\PrimaryKey $primaryKey The primary key.
     * @param string                         $table      The table name of the primary key.
     *
     * @return string The create primary key SQL query.
     */
    function getCreatePrimaryKeySQLQuery(Schema\PrimaryKey $primaryKey, $table);

    /**
     * Gets the create foreign key SQL query.
     *
     * @param \Fridge\DBAL\Schema\ForeignKey $foreignKey The foreign key.
     * @param string                         $table      The table name of the foreign key.
     *
     * @return string The create foreign key SQL query.
     */
    function getCreateForeignKeySQLQuery(Schema\ForeignKey $foreignKey, $table);

    /**
     * Gets the create index SQL query.
     *
     * @param \Fridge\DBAL\Schema\Index $index The index.
     * @param string                    $table The table name of the index.
     *
     * @return string The create index SQL query.
     */
    function getCreateIndexSQLQuery(Schema\Index $index, $table);

    /**
     * Gets the rename database SQL query.
     *
     * @param \Fridge\DBAL\Schema\Diff\SchemaDiff $schemaDiff The schema diff.
     *
     * @return string The rename database SQL query.
     */
    function getRenameDatabaseSQLQuery(Schema\Diff\SchemaDiff $schemaDiff);

    /**
     * Gets the rename table SQL query.
     *
     * @param \Fridge\DBAL\Schema\Diff\TableDiff $tableDiff The table diff.
     *
     * @return string The rename table SQL quuery.
     */
    function getRenameTableSQLQuery(Schema\Diff\TableDiff $tableDiff);

    /**
     * Gets the rename table column SQL query.
     *
     * @param \Fridge\DBAL\Schema\Diff\ColumnDiff $columnDiff The column diff.
     * @param string                              $table      The table name.
     *
     * @return string The rename table column SQL query.
     */
    function getRenameColumnSQLQueries(Schema\Diff\ColumnDiff $columnDiff, $table);

    /**
     * Gets the drop database SQL query.
     *
     * @param string $database The database name.
     *
     * @return string The drop database SQL query.
     */
    function getDropDatabaseSQLQuery($database);

    /**
     * Gets the drop sequence SQL query.
     *
     * @param \Fridge\DBAL\Schema\Sequence $sequence The sequence.
     *
     * @return string The drop sequence SQL query.
     */
    function getDropSequenceSQLQuery(Schema\Sequence $sequence);

    /**
     * Gets the drop view SQL query.
     *
     * @param \Fridge\DBAL\Schema\View $view The view.
     *
     * @return string The drop view SQL query.
     */
    function getDropViewSQLQuery(Schema\View $view);

    /**
     * Gets the drop table SQL query.
     *
     * @param \Fridge\DBAL\Schema\Table $table The table.
     *
     * @return string The drop table SQL query.
     */
    function getDropTableSQLQuery(Schema\Table $table);

    /**
     * Gets the drop table column SQL query.
     *
     * @param \Fridge\DBAL\Schema\Column $column The column.
     * @param string                     $table  The table name.
     */
    function getDropColumnSQLQuery(Schema\Column $column, $table);

    /**
     * Gets the drop constraint SQL query.
     *
     * @param \Fridge\DBAL\Schema\ConstraintInterface $constraint The constraint.
     * @param string                                  $table      The table name of the constraint.
     *
     * @return string The drop constraint SQL query.
     */
    function getDropConstraintSQLQuery(Schema\ConstraintInterface $constraint, $table);

    /**
     * Gets the drop primary key SQL query.
     *
     * @param \Fridge\DBAL\Schema\PrimaryKey $primaryKey The primary key.
     * @param string                         $table      The table name of the primary key.
     *
     * @return string The drop primary key SQL query.
     */
    function getDropPrimaryKeySQLQuery(Schema\PrimaryKey $primaryKey, $table);

    /**
     * Gets the drop foreign key SQL query.
     *
     * @param \Fridge\DBAL\Schema\ForeignKey $foreignKey The foreign key.
     * @param string                         $table      The table name of the foreign key.
     *
     * @return string The drop foreign key SQL query.
     */
    function getDropForeignKeySQLQuery(Schema\ForeignKey $foreignKey, $table);

    /**
     * Gets the drop index SQL query.
     *
     * @param \Fridge\DBAL\Schema\Index $index The index.
     * @param string                    $table The table name of the index.
     *
     * @return string The drop index SQL query.
     */
    function getDropIndexSQLQuery(Schema\Index $index, $table);

    /**
     * Gets the quote identifier.
     *
     * @return string The quote identifier
     */
    function getQuoteIdentifier();

    /**
     * Quotes identifiers.
     *
     * @param array $identifiers The identifiers.
     *
     * @return array The quoted identifiers.
     */
    function quoteIdentifiers(array $identifiers);

    /**
     * Quotes an identifier.
     *
     * @param string $identifier The identifier.
     *
     * @return string The quoted identifier.
     */
    function quoteIdentifier($identifier);
}
