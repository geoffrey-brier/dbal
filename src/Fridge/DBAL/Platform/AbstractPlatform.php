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
    Fridge\DBAL\Exception,
    Fridge\DBAL\Schema,
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
     */
    public function getMappedType($type)
    {
        if ($this->hasMappedType($type)) {
            return $this->mappedTypes[$type];
        }

        if ($this->strictMappedType) {
            throw Exception\PlatformException::mappedTypeDoesNotExist($type);
        }

        return $this->fallbackMappedType;
    }

    /**
     * {@inheritdoc}
     */
    public function addMappedType($databaseType, $fridgeType)
    {
        if ($this->hasMappedType($databaseType)) {
            throw Exception\PlatformException::mappedTypeAlreadyExists($databaseType);
        }

        $this->mappedTypes[$databaseType] = $fridgeType;
    }

    /**
     * {@inheritdoc}
     */
    public function overrideMappedType($databaseType, $fridgeType)
    {
        if (!$this->hasMappedType($databaseType)) {
            throw Exception\PlatformException::mappedTypeDoesNotExist($databaseType);
        }

        $this->mappedTypes[$databaseType] = $fridgeType;
    }

    /**
     * {@inheritdoc}
     */
    public function removeMappedType($type)
    {
        if (!$this->hasMappedType($type)) {
            throw Exception\PlatformException::mappedTypeDoesNotExist($type);
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
     */
    public function setFallbackMappedType($fallbackMappedType)
    {
        if (!Type::hasType($fallbackMappedType)) {
            throw Exception\TypeException::typeDoesNotExist($fallbackMappedType);
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
     */
    public function addMandatoryType($type)
    {
        if ($this->hasMandatoryType($type)) {
            throw Exception\PlatformException::mandatoryTypeAlreadyExists($type);
        }

        $this->mandatoryTypes[] = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function removeMandatoryType($type)
    {
        if (!$this->hasMandatoryType($type)) {
            throw Exception\PlatformException::mandatoryTypeDoesNotExist($type);
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
    public function supportInlineTableColumnComment()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreateSavepointSQLQuery($savepoint)
    {
        if (!$this->supportSavepoint()) {
            throw Exception\PlatformException::methodNotSupported(__METHOD__);
        }

        return 'SAVEPOINT '.$savepoint;
    }

    /**
     * {@inheritdoc}
     */
    public function getReleaseSavepointSQLQuery($savepoint)
    {
        if (!$this->supportSavepoint()) {
            throw Exception\PlatformException::methodNotSupported(__METHOD__);
        }

        return 'RELEASE SAVEPOINT '.$savepoint;
    }

    /**
     * {@inheritdoc}
     */
    public function getRollbackSavepointSQLQuery($savepoint)
    {
        if (!$this->supportSavepoint()) {
            throw Exception\PlatformException::methodNotSupported(__METHOD__);
        }

        return 'ROLLBACK TO SAVEPOINT '.$savepoint;
    }

    /**
     * {@inheritdoc}
     */
    public function getSetCharsetSQLQuery($charset)
    {
        return 'SET NAMES \''.$charset.'\'';
    }

    /**
     * {@inheritdoc}
     */
    public function getCreateDatabaseSQLQuery($database)
    {
        return 'CREATE DATABASE '.$database;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreateSequenceSQLQuery(Schema\Sequence $sequence)
    {
        if (!$this->supportSequence()) {
            throw Exception\PlatformException::methodNotSupported(__METHOD__);
        }

        return 'CREATE SEQUENCE '.$sequence->getName().
               ' INCREMENT BY '.$sequence->getIncrementSize().
               ' MINVALUE '.$sequence->getInitialValue().
               ' START WITH '.$sequence->getInitialValue();
    }

    /**
     * {@inheritdoc}
     */
    public function getCreateViewSQLQuery(Schema\View $view)
    {
        if (!$this->supportView()) {
            throw Exception\PlatformException::methodNotSupported(__METHOD__);
        }

        return 'CREATE VIEW '.$view->getName().' AS '.$view->getSQL();
    }

    /**
     * {@inheritdoc}
     */
    public function getCreateTableSQLQueries(Schema\Table $table, array $flags = array())
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
            foreach ($this->getCreateTableIndexes($table) as $index) {
                $query .= ', '.$this->getIndexSQLDeclaration($index);
            }
        }

        if (!isset($flags['foreign_key']) || $flags['foreign_key']) {
            foreach ($table->getForeignKeys() as $foreignKey) {
                $query .= ', '.$this->getForeignKeySQLDeclaration($foreignKey);
            }
        }

        $query .= ')';

        array_unshift($queries, $query);

        return $queries;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreateConstraintSQLQuery(Schema\ConstraintInterface $constraint, $table)
    {
        if ($constraint instanceof Schema\PrimaryKey) {
            return $this->getCreatePrimaryKeySQLQuery($constraint, $table);
        }

        if ($constraint instanceof Schema\ForeignKey) {
            return $this->getCreateForeignKeySQLQuery($constraint, $table);
        }

        return $this->getCreateIndexSQLQuery($constraint, $table);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatePrimaryKeySQLQuery(Schema\PrimaryKey $primaryKey, $table)
    {
        return 'ALTER TABLE '.$table.' ADD '.$this->getPrimaryKeySQLDeclaration($primaryKey);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreateForeignKeySQLQuery(Schema\ForeignKey $foreignKey, $table)
    {
        return 'ALTER TABLE '.$table.' ADD '.$this->getForeignKeySQLDeclaration($foreignKey);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreateIndexSQLQuery(Schema\Index $index, $table)
    {
        if ($index->isUnique()) {
            return 'ALTER TABLE '.$table.' ADD '.$this->getIndexSQLDeclaration($index);
        }

        if (!$this->supportIndex()) {
            throw Exception\PlatformException::methodNotSupported(__METHOD__);
        }

        return 'CREATE INDEX '.$index->getName().
                ' ON '.$table.
                ' ('.implode(', ', $index->getColumnNames()).')';
    }

    /**
     * {@inheritdoc}
     */
    public function getCreateCheckConstraintSQLQuery(Schema\Check $check, $table)
    {
        return 'ALTER TABLE '.$table.' ADD '.$this->getCheckConstraintSQLDeclaration($check);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreateColumnCommentsSQLQueries(array $columns, $table)
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
     * {@inheritdoc}
     */
    public function getCreateColumnCommentSQLQuery(Schema\Column $column, $table)
    {
        return 'COMMENT ON COLUMN '.$table.'.'.$column->getName().' IS '.$this->getColumnCommentSQLDeclaration($column);
    }

    /**
     * {@inheritdoc}
     */
    public function getDropDatabaseSQLQuery($database)
    {
        return 'DROP DATABASE '.$database;
    }

    /**
     * {@inheritdoc}
     */
    public function getDropSequenceSQLQuery(Schema\Sequence $sequence)
    {
        if (!$this->supportSequence()) {
            throw Exception\PlatformException::methodNotSupported(__METHOD__);
        }

        return 'DROP SEQUENCE '.$sequence->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getDropViewSQLQuery(Schema\View $view)
    {
        if (!$this->supportView()) {
            throw Exception\PlatformException::methodNotSupported(__METHOD__);
        }

        return 'DROP VIEW '.$view->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getDropTableSQLQuery(Schema\Table $table)
    {
        return 'DROP TABLE '.$table->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getDropConstraintSQLQuery(Schema\ConstraintInterface $contraint, $table)
    {
        if ($contraint instanceof Schema\PrimaryKey) {
            return $this->getDropPrimaryKeySQLQuery($contraint, $table);
        }

        if ($contraint instanceof Schema\ForeignKey) {
            return $this->getDropForeignKeySQLQuery($contraint, $table);
        }

        return $this->getDropIndexSQLQuery($contraint, $table);
    }

    /**
     * {@inheritdoc}
     */
    public function getDropPrimaryKeySQLQuery(Schema\PrimaryKey $primaryKey, $table)
    {
        if (!$this->supportPrimaryKey()) {
            throw Exception\PlatformException::methodNotSupported(__METHOD__);
        }

        return 'ALTER TABLE '.$table.' DROP CONSTRAINT '.$primaryKey->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getDropForeignKeySQLQuery(Schema\ForeignKey $foreignKey, $table)
    {
        if (!$this->supportForeignKey()) {
            throw Exception\PlatformException::methodNotSupported(__METHOD__);
        }

        return 'ALTER TABLE '.$table.' DROP CONSTRAINT '.$foreignKey->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getDropIndexSQLQuery(Schema\Index $index, $table)
    {
        if (!$this->supportIndex()) {
            throw Exception\PlatformException::methodNotSupported(__METHOD__);
        }

        if ($index->isUnique()) {
            return 'ALTER TABLE '.$table.' DROP CONSTRAINT '.$index->getName();
        }

        return 'DROP INDEX '.$index->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getDropCheckConstraintSQLQuery(Schema\Check $check, $table)
    {
        return 'ALTER TABLE '.$table.' DROP CONSTRAINT '.$check->getName();
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
     * @return string The varchar SQL declaration snippet.
     */
    protected function getVarcharSQLDeclarationSnippet($length, $fixed)
    {
        if (!is_int($length) || (is_int($length) && ($length <= 0))) {
            throw Exception\PlatformException::invalidVarcharLength();
        }

        if (!is_bool($fixed)) {
            throw Exception\PlatformException::invalidVarcharFixedFlag();
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
            throw Exception\PlatformException::transactionIsolationDoesNotExist($isolation);
        }

        return $isolation;
    }

    /**
     * Gets the indexes needed for the create table SQL query.
     *
     * @param \Fridge\DBAL\Schema\Table $table The table.
     *
     * @return array The indexes needed for the created table SQL query.
    */
    protected function getCreateTableIndexes(Schema\Table $table)
    {
        if (!$table->hasPrimaryKey()) {
            return $table->getIndexes();
        }

        $indexes = array();

        foreach ($table->getIndexes() as $index) {
            if (!$index->hasSameColumnNames($table->getPrimaryKey()->getColumnNames())) {
                $indexes[] = $index;
            }
        }

        return $indexes;
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
    protected function getColumnSQLDeclaration(Schema\Column $column)
    {
        $columnDeclaration = $column->getName().' '.$column->getType()->getSQLDeclaration($this, $column->toArray());

        if ($column->isNotNull()) {
            $columnDeclaration .= ' NOT NULL';
        }

        if ($column->getDefault() !== null) {
            $default = $column->getType()->convertToDatabaseValue($column->getDefault(), $this);
            $columnDeclaration .= ' DEFAULT \''.$default.'\'';
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
     * @return string The primary key SQL declaration.
     */
    protected function getPrimaryKeySQLDeclaration(Schema\PrimaryKey $primaryKey)
    {
        if (!$this->supportPrimaryKey()) {
            throw Exception\PlatformException::methodNotSupported(__METHOD__);
        }

        return 'CONSTRAINT '.$primaryKey->getName().' PRIMARY KEY ('.implode(', ', $primaryKey->getColumnNames()).')';
    }

    /**
     * Gets the foreign key SQL declaration.
     *
     * @param \Fridge\DBAL\Schema\ForeignKey $foreignKey The foreign key.
     *
     * @return string The foreign key SQL declaration.
     */
    protected function getForeignKeySQLDeclaration(Schema\ForeignKey $foreignKey)
    {
        if (!$this->supportForeignKey()) {
            throw Exception\PlatformException::methodNotSupported(__METHOD__);
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
     * @return string The index SQL declaration.
     */
    protected function getIndexSQLDeclaration(Schema\Index $index)
    {
        if (!$this->supportIndex()) {
            throw Exception\PlatformException::methodNotSupported(__METHOD__);
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
     * @return string The check constraint SQL declaration.
     */
    protected function getCheckConstraintSQLDeclaration(Schema\Check $check)
    {
        return 'CONSTRAINT '.$check->getName().' CHECK ('.$check->getConstraint().')';
    }

    /**
     * Gets the column comment SQL declaration.
     *
     * @param Schema\Column $column The colum,.
     *
     * @return string The column comment SQL declaration.
     */
    protected function getColumnCommentSQLDeclaration(Schema\Column $column)
    {
        $comment = '\'';
        $comment .= $column->getComment();

        if ($this->hasMandatoryType($column->getType()->getName())) {
            $comment .= '(FridgeType::'.strtoupper($column->getType()->getName()).')';
        }

        $comment .= '\'';

        return $comment;
    }
}
