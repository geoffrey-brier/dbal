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

use Fridge\DBAL\Schema\Check,
    Fridge\DBAL\Schema\Column,
    Fridge\DBAL\Schema\ConstraintInterface,
    Fridge\DBAL\Schema\Diff\ColumnDiff,
    Fridge\DBAL\Schema\Diff\SchemaDiff,
    Fridge\DBAL\Schema\Diff\TableDiff,
    Fridge\DBAL\Schema\ForeignKey,
    Fridge\DBAL\Schema\Index,
    Fridge\DBAL\Schema\PrimaryKey,
    Fridge\DBAL\Schema\Sequence,
    Fridge\DBAL\Schema\Table,
    Fridge\DBAL\Schema\View;

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
     * Gets the blob SQL declaration.
     *
     * @param array $options The blob options.
     *
     * @return string The blob SQL declaration.
     */
    function getBlobSQLDeclaration(array $options);

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
     * Checks if the platform supports view.
     *
     * @return boolean TRUE if the platform supports view else FALSE.
     */
    function supportView();

    /**
     * Checks if the platform supports primary key.
     *
     * @return boolean TRUE if the platform supports primary key else FALSE.
     */
    function supportPrimaryKey();

    /**
     * Checks if the platform supports foreign key.
     *
     * @return boolean TRUE if the platform supports foreign key else FALSE.
     */
    function supportForeignKey();

    /**
     * Checks if the platform supports index.
     *
     * @return boolean TRUE if the platform supports index else FALSE.
     */
    function supportIndex();

    /**
     * Checks if the platform support check.
     *
     * @return boolean TRUE if the platform supports check else FALSE.
     */
    function supportCheck();

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
     * Gets the select query to fetch table check constraints.
     *
     * @param string $table    The table name.
     * @param string $database The database name.
     *
     * @return string The select query to fetch table check constraints.
     */
    function getSelectTableCheckSQLQuery($table, $database);

    /**
     * Gets the create database SQL queries.
     *
     * @param string $database The database name.
     *
     * @return array The create database SQL queries.
     */
    function getCreateDatabaseSQLQueries($database);

    /**
     * Gets the create sequence SQL queries.
     *
     * @param \Fridge\DBAL\Schema\Sequence $sequence The sequence.
     *
     * @return array The create sequence SQL queries.
     */
    function getCreateSequenceSQLQueries(Sequence $sequence);

    /**
     * Gets the create view SQL queries.
     *
     * @param \Fridge\DBAL\Schema\View $view The view.
     *
     * @return array The create view SQL queries.
     */
    function getCreateViewSQLQueries(View $view);

    /**
     * Gets the create table SQL queries.
     *
     * The $flags parameters can contain:
     *  - primary_key: TRUE if queries include primary key else FALSE (default: TRUE).
     *  - index: TRUE if queries include indexes else FALSE (default: TRUE).
     *  - foreign_key: TRUE if queries include foreingn keys else FALSE (default: TRUE).
     *  - check: TRUE if queries include checks else FALSE (default: TRUE).
     *
     * @param \Fridge\DBAL\Schema\Table $table The table.
     * @param array                     $flags The create table flags.
     *
     * @return array The create table SQL queries.
     */
    function getCreateTableSQLQueries(Table $table, array $flags = array());

    /**
     * Gets the create table column SQL queries.
     *
     * @param \Fridge\DBAL\Schema\Column $column The column.
     * @param string                     $table  The table name.
     *
     * @return array The create table column SQL queries.
     */
    function getCreateColumnSQLQueries(Column $column, $table);

    /**
     * Gets the create constraint SQL queries.
     *
     * @param \Fridge\DBAL\Schema\ConstraintInterface $constraint The constraint.
     * @param string                                  $table      The table name of the constraint.
     *
     * @return array The create constraint SQL queries.
     */
    function getCreateConstraintSQLQueries(ConstraintInterface $constraint, $table);

    /**
     * Gets the create primary key SQL queries.
     *
     * @param \Fridge\DBAL\Schema\PrimaryKey $primaryKey The primary key.
     * @param string                         $table      The table name of the primary key.
     *
     * @return array The create primary key SQL queries.
     */
    function getCreatePrimaryKeySQLQueries(PrimaryKey $primaryKey, $table);

    /**
     * Gets the create foreign key SQL queries.
     *
     * @param \Fridge\DBAL\Schema\ForeignKey $foreignKey The foreign key.
     * @param string                         $table      The table name of the foreign key.
     *
     * @return array The create foreign key SQL queries.
     */
    function getCreateForeignKeySQLQueries(ForeignKey $foreignKey, $table);

    /**
     * Gets the create index SQL queries.
     *
     * @param \Fridge\DBAL\Schema\Index $index The index.
     * @param string                    $table The table name of the index.
     *
     * @return array The create index SQL queries.
     */
    function getCreateIndexSQLQueries(Index $index, $table);

    /**
     * Gets the create check constraint SQL queries.
     *
     * @param \Fridge\DBAL\Schema\Check $check The check constraint.
     * @param string                    $table The table name of the check constraint.
     *
     * @return array The create check constraint SQL queries.
     */
    function getCreateCheckSQLQueries(Check $check, $table);

    /**
     * Gets the rename database SQL queries.
     *
     * @param \Fridge\DBAL\Schema\Diff\SchemaDiff $schemaDiff The schema diff.
     *
     * @return array The rename database SQL queries.
     */
    function getRenameDatabaseSQLQueries(SchemaDiff $schemaDiff);

    /**
     * Gets the rename table SQL queries.
     *
     * @param \Fridge\DBAL\Schema\Diff\TableDiff $tableDiff The table diff.
     *
     * @return array The rename table SQL quueries.
     */
    function getRenameTableSQLQueries(TableDiff $tableDiff);

    /**
     * Gets the alter table column SQL queries.
     *
     * @param \Fridge\DBAL\Schema\Diff\ColumnDiff $columnDiff The column diff.
     * @param string                              $table      The table name.
     *
     * @return array The alter table column SQL queries.
     */
    function getAlterColumnSQLQueries(ColumnDiff $columnDiff, $table);

    /**
     * Gets the drop database SQL queries.
     *
     * @param string $database The database name.
     *
     * @return array The drop database SQL queries.
     */
    function getDropDatabaseSQLQueries($database);

    /**
     * Gets the drop sequence SQL queries.
     *
     * @param \Fridge\DBAL\Schema\Sequence $sequence The sequence.
     *
     * @return array The drop sequence SQL queries.
     */
    function getDropSequenceSQLQueries(Sequence $sequence);

    /**
     * Gets the drop view SQL queries.
     *
     * @param \Fridge\DBAL\Schema\View $view The view.
     *
     * @return array The drop view SQL queries.
     */
    function getDropViewSQLQueries(View $view);

    /**
     * Gets the drop table SQL queries.
     *
     * @param \Fridge\DBAL\Schema\Table $table The table.
     *
     * @return array The drop table SQL queries.
     */
    function getDropTableSQLQueries(Table $table);

    /**
     * Gets the drop table column SQL queries.
     *
     * @param \Fridge\DBAL\Schema\Column $column The column.
     * @param string                     $table  The table name.
     *
     * @return array The drop table column SQL queries.
     */
    function getDropColumnSQLQueries(Column $column, $table);

    /**
     * Gets the drop constraint SQL queries.
     *
     * @param \Fridge\DBAL\Schema\ConstraintInterface $constraint The constraint.
     * @param string                                  $table      The table name of the constraint.
     *
     * @return array The drop constraint SQL queries.
     */
    function getDropConstraintSQLQueries(ConstraintInterface $constraint, $table);

    /**
     * Gets the drop primary key SQL queries.
     *
     * @param \Fridge\DBAL\Schema\PrimaryKey $primaryKey The primary key.
     * @param string                         $table      The table name of the primary key.
     *
     * @return array The drop primary key SQL queries.
     */
    function getDropPrimaryKeySQLQueries(PrimaryKey $primaryKey, $table);

    /**
     * Gets the drop foreign key SQL queries.
     *
     * @param \Fridge\DBAL\Schema\ForeignKey $foreignKey The foreign key.
     * @param string                         $table      The table name of the foreign key.
     *
     * @return array The drop foreign key SQL queries.
     */
    function getDropForeignKeySQLQueries(ForeignKey $foreignKey, $table);

    /**
     * Gets the drop index SQL queries.
     *
     * @param \Fridge\DBAL\Schema\Index $index The index.
     * @param string                    $table The table name of the index.
     *
     * @return array The drop index SQL queries.
     */
    function getDropIndexSQLQueries(Index $index, $table);

    /**
     * Gets the drop check constraint SQL queries.
     *
     * @param \Fridge\DBAL\Schema\Check $check The check.
     * @param string                    $table The table name of the check constraint.
     *
     * @return array The drop index SQL queries.
     */
    function getDropCheckSQLQueries(Check $check, $table);

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
