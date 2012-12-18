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

use Fridge\DBAL\Connection\Connection,
    Fridge\DBAL\Exception\PlatformException,
    Fridge\DBAL\Exception\TypeException,
    Fridge\DBAL\Schema\Check,
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
    Fridge\DBAL\Schema\View,
    Fridge\DBAL\Type\Type;

/**
 * {@inheritdoc}
 *
 * All platforms must extend this class.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractPlatform implements PlatformInterface
{
    /** @var array */
    protected $mappedTypes;

    /** @var boolean */
    protected $strictMappedType;

    /** @var string */
    protected $fallbackMappedType;

    /** @var array */
    protected $mandatoryTypes;

    /**
     * Platform constructor.
     */
    public function __construct()
    {
        $this->initializeMappedTypes();

        $this->strictMappedType = true;
        $this->fallbackMappedType = Type::TEXT;

        $this->initializeMandatoryTypes();
    }

    /**
     * {@inheritdoc}
     */
    public function hasMappedType($type)
    {
        return isset($this->mappedTypes[$type]);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If te mapped type does not exist.
     */
    public function getMappedType($type)
    {
        if ($this->hasMappedType($type)) {
            return $this->mappedTypes[$type];
        }

        if ($this->strictMappedType) {
            throw PlatformException::mappedTypeDoesNotExist($type);
        }

        return $this->fallbackMappedType;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the mapped type already exists.
     * @throws \Fridge\DBAL\Exception\TypeException     If the type does not exist.
     */
    public function addMappedType($databaseType, $fridgeType)
    {
        if ($this->hasMappedType($databaseType)) {
            throw PlatformException::mappedTypeAlreadyExists($databaseType);
        }

        if (!Type::hasType($fridgeType)) {
            throw TypeException::typeDoesNotExist($fridgeType);
        }

        $this->mappedTypes[$databaseType] = $fridgeType;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the mapped type does not exist.
     * @throws \Fridge\DBAL\Exception\TypeException     If the type does not exist.
     */
    public function overrideMappedType($databaseType, $fridgeType)
    {
        if (!$this->hasMappedType($databaseType)) {
            throw PlatformException::mappedTypeDoesNotExist($databaseType);
        }

        if (!Type::hasType($fridgeType)) {
            throw TypeException::typeDoesNotExist($fridgeType);
        }

        $this->mappedTypes[$databaseType] = $fridgeType;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the mapped type does not exist.
     */
    public function removeMappedType($type)
    {
        if (!$this->hasMappedType($type)) {
            throw PlatformException::mappedTypeDoesNotExist($type);
        }

        unset($this->mappedTypes[$type]);
    }

    /**
     * {@inheritdoc}
     */
    public function useStrictMappedType($strictMappedType = null)
    {
        if ($strictMappedType !== null) {
            $this->strictMappedType = (bool) $strictMappedType;
        }

        return $this->strictMappedType;
    }

    /**
     * {@inheritdoc}
     */
    public function getFallbackMappedType()
    {
        return $this->fallbackMappedType;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\TypeException If the type does not exist.
     */
    public function setFallbackMappedType($fallbackMappedType)
    {
        if (!Type::hasType($fallbackMappedType)) {
            throw TypeException::typeDoesNotExist($fallbackMappedType);
        }

        $this->fallbackMappedType = $fallbackMappedType;
    }

    /**
     * {@inheritdoc}
     */
    public function hasMandatoryType($type)
    {
        return in_array($type, $this->mandatoryTypes);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the mandatory type already exists.
     * @throws \Fridge\DBAL\Exception\TypeException     If the type does not exist.
     */
    public function addMandatoryType($type)
    {
        if ($this->hasMandatoryType($type)) {
            throw PlatformException::mandatoryTypeAlreadyExists($type);
        }

        if (!Type::hasType($type)) {
            throw TypeException::typeDoesNotExist($type);
        }

        $this->mandatoryTypes[] = $type;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the mandatory type does not exist.
     */
    public function removeMandatoryType($type)
    {
        if (!$this->hasMandatoryType($type)) {
            throw PlatformException::mandatoryTypeDoesNotExist($type);
        }

        $index = array_search($type, $this->mandatoryTypes);
        unset($this->mandatoryTypes[$index]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBigIntegerSQLDeclaration(array $options = array())
    {
        return 'BIGINT';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlobSQLDeclaration(array $options = array())
    {
        return 'BLOB';
    }

    /**
     * {@inheritdoc}
     */
    public function getBooleanSQLDeclaration(array $options = array())
    {
        return 'BOOLEAN';
    }

    /**
     * {@inheritdoc}
     */
    public function getClobSQLDeclaration(array $options = array())
    {
        return 'TEXT';
    }

    /**
     * {@inheritdoc}
     */
    public function getDateSQLDeclaration(array $options = array())
    {
        return 'DATE';
    }

    /**
     * {@inheritdoc}
     */
    public function getDateTimeSQLDeclaration(array $options = array())
    {
        return 'DATETIME';
    }

    /**
     * {@inheritdoc}
     */
    public function getDecimalSQLDeclaration(array $options = array())
    {
        if (!isset($options['precision'])) {
            $options['precision'] = $this->getDefaultDecimalPrecision();
        }

        if (!isset($options['scale'])) {
            $options['scale'] = $this->getDefaultDecimalScale();
        }

        return 'NUMERIC('.$options['precision'].', '.$options['scale'].')';
    }

    /**
     * {@inheritdoc}
     */
    public function getFloatSQLDeclaration(array $options = array())
    {
        return 'DOUBLE PRECISION';
    }

    /**
     * {@inheritdoc}
     */
    public function getIntegerSQLDeclaration(array $options = array())
    {
        return 'INT';
    }

    /**
     * {@inheritdoc}
     */
    public function getSmallIntegerSQLDeclaration(array $options = array())
    {
        return 'SMALLINT';
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeSQLDeclaration(array $options = array())
    {
        return 'TIME';
    }

    /**
     * {@inheritdoc}
     */
    public function getVarcharSQLDeclaration(array $options)
    {
        if (!isset($options['length'])) {
            $options['length'] = $this->getDefaultVarcharLength();
        }

        if ($options['length'] > $this->getMaxVarcharLength()) {
            return $this->getClobSQLDeclaration($options);
        }

        $fixed = isset($options['fixed']) ? $options['fixed'] : false;

        return $this->getVarcharSQLDeclarationSnippet($options['length'], $fixed);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultDecimalPrecision()
    {
        return 5;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultDecimalScale()
    {
        return 2;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultVarcharLength()
    {
        return 255;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultTransactionIsolation()
    {
        return Connection::TRANSACTION_READ_COMMITTED;
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxVarcharLength()
    {
        return 65535;
    }

    /**
     * {@inheritdoc}
     */
    public function getDateFormat()
    {
        return 'Y-m-d';
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeFormat()
    {
        return 'H:i:s';
    }

    /**
     * {@inheritdoc}
     */
    public function getDateTimeFormat()
    {
        return 'Y-m-d H:i:s';
    }

    /**
     * {@inheritdoc}
     */
    public function supportSavepoint()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supportTransactionIsolation()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supportSequence()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supportView()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supportPrimaryKey()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supportForeignKey()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supportIndex()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supportCheck()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supportInlineTableColumnComment()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the platform does not support savepoint.
     */
    public function getCreateSavepointSQLQuery($savepoint)
    {
        if (!$this->supportSavepoint()) {
            throw PlatformException::methodNotSupported(__METHOD__);
        }

        return 'SAVEPOINT '.$savepoint;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the platform does not support savepoint.
     */
    public function getReleaseSavepointSQLQuery($savepoint)
    {
        if (!$this->supportSavepoint()) {
            throw PlatformException::methodNotSupported(__METHOD__);
        }

        return 'RELEASE SAVEPOINT '.$savepoint;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the platform does not support savepoint.
     */
    public function getRollbackSavepointSQLQuery($savepoint)
    {
        if (!$this->supportSavepoint()) {
            throw PlatformException::methodNotSupported(__METHOD__);
        }

        return 'ROLLBACK TO SAVEPOINT '.$savepoint;
    }

    /**
     * {@inheritdoc}
     */
    public function getSetCharsetSQLQuery($charset)
    {
        return 'SET NAMES '.$this->quote($charset);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the platform does not allow to select current database name.
     */
    public function getSelectDatabaseSQLQuery()
    {
        throw PlatformException::methodNotSupported(__METHOD__);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the platform does not allow to select databases names.
     */
    public function getSelectDatabasesSQLQuery()
    {
        throw PlatformException::methodNotSupported(__METHOD__);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the platform does not allow to select sequences.
     */
    public function getSelectSequencesSQLQuery($database)
    {
        throw PlatformException::methodNotSupported(__METHOD__);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the platform does not allow to select views.
     */
    public function getSelectViewsSQLQuery($database)
    {
        throw PlatformException::methodNotSupported(__METHOD__);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the platform does not allow to select table names.
     */
    public function getSelectTableNamesSQLQuery($database)
    {
        throw PlatformException::methodNotSupported(__METHOD__);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the platform does not allow to select table columns.
     */
    public function getSelectTableColumnsSQLQuery($table, $database)
    {
        throw PlatformException::methodNotSupported(__METHOD__);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the platform does not allow to select table primary key.
     */
    public function getSelectTablePrimaryKeySQLQuery($table, $database)
    {
        throw PlatformException::methodNotSupported(__METHOD__);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the platform does not allow to select table foreign keys.
     */
    public function getSelectTableForeignKeysSQLQuery($table, $database)
    {
        throw PlatformException::methodNotSupported(__METHOD__);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the platform does not allow to select table indexes.
     */
    public function getSelectTableIndexesSQLQuery($table, $database)
    {
        throw PlatformException::methodNotSupported(__METHOD__);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the platform does not allow to select table checks.
     */
    public function getSelectTableCheckSQLQuery($table, $database)
    {
        throw PlatformException::methodNotSupported(__METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreateDatabaseSQLQueries($database)
    {
        return array('CREATE DATABASE '.$database);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the platform does not support sequence.
     */
    public function getCreateSequenceSQLQueries(Sequence $sequence)
    {
        if (!$this->supportSequence()) {
            throw PlatformException::methodNotSupported(__METHOD__);
        }

        return array(
            'CREATE SEQUENCE '.$sequence->getName().
            ' INCREMENT BY '.$sequence->getIncrementSize().
            ' MINVALUE '.$sequence->getInitialValue().
            ' START WITH '.$sequence->getInitialValue()
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the platform does not support view.
     */
    public function getCreateViewSQLQueries(View $view)
    {
        if (!$this->supportView()) {
            throw PlatformException::methodNotSupported(__METHOD__);
        }

        return array('CREATE VIEW '.$view->getName().' AS '.$view->getSQL());
    }

    /**
     * {@inheritdoc}
     */
    public function getCreateTableSQLQueries(Table $table, array $flags = array())
    {
        $queries = array();

        if (!$this->supportInlineTableColumnComment()) {
            $queries = $this->getCreateColumnCommentsSQLQueries($table->getColumns(), $table->getName());
        }

        $query = 'CREATE TABLE '.$table->getName().' ('.$this->getColumnsSQLDeclaration($table->getColumns());

        if ($table->hasPrimaryKey() && (!isset($flags['primary_key']) || $flags['primary_key'])) {
            $query .= ', '.$this->getPrimaryKeySQLDeclaration($table->getPrimaryKey());
        }

        if (!isset($flags['index']) || $flags['index']) {
            foreach ($table->getFilteredIndexes() as $index) {
                $query .= ', '.$this->getIndexSQLDeclaration($index);
            }
        }

        if (!isset($flags['foreign_key']) || $flags['foreign_key']) {
            foreach ($table->getForeignKeys() as $foreignKey) {
                $query .= ', '.$this->getForeignKeySQLDeclaration($foreignKey);
            }
        }

        if (!isset($flags['check']) || $flags['check']) {
            foreach ($table->getChecks() as $check) {
                $query .= ', '.$this->getCheckSQLDeclaration($check);
            }
        }

        $query .= ')';

        array_unshift($queries, $query);

        return $queries;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreateColumnSQLQueries(Column $column, $table)
    {
        $queries = array('ALTER TABLE '.$table.' ADD COLUMN '.$this->getColumnSQLDeclaration($column));

        if (!$this->supportInlineTableColumnComment()
            && ($this->hasMandatoryType($column->getType()->getName())
            || ($column->getComment() !== null))) {
            $queries[] = $this->getCreateColumnCommentSQLQuery($column, $table);
        }

        return $queries;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the platform does not support the constraint.
     */
    public function getCreateConstraintSQLQueries(ConstraintInterface $constraint, $table)
    {
        if ($constraint instanceof PrimaryKey) {
            return $this->getCreatePrimaryKeySQLQueries($constraint, $table);
        }

        if ($constraint instanceof ForeignKey) {
            return $this->getCreateForeignKeySQLQueries($constraint, $table);
        }

        if ($constraint instanceof Index) {
            return $this->getCreateIndexSQLQueries($constraint, $table);
        }

        if ($constraint instanceof Check) {
            return $this->getCreateCheckSQLQueries($constraint, $table);
        }

        throw PlatformException::constraintNotSupported(get_class($constraint));
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatePrimaryKeySQLQueries(PrimaryKey $primaryKey, $table)
    {
        return array('ALTER TABLE '.$table.' ADD '.$this->getPrimaryKeySQLDeclaration($primaryKey));
    }

    /**
     * {@inheritdoc}
     */
    public function getCreateForeignKeySQLQueries(ForeignKey $foreignKey, $table)
    {
        return array('ALTER TABLE '.$table.' ADD '.$this->getForeignKeySQLDeclaration($foreignKey));
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the platform does not support index.
     */
    public function getCreateIndexSQLQueries(Index $index, $table)
    {
        if ($index->isUnique()) {
            return array('ALTER TABLE '.$table.' ADD '.$this->getIndexSQLDeclaration($index));
        }

        if (!$this->supportIndex()) {
            throw PlatformException::methodNotSupported(__METHOD__);
        }

        return array(
            'CREATE INDEX '.$index->getName().
            ' ON '.$table.
            ' ('.implode(', ', $index->getColumnNames()).')'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getCreateCheckSQLQueries(Check $check, $table)
    {
        return array('ALTER TABLE '.$table.' ADD '.$this->getCheckSQLDeclaration($check));
    }

    /**
     * {@inheritdoc}
     */
    public function getRenameDatabaseSQLQueries(SchemaDiff $schemaDiff)
    {
        return array(
            'ALTER DATABASE '.$schemaDiff->getOldAsset()->getName().
            ' RENAME TO '.$schemaDiff->getNewAsset()->getName()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getRenameTableSQLQueries(TableDiff $tableDiff)
    {
        return array(
            'ALTER TABLE '.$tableDiff->getOldAsset()->getName().
            ' RENAME TO '.$tableDiff->getNewAsset()->getName()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAlterColumnSQLQueries(ColumnDiff $columnDiff, $table)
    {
        $queries = array(
            'ALTER TABLE '.$table.' ALTER COLUMN '.$columnDiff->getOldAsset()->getName().' '.
            $this->getColumnSQLDeclaration($columnDiff->getNewAsset())
        );

        if (!$this->supportInlineTableColumnComment()
            && ($this->hasMandatoryType($columnDiff->getNewAsset()->getType()->getName())
            || ($columnDiff->getNewAsset()->getComment() !== null))) {
            $queries[] = $this->getCreateColumnCommentSQLQuery($columnDiff->getNewAsset(), $table);
        }

        return $queries;
    }

    /**
     * {@inheritdoc}
     */
    public function getDropDatabaseSQLQueries($database)
    {
        return array('DROP DATABASE '.$database);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the platform does not support sequence.
     */
    public function getDropSequenceSQLQueries(Sequence $sequence)
    {
        if (!$this->supportSequence()) {
            throw PlatformException::methodNotSupported(__METHOD__);
        }

        return array('DROP SEQUENCE '.$sequence->getName());
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the platform does not support view.
     */
    public function getDropViewSQLQueries(View $view)
    {
        if (!$this->supportView()) {
            throw PlatformException::methodNotSupported(__METHOD__);
        }

        return array('DROP VIEW '.$view->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function getDropTableSQLQueries(Table $table)
    {
        return array('DROP TABLE '.$table->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function getDropColumnSQLQueries(Column $column, $table)
    {
        return array('ALTER TABLE '.$table.' DROP COLUMN '.$column->getName());
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the platform does not support the constraint.
     */
    public function getDropConstraintSQLQueries(ConstraintInterface $contraint, $table)
    {
        if ($contraint instanceof PrimaryKey) {
            return $this->getDropPrimaryKeySQLQueries($contraint, $table);
        }

        if ($contraint instanceof ForeignKey) {
            return $this->getDropForeignKeySQLQueries($contraint, $table);
        }

        if ($contraint instanceof Index) {
            return $this->getDropIndexSQLQueries($contraint, $table);
        }

        if ($contraint instanceof Check) {
            return $this->getDropCheckSQLQueries($contraint, $table);
        }

        throw PlatformException::constraintNotSupported(get_class($contraint));
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the platform does not support primary key.
     */
    public function getDropPrimaryKeySQLQueries(PrimaryKey $primaryKey, $table)
    {
        if (!$this->supportPrimaryKey()) {
            throw PlatformException::methodNotSupported(__METHOD__);
        }

        return array('ALTER TABLE '.$table.' DROP CONSTRAINT '.$primaryKey->getName());
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the platform does not support foreign key.
     */
    public function getDropForeignKeySQLQueries(ForeignKey $foreignKey, $table)
    {
        if (!$this->supportForeignKey()) {
            throw PlatformException::methodNotSupported(__METHOD__);
        }

        return array('ALTER TABLE '.$table.' DROP CONSTRAINT '.$foreignKey->getName());
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the platform does not support index.
     */
    public function getDropIndexSQLQueries(Index $index, $table)
    {
        if (!$this->supportIndex()) {
            throw PlatformException::methodNotSupported(__METHOD__);
        }

        if ($index->isUnique()) {
            return array('ALTER TABLE '.$table.' DROP CONSTRAINT '.$index->getName());
        }

        return array('DROP INDEX '.$index->getName());
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the platform does not support check.
     */
    public function getDropCheckSQLQueries(Check $check, $table)
    {
        if (!$this->supportCheck()) {
            throw PlatformException::methodNotSupported(__METHOD__);
        }

        return array('ALTER TABLE '.$table.' DROP CONSTRAINT '.$check->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function getQuoteIdentifier()
    {
        return '"';
    }

    /**
     * {@inheritdoc}
     */
    public function quoteIdentifiers(array $identifiers)
    {
        foreach ($identifiers as $key => $identifier) {
            $identifiers[$key] = $this->quoteIdentifier($identifier);
        }

        return $identifiers;
    }

    /**
     * {@inheritdoc}
     */
    public function quoteIdentifier($identifier)
    {
        return $this->getQuoteIdentifier().$identifier.$this->getQuoteIdentifier();
    }

    /**
     * Initializes the mapped types.
     */
    abstract protected function initializeMappedTypes();

    /**
     * Initializes the mandatory types.
     */
    protected function initializeMandatoryTypes()
    {
        $this->mandatoryTypes = array(Type::TARRAY, Type::OBJECT);
    }

    /**
     * Gets the varchar SQL declaration snippet.
     *
     * @param integer $length The varchar length.
     * @param boolean $fixed  TRUE if the varchar is fixed else FALSE.
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the length is not a positive integer or if the fixed flag
     *                                                  is not a boolean.
     *
     * @return string The varchar SQL declaration snippet.
     */
    protected function getVarcharSQLDeclarationSnippet($length, $fixed)
    {
        if (!is_int($length) || (is_int($length) && ($length <= 0))) {
            throw PlatformException::invalidVarcharLength();
        }

        if (!is_bool($fixed)) {
            throw PlatformException::invalidVarcharFixedFlag();
        }

        if ($fixed) {
            return 'CHAR('.$length.')';
        }

        return 'VARCHAR('.$length.')';
    }

    /**
     * Gets the transaction isolation SQL declaration.
     *
     * @param string $isolation The transaction isolation.
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the isolation does not exist.
     *
     * @return string The transaction isolation SQL declaration.
     */
    protected function getTransactionIsolationSQLDeclaration($isolation)
    {
        $availableIsolations = array(
            Connection::TRANSACTION_READ_COMMITTED,
            Connection::TRANSACTION_READ_UNCOMMITTED,
            Connection::TRANSACTION_REPEATABLE_READ,
            Connection::TRANSACTION_SERIALIZABLE,
        );

        if (!in_array($isolation, $availableIsolations)) {
            throw PlatformException::transactionIsolationDoesNotExist($isolation);
        }

        return $isolation;
    }

    /**
     * Gets the columns SQL declaration.
     *
     * @param array $columns The columns.
     *
     * @return string The columns SQL declaration.
     */
    protected function getColumnsSQLDeclaration(array $columns)
    {
        $columnsDeclaration = array();

        foreach ($columns as $column) {
            $columnsDeclaration[] = $this->getColumnSQLDeclaration($column);
        }

        return implode(', ', $columnsDeclaration);
    }

    /**
     * Gets the column SQL declaration.
     *
     * @param \Fridge\DBAL\Schema\Column $column The column.
     *
     * @return string The column SQL declaration.
     */
    protected function getColumnSQLDeclaration(Column $column)
    {
        $columnDeclaration = $column->getName().' '.$column->getType()->getSQLDeclaration($this, $column->toArray());

        if ($column->isNotNull()) {
            $columnDeclaration .= ' NOT NULL';
        }

        if ($column->getDefault() !== null) {
            $default = $column->getType()->convertToDatabaseValue($column->getDefault(), $this);
            $columnDeclaration .= ' DEFAULT '.$this->quote($default);
        }

        if ($this->supportInlineTableColumnComment()
            && ($this->hasMandatoryType($column->getType()->getName())
            || ($column->getComment() !== null))
        ) {
            $columnDeclaration .= ' COMMENT '.$this->getColumnCommentSQLDeclaration($column);
        }

        return $columnDeclaration;
    }

    /**
     * Gets the primary key SQL declaration.
     *
     * @param \Fridge\DBAL\Schema\PrimaryKey $primaryKey The primary key.
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the platform does not support primary key.
     *
     * @return string The primary key SQL declaration.
     */
    protected function getPrimaryKeySQLDeclaration(PrimaryKey $primaryKey)
    {
        if (!$this->supportPrimaryKey()) {
            throw PlatformException::methodNotSupported(__METHOD__);
        }

        return 'CONSTRAINT '.$primaryKey->getName().' PRIMARY KEY ('.implode(', ', $primaryKey->getColumnNames()).')';
    }

    /**
     * Gets the foreign key SQL declaration.
     *
     * @param \Fridge\DBAL\Schema\ForeignKey $foreignKey The foreign key.
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the platform does not support foreign key.
     *
     * @return string The foreign key SQL declaration.
     */
    protected function getForeignKeySQLDeclaration(ForeignKey $foreignKey)
    {
        if (!$this->supportForeignKey()) {
            throw PlatformException::methodNotSupported(__METHOD__);
        }

        return 'CONSTRAINT '.$foreignKey->getName().
               ' FOREIGN KEY'.
               ' ('.implode(', ', $foreignKey->getLocalColumnNames()).')'.
               ' REFERENCES '.$foreignKey->getForeignTableName().
               ' ('.implode(', ', $foreignKey->getForeignColumnNames()).')'.
               ' ON DELETE '.$foreignKey->getOnDelete().
               ' ON UPDATE '.$foreignKey->getOnUpdate();
    }

    /**
     * Gets the index SQL declaration.
     *
     * @param \Fridge\DBAl\Schema\Index $index The index.
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the platform does not support index.
     *
     * @return string The index SQL declaration.
     */
    protected function getIndexSQLDeclaration(Index $index)
    {
        if (!$this->supportIndex()) {
            throw PlatformException::methodNotSupported(__METHOD__);
        }

        if (!$index->isUnique()) {
            return 'INDEX '.$index->getName().' ('.implode(', ', $index->getColumnNames()).')';
        }

        return 'CONSTRAINT '.$index->getName().' UNIQUE ('.implode(', ', $index->getColumnNames()).')';
    }

    /**
     * Gets the check constraint SQL declaration.
     *
     * @param \Fridge\DBAL\Schema\Check $check The check constraint.
     *
     * @throws \Fridge\DBAL\Exception\PlatformException If the platform does not support check.
     *
     * @return string The check constraint SQL declaration.
     */
    protected function getCheckSQLDeclaration(Check $check)
    {
        if (!$this->supportCheck()) {
            throw PlatformException::methodNotSupported(__METHOD__);
        }

        return 'CONSTRAINT '.$check->getName().' CHECK ('.$check->getDefinition().')';
    }

    /**
     * Gets the create column comments SQL queries.
     *
     * @param array  $columns The columns.
     * @param string $table   The table name.
     *
     * @return array The create column comments SQL queries.
     */
    protected function getCreateColumnCommentsSQLQueries(array $columns, $table)
    {
        $queries = array();

        foreach ($columns as $column) {
            if ($this->hasMandatoryType($column->getType()->getName()) || ($column->getComment() !== null)) {
                $queries[] = $this->getCreateColumnCommentSQLQuery($column, $table);
            }
        }

        return $queries;
    }

    /**
     * Gets the create column comment SQL query.
     *
     * @param \Fridge\DBAL\Schema\Column $column The column.
     * @param string                     $table  The table name.
     *
     * @return string The create column comment SQL query.
     */
    protected function getCreateColumnCommentSQLQuery(Column $column, $table)
    {
        return 'COMMENT ON COLUMN '.$table.'.'.$column->getName().' IS '.$this->getColumnCommentSQLDeclaration($column);
    }

    /**
     * Gets the column comment SQL declaration.
     *
     * @param Schema\Column $column The colum,.
     *
     * @return string The column comment SQL declaration.
     */
    protected function getColumnCommentSQLDeclaration(Column $column)
    {
        $comment = $column->getComment();

        if ($this->hasMandatoryType($column->getType()->getName())) {
            $comment .= '(FridgeType::'.strtoupper($column->getType()->getName()).')';
        }

        return $this->quote($comment);
    }

    /**
     * Gets the quote.
     *
     * @return string The quote.
     */
    protected function getQuote()
    {
        return '\'';
    }

    /**
     * Quotes a value.
     *
     * @param string $value The value to quote.
     *
     * @return string The quoted value.
     */
    protected function quote($value)
    {
        return $this->getQuote().$value.$this->getQuote();
    }
}
