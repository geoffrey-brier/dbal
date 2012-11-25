<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Schema;

use Fridge\DBAL\Exception\SchemaException,
    Fridge\DBAL\Type\Type;

/**
 * Describes a database table.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Table extends AbstractAsset
{
    /** @var Fridge\DBAL\Schema\Schema */
    protected $schema;

    /** @var array */
    protected $columns = array();

    /** @var Fridge\DBAL\Schema\PrimaryKey */
    protected $primaryKey;

    /** @var array */
    protected $foreignKeys = array();

    /** @var array */
    protected $indexes = array();

    /** @var array */
    protected $checks = array();

    /**
     * Creates a table.
     *
     * @param string                         $name        The table name.
     * @param array                          $columns     The table columns.
     * @param \Fridge\DBAL\Schema\PrimaryKey $primaryKey  The table primary key.
     * @param array                          $foreignKeys The table foreign keys.
     * @param array                          $indexes     The table indexes.
     * @param array                          $checks      The table checks.
     */
    public function __construct(
        $name,
        array $columns = array(),
        PrimaryKey $primaryKey = null,
        array $foreignKeys = array(),
        array $indexes = array(),
        array $checks = array()
    )
    {
        parent::__construct($name);

        $this->setColumns($columns);

        if ($primaryKey !== null) {
            $this->setPrimaryKey($primaryKey);
        }

        $this->setForeignKeys($foreignKeys);
        $this->setIndexes($indexes);
        $this->setChecks($checks);
    }

    /**
     * Checks if the table has a schema.
     *
     * @return boolean TRUE if the table has a schema else FALSE.
     */
    public function hasSchema()
    {
        return $this->schema !== null;
    }

    /**
     * Gets the table schema.
     *
     * @return \Fridge\DBAL\Schema\Schema The table schema.
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * Sets the table schema.
     *
     * @param \Fridge\DBAL\Schema\Schema $schema The table schema.
     */
    public function setSchema(Schema $schema)
    {
        $this->schema = $schema;

        if (!$this->getSchema()->hasTable($this->getName())) {
            $this->getSchema()->addTable($this);
        }
    }

    /**
     * Creates & adds a new column.
     *
     * @param string                                 $name       The column name.
     * @param string|\Fridge\DBAL\Type\TypeInterface $type       The column type.
     * @param array                                  $properties The column properties.
     *
     * @return \Fridge\DBAL\Schema\Column The new column.
     */
    public function createColumn($name, $type, array $properties = array())
    {
        if (is_string($type)) {
            $type = Type::getType($type);
        }

        $column = new Column($name, $type, $properties);
        $this->addColumn($column);

        return $column;
    }

    /**
     * Checks if the table has columns.
     *
     * @return boolean TRUE if the table has columns else FALSE.
     */
    public function hasColumns()
    {
        return !empty($this->columns);
    }

    /**
     * Gets the table columns.
     *
     * @return array The table columns.
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Sets the table columns.
     *
     * @param array $columns The table columns.
     */
    public function setColumns(array $columns)
    {
        foreach ($this->columns as $column) {
            $this->dropColumn($column->getName());
        }

        foreach ($columns as $column) {
            $this->addColumn($column);
        }
    }

    /**
     * Checks if a table column exists.
     *
     * @param string $name The column name.
     *
     * @return boolean TRUE if the column exists else FALSE.
     */
    public function hasColumn($name)
    {
        return isset($this->columns[$name]);
    }

    /**
     * Gets a table column.
     *
     * @param string $name The table column name.
     *
     * @return \Fridge\DBAL\Schema\Column The table column.
     */
    public function getColumn($name)
    {
        if (!$this->hasColumn($name)) {
            throw SchemaException::tableColumnDoesNotExist($this->getName(), $name);
        }

        return $this->columns[$name];
    }

    /**
     * Adds a column to the table.
     *
     * @param \Fridge\DBAL\Schema\Column $column The column to add.
     */
    public function addColumn(Column $column)
    {
        if ($this->hasColumn($column->getName())) {
            throw SchemaException::tableColumnAlreadyExists($this->getName(), $column->getName());
        }

        $this->columns[$column->getName()] = $column;
    }

    /**
     * Renames a column.
     *
     * @param string $oldName The old column name.
     * @param string $newName The new column name.
     */
    public function renameColumn($oldName, $newName)
    {
        if (!$this->hasColumn($oldName)) {
            throw SchemaException::tableColumnDoesNotExist($this->getName(), $oldName);
        }

        if ($this->hasColumn($newName)) {
            throw SchemaException::tableColumnAlreadyExists($this->getName(), $newName);
        }

        $this->columns[$oldName]->setName($newName);
        $this->columns[$newName] = $this->columns[$oldName];
        unset($this->columns[$oldName]);
    }

    /**
     * Drops a column.
     *
     * @param string $name The column name.
     */
    public function dropColumn($name)
    {
        if (!$this->hasColumn($name)) {
            throw SchemaException::tableColumnDoesNotExist($this->getName(), $name);
        }

        unset($this->columns[$name]);
    }

    /**
     * Creates & adds a new primary key.
     *
     * @param array  $columnNames The primary key column names.
     * @param string $name        The primary key name.
     *
     * @return \Fridge\DBAL\Schema\PrimaryKey The new primary key.
     */
    public function createPrimaryKey(array $columnNames, $name = null)
    {
        if ($this->hasPrimaryKey()) {
            throw SchemaException::tablePrimaryKeyAlreadyExists($this->getName());
        }

        $primaryKey = new PrimaryKey($name, $columnNames);
        $this->setPrimaryKey($primaryKey);

        $this->createIndex($columnNames, true, $primaryKey->getName());

        return $primaryKey;
    }

    /**
     * Checks if the table has a primary key.
     *
     * @return boolean TRUE if the table has a primary key else FALSE.
     */
    public function hasPrimaryKey()
    {
        return $this->primaryKey !== null;
    }

    /**
     * Gets the table primary key.
     *
     * @return \Fridge\DBAL\Schema\PrimaryKey The table primary key.
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * Sets the table primery key.
     *
     * @param \Fridge\DBAL\Schema\PrimaryKey $primaryKey The table primary key.
     */
    public function setPrimaryKey(PrimaryKey $primaryKey)
    {
        foreach ($primaryKey->getColumnNames() as $columnName) {
            $this->getColumn($columnName)->setNotNull(true);
        }

        $this->primaryKey = $primaryKey;
    }

    /**
     * Drops the table primary key.
     */
    public function dropPrimaryKey()
    {
        if (!$this->hasPrimaryKey()) {
            throw SchemaException::tablePrimaryKeyDoesNotExist($this->getName());
        }

        foreach ($this->getIndexes() as $index) {
            if ($index->hasSameColumnNames($this->getPrimaryKey()->getColumnNames())) {
                $this->dropIndex($index->getName());
            }
        }

        $this->primaryKey = null;
    }

    /**
     * Creates & adds a new foreign key.
     *
     * @param array                            $localColumnNames   The foreign key local column names.
     * @param string|\Fridge\DBAL\Schema\Table $foreignTable       The foreign key foreign table.
     * @param array                            $foreignColumnNames The foreign key foreign column names.
     * @param string                           $onDelete           The foreign key referential on delete action.
     * @param string                           $onUpdate           the foreign key referential on update action.
     * @param string                           $name               The foreign key name.
     *
     * @return \Fridge\DBAL\Schema\ForeignKey The new foreign key.
     */
    public function createForeignKey(
        array $localColumnNames,
        $foreignTable,
        array $foreignColumnNames,
        $onDelete = ForeignKey::RESTRICT,
        $onUpdate = ForeignKey::RESTRICT,
        $name = null
    )
    {
        if ($foreignTable instanceof Table) {
            $foreignTable = $foreignTable->getName();
        }

        $foreignKey = new ForeignKey(
            $name,
            $localColumnNames,
            $foreignTable,
            $foreignColumnNames,
            $onDelete,
            $onUpdate
        );

        $this->addForeignKey($foreignKey);

        $this->createIndex($localColumnNames, false, 'idx_'.$foreignKey->getName());

        return $foreignKey;
    }

    /**
     * Checks if the table has foreign keys.
     *
     * @return boolean TRUE if the table has foreign keys else FALSE.
     */
    public function hasForeignKeys()
    {
        return !empty($this->foreignKeys);
    }

    /**
     * Gets the table foreign keys.
     *
     * @return array The table foreign keys.
     */
    public function getForeignKeys()
    {
        return $this->foreignKeys;
    }

    /**
     * Sets the table foreign keys.
     *
     * @param array $foreignKeys The table foreign keys.
     */
    public function setForeignKeys(array $foreignKeys)
    {
        foreach ($this->foreignKeys as $foreignKey) {
            $this->dropForeignKey($foreignKey->getName());
        }

        foreach ($foreignKeys as $foreignKey) {
            $this->addForeignKey($foreignKey);
        }
    }

    /**
     * Checks if the foreign key exists.
     *
     * @param string $name The foreign key name.
     *
     * @return boolean TRUE if the foreign key exists else FALSE.
     */
    public function hasForeignKey($name)
    {
        return isset($this->foreignKeys[$name]);
    }

    /**
     * Gets a foreign key
     *
     * @param string $name The foreign key name.
     *
     * @return \Fridge\DBAL\Schema\ForeignKey The foreign key.
     */
    public function getForeignKey($name)
    {
        if (!$this->hasForeignKey($name)) {
            throw SchemaException::tableForeignKeyDoesNotExist($this->getName(), $name);
        }

        return $this->foreignKeys[$name];
    }

    /**
     * Adds a foreign key to the table.
     *
     * @param \Fridge\DBAL\Schema\ForeignKey $foreignKey The foreign key to add.
     */
    public function addForeignKey(ForeignKey $foreignKey)
    {
        if ($this->hasForeignKey($foreignKey->getName())) {
            throw SchemaException::tableForeignKeyAlreadyExists($this->getName(), $foreignKey->getName());
        }

        foreach ($foreignKey->getLocalColumnNames() as $columnName) {
            if (!$this->hasColumn($columnName)) {
                throw SchemaException::tableColumnDoesNotExist($this->getName(), $columnName);
            }
        }

        if ($this->hasSchema()) {
            foreach ($foreignKey->getForeignColumnNames() as $columnName) {
                if (!$this->getSchema()->getTable($foreignKey->getForeignTableName())->hasColumn($columnName)) {
                    throw SchemaException::tableColumnDoesNotExist($foreignKey->getForeignTableName(), $columnName);
                }
            }
        }

        $this->foreignKeys[$foreignKey->getName()] = $foreignKey;
    }

    /**
     * Renames a foreign key.
     *
     * @param string $oldName The old foreign key name.
     * @param string $newName The new foreign key name.
     */
    public function renameForeignKey($oldName, $newName)
    {
        if (!$this->hasForeignKey($oldName)) {
            throw SchemaException::tableForeignKeyDoesNotExist($this->getName(), $oldName);
        }

        if ($this->hasForeignKey($newName)) {
            throw SchemaException::tableForeignKeyAlreadyExists($this->getName(), $newName);
        }

        $this->foreignKeys[$oldName]->setName($newName);
        $this->foreignKeys[$newName] = $this->foreignKeys[$oldName];
        unset($this->foreignKeys[$oldName]);
    }

    /**
     * Drops a foreign key.
     *
     * @param string $name The foreign key name.
     */
    public function dropForeignKey($name)
    {
        if (!$this->hasForeignKey($name)) {
            throw SchemaException::tableForeignKeyDoesNotExist($this->getName(), $name);
        }

        unset($this->foreignKeys[$name]);
    }

    /**
     * Creates & adds a new index.
     *
     * @param array   $columnNames The index column names.
     * @param boolean $unique      TRUE if the index is unique else FALSE.
     * @param string  $name        The index name.
     *
     * @return \Fridge\DBAL\Schema\Index The new index.
     */
    public function createIndex(array $columnNames, $unique = false, $name = null)
    {
        $index = new Index($name, $columnNames, $unique);
        $this->addIndex($index);

        return $index;
    }

    /**
     * Checks if tha table has indexes.
     *
     * @return boolean TRUE if the table has indexes else FALSE.
     */
    public function hasIndexes()
    {
        return !empty($this->indexes);
    }

    /**
     * Gets the table indexes.
     *
     * @return array The table indexes.
     */
    public function getIndexes()
    {
        return $this->indexes;
    }

    /**
     * Sets the table indexes.
     *
     * @param array $indexes The table indexes.
     */
    public function setIndexes(array $indexes)
    {
        foreach ($this->indexes as $index) {
            $this->dropIndex($index->getName());
        }

        foreach ($indexes as $index) {
            $this->addIndex($index);
        }
    }

    /**
     * Gets the filtered indexes (remove primary key index if it exists).
     *
     * @return array The filtered indexes.
     */
    public function getFilteredIndexes()
    {
        if (!$this->hasPrimaryKey()) {
            return $this->getIndexes();
        }

        $indexes = array();

        foreach ($this->getIndexes() as $name => $index) {
            if (!$index->hasSameColumnNames($this->getPrimaryKey()->getColumnNames())) {
                $indexes[$name] = $index;
            }
        }

        return $indexes;
    }

    /**
     * Checks if a table index exists.
     *
     * @param string $name The index name.
     *
     * @return boolean TRUE if the index exists else FALSE.
     */
    public function hasIndex($name)
    {
        return isset($this->indexes[$name]);
    }

    /**
     * Gets a table index.
     *
     * @param string $name The table index name.
     *
     * @return \Fridge\DBAL\Schema\Index The table index.
     */
    public function getIndex($name)
    {
        if (!$this->hasIndex($name)) {
            throw SchemaException::tableIndexDoesNotExist($this->getName(), $name);
        }

        return $this->indexes[$name];
    }

    /**
     * Adds an index to the table.
     *
     * @param \Fridge\DBAL\Schema\Index $index The index to add.
     */
    public function addIndex(Index $index)
    {
        if ($this->hasIndex($index->getName())) {
            throw SchemaException::tableIndexAlreadyExists($this->getName(), $index->getName());
        }

        foreach ($index->getColumnNames() as $columnName) {
            if (!$this->hasColumn($columnName)) {
                throw SchemaException::tableColumnDoesNotExist($this->getName(), $columnName);
            }
        }

        foreach ($this->getIndexes() as $indexCandidate) {
            if ($indexCandidate->isBetterThan($index)) {
                return;
            }
        }

        foreach ($this->getIndexes() as $indexCandidate) {
            if ($index->isBetterThan($indexCandidate)) {
                $this->dropIndex($indexCandidate->getName());
            }
        }

        $this->indexes[$index->getName()] = $index;
    }

    /**
     * Renames an index.
     *
     * @param string $oldName The old index name.
     * @param string $newName The new index name.
     */
    public function renameIndex($oldName, $newName)
    {
        if (!$this->hasIndex($oldName)) {
            throw SchemaException::tableIndexDoesNotExist($this->getName(), $oldName);
        }

        if ($this->hasIndex($newName)) {
            throw SchemaException::tableIndexAlreadyExists($this->getName(), $newName);
        }

        $this->indexes[$oldName]->setName($newName);
        $this->indexes[$newName] = $this->indexes[$oldName];
        unset($this->indexes[$oldName]);
    }

    /**
     * Drops an index.
     *
     * @param string $name The index name.
     */
    public function dropIndex($name)
    {
        if (!$this->hasIndex($name)) {
            throw SchemaException::tableIndexDoesNotExist($this->getName(), $name);
        }

        unset($this->indexes[$name]);
    }

    /**
     * Creates & adds a new check.
     *
     * @param string $constraint The check constraint.
     * @param string $name       The check name.
     *
     * @return \Fridge\DBAL\Schema\Check The new index.
     */
    public function createCheck($constraint, $name = null)
    {
        $check = new Check($name, $constraint);
        $this->addCheck($check);

        return $check;
    }

    /**
     * Checks if the table has checks.
     *
     * @return boolean TRUE if the table has checks else FALSE.
     */
    public function hasChecks()
    {
        return !empty($this->checks);
    }

    /**
     * Gets the table checks.
     *
     * @return array The table checks.
     */
    public function getChecks()
    {
        return $this->checks;
    }

    /**
     * Sets the table chekcs.
     *
     * @param array $checks The table checks.
     */
    public function setChecks(array $checks)
    {
        foreach ($this->checks as $check) {
            $this->dropCheck($check->getName());
        }

        foreach ($checks as $check) {
            $this->addCheck($check);
        }
    }

    /**
     * Checks if a table check exists.
     *
     * @param string $name The check name.
     *
     * @return boolean TRUE if the check exists else FALSE.
     */
    public function hasCheck($name)
    {
        return isset($this->checks[$name]);
    }

    /**
     * Gets a table check.
     *
     * @param string $name The table check name.
     *
     * @return \Fridge\DBAL\Schema\Check The table check.
     */
    public function getCheck($name)
    {
        if (!$this->hasCheck($name)) {
            throw SchemaException::tableCheckDoesNotExist($this->getName(), $name);
        }

        return $this->checks[$name];
    }

    /**
     * Adds a check to the table.
     *
     * @param \Fridge\DBAL\Schema\Check $check The check to add.
     */
    public function addCheck(Check $check)
    {
        if ($this->hasCheck($check->getName())) {
            throw SchemaException::tableCheckAlreadyExists($this->getName(), $check->getName());
        }

        $this->checks[$check->getName()] = $check;
    }

    /**
     * Renames a check.
     *
     * @param string $oldName The old check name.
     * @param string $newName The new check name.
     */
    public function renameCheck($oldName, $newName)
    {
        if (!$this->hasCheck($oldName)) {
            throw SchemaException::tableCheckDoesNotExist($this->getName(), $oldName);
        }

        if ($this->hasCheck($newName)) {
            throw SchemaException::tableCheckAlreadyExists($this->getName(), $newName);
        }

        $this->checks[$oldName]->setName($newName);
        $this->checks[$newName] = $this->checks[$oldName];
        unset($this->checks[$oldName]);
    }

    /**
     * Drops a check.
     *
     * @param string $name The check name.
     */
    public function dropCheck($name)
    {
        if (!$this->hasCheck($name)) {
            throw SchemaException::tableCheckDoesNotExist($this->getName(), $name);
        }

        unset($this->checks[$name]);
    }

    /**
     * {@inhertidoc}
     */
    public function __clone()
    {
        foreach ($this->columns as $key => $column) {
            $this->columns[$key] = clone $column;
        }

        if ($this->primaryKey !== null) {
            $this->primaryKey = clone $this->primaryKey;
        }

        foreach ($this->foreignKeys as $key => $foreignKey) {
            $this->foreignKeys[$key] = clone $foreignKey;
        }

        foreach ($this->indexes as $key => $index) {
            $this->indexes[$key] = clone $index;
        }

        foreach ($this->checks as $key => $check) {
            $this->checks[$key] = clone $check;
        }
    }
}
