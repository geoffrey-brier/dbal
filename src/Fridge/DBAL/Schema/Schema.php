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

use Fridge\DBAL\Exception\SchemaException;

/**
 * Describes a database schema.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Schema extends AbstractAsset
{
    /** @var array */
    protected $tables;

    /** @var array */
    protected $sequences;

    /** @var array */
    protected $views;

    /**
     * Creates a schema.
     *
     * @param string $name      The schema name.
     * @param array  $tables    The schema tables.
     * @param array  $sequences The schema sequences.
     * @param array  $views     The schema views.
     */
    public function __construct($name, array $tables = array(), array $sequences = array(), array $views = array())
    {
        parent::__construct($name);

        $this->tables = array();
        $this->sequences = array();
        $this->views = array();

        $this->setTables($tables);
        $this->setSequences($sequences);
        $this->setViews($views);
    }

    /**
     * Creates & adds a new table.
     *
     * @param string $name        The table name.
     * @param array  $columns     The table columns.
     * @param array  $primaryKey  The table primary key.
     * @param array  $foreignKeys The table foreign keys.
     * @param array  $indexes     The table indexes.
     * @param array  $checks      The table checks.
     *
     * @return \Fridge\DBAL\Schema\Table The new table.
     */
    public function createTable(
        $name,
        array $columns = array(),
        array $primaryKey = array(),
        array $foreignKeys = array(),
        array $indexes = array(),
        array $checks = array()
    )
    {
        $table = new Table($name);

        foreach ($columns as $column) {
            if (!isset($column['options'])) {
                $column['options'] = array();
            }

            $table->createColumn($column['name'], $column['type'], $column['options']);
        }

        if (!empty($primaryKey)) {
            if (!isset($primaryKey['name'])) {
                $primaryKey['name'] = null;
            }

            $table->createPrimaryKey($primaryKey['columns'], $primaryKey['name']);
        }

        foreach ($foreignKeys as $foreignKey) {
            if (!isset($foreignKey['name'])) {
                $foreignKey['name'] = null;
            }

            if (!isset($foreignKey['on_delete'])) {
                $foreignKey['on_delete'] = ForeignKey::RESTRICT;
            }

            if (!isset($foreignKey['on_update'])) {
                $foreignKey['on_update'] = ForeignKey::RESTRICT;
            }

            $table->createForeignKey(
                $foreignKey['local_columns'],
                $foreignKey['foreign_table'],
                $foreignKey['foreign_columns'],
                $foreignKey['on_delete'],
                $foreignKey['on_update'],
                $foreignKey['name']
            );
        }

        foreach ($indexes as $index) {
            if (!isset($index['name'])) {
                $index['name'] = null;
            }

            if (!isset($index['unique'])) {
                $index['unique'] = false;
            }

            $table->createIndex($index['columns'], $index['unique'], $index['name']);
        }

        foreach ($checks as $check) {
            if (!isset($check['name'])) {
                $check['name'] = null;
            }

            $table->createCheck($check['definition'], $check['name']);
        }

        $this->addTable($table);

        return $table;
    }

    /**
     * Checks if the schema has tables.
     *
     * @return boolean TRUE if the schema has tables else FALSE.
     */
    public function hasTables()
    {
        return !empty($this->tables);
    }

    /**
     * Gets the schema tables.
     *
     * @return array The schema tables.
     */
    public function getTables()
    {
        return $this->tables;
    }

    /**
     * Sets the schema tables.
     *
     * @param array $tables The schema tables.
     */
    public function setTables(array $tables)
    {
        foreach ($this->tables as $table) {
            $this->dropTable($table->getName());
        }

        foreach ($tables as $table) {
            $this->addTable($table);
        }
    }

    /**
     * Checks if a table exists.
     *
     * @param string $name The table name.
     *
     * @return boolean TRUE if the tables exists else FALSE.
     */
    public function hasTable($name)
    {
        return isset($this->tables[$name]);
    }

    /**
     * Gets a table.
     *
     * @param string $name The table name.
     *
     * @return \Fridge\DBAL\Schema\Table The table.
     */
    public function getTable($name)
    {
        if (!$this->hasTable($name)) {
            throw SchemaException::schemaTableDoesNotExist($this->getName(), $name);
        }

        return $this->tables[$name];
    }

    /**
     * Adds a table to the schema.
     *
     * @param \Fridge\DBAL\Schema\Table $table The table to add.
     */
    public function addTable(Table $table)
    {
        if ($this->hasTable($table->getName())) {
            throw SchemaException::schemaTableAlreadyExists($this->getName(), $table->getName());
        }

        $this->tables[$table->getName()] = $table;

        if ($table->getSchema() !== $this) {
            $table->setSchema($this);
        }
    }

    /**
     * Renames a table.
     *
     * @param string $oldName The old table name.
     * @param string $newName The new table name.
     */
    public function renameTable($oldName, $newName)
    {
        if (!$this->hasTable($oldName)) {
            throw SchemaException::schemaTableDoesNotExist($this->getName(), $oldName);
        }

        if ($this->hasTable($newName)) {
            throw SchemaException::schemaTableAlreadyExists($this->getName(), $newName);
        }

        $this->tables[$oldName]->setName($newName);
        $this->tables[$newName] = $this->tables[$oldName];

        unset($this->tables[$oldName]);
    }

    /**
     * Drops a table.
     *
     * @param string $name The table name.
     */
    public function dropTable($name)
    {
        if (!$this->hasTable($name)) {
            throw SchemaException::schemaTableDoesNotExist($this->getName(), $name);
        }

        unset($this->tables[$name]);
    }

    /**
     * Creates & adds a new sequence.
     *
     * @param string  $name          The sequence name.
     * @param integer $initialValue  The sequence initial value.
     * @param integer $incrementSize The sequence increment size.
     *
     * @return \Fridge\DBAL\Schema\Sequence The new sequence.
     */
    public function createSequence($name, $initialValue = 1, $incrementSize = 1)
    {
        $sequence = new Sequence($name, $initialValue, $incrementSize);
        $this->addSequence($sequence);

        return $sequence;
    }

    /**
     * Checks if the schema has sequences.
     *
     * @return boolean TRUE if the schema has sequences else FALSE.
     */
    public function hasSequences()
    {
        return !empty($this->sequences);
    }

    /**
     * Gets the schema sequences.
     *
     * @return array The schema sequences.
     */
    public function getSequences()
    {
        return $this->sequences;
    }

    /**
     * Sets the schema sequences.
     *
     * @param array $sequences The schema sequences.
     */
    public function setSequences(array $sequences)
    {
        foreach ($this->sequences as $sequence) {
            $this->dropSequence($sequence->getName());
        }

        foreach ($sequences as $sequence) {
            $this->addSequence($sequence);
        }
    }

    /**
     * Checks if a sequence exists.
     *
     * @param string $name The sequence name.
     *
     * @return boolean TRUE if the sequence exists else FALSE.
     */
    public function hasSequence($name)
    {
        return isset($this->sequences[$name]);
    }

    /**
     * Gets a sequence.
     *
     * @param string $name The sequence name.
     *
     * @return \Fridge\DBAL\Schema\Sequence The sequence.
     */
    public function getSequence($name)
    {
        if (!$this->hasSequence($name)) {
            throw SchemaException::schemaSequenceDoesNotExist($this->getName(), $name);
        }

        return $this->sequences[$name];
    }

    /**
     * Adds a sequence to the schema.
     *
     * @param Fridge\DBAL\Schema\Sequence $sequence The sequence to add.
     */
    public function addSequence(Sequence $sequence)
    {
        if ($this->hasSequence($sequence->getName())) {
            throw SchemaException::schemaSequenceAlreadyExists($this->getName(), $sequence->getName());
        }

        $this->sequences[$sequence->getName()] = $sequence;
    }

    /**
     * Renames a sequence.
     *
     * @param string $oldName The old sequence name.
     * @param string $newName The new sequence name.
     */
    public function renameSequence($oldName, $newName)
    {
        if (!$this->hasSequence($oldName)) {
            throw SchemaException::schemaSequenceDoesNotExist($this->getName(), $oldName);
        }

        if ($this->hasSequence($newName)) {
            throw SchemaException::schemaSequenceAlreadyExists($this->getName(), $newName);
        }

        $this->sequences[$oldName]->setName($newName);
        $this->sequences[$newName] = $this->sequences[$oldName];

        unset($this->sequences[$oldName]);
    }

    /**
     * Drops a sequence.
     *
     * @param string $name The sequence name.
     */
    public function dropSequence($name)
    {
        if (!$this->hasSequence($name)) {
            throw SchemaException::schemaSequenceDoesNotExist($this->getName(), $name);
        }

        unset($this->sequences[$name]);
    }

    /**
     * Creates & adds a new view.
     *
     * @param string $name The view name.
     * @param string $sql  The view sql.
     *
     * @return \Fridge\DBAL\Schema\View The new view.
     */
    public function createView($name, $sql = null)
    {
        $view = new View($name, $sql);
        $this->addView($view);

        return $view;
    }

    /**
     * Checks if the schema has views.
     *
     * @return boolean TRUE if the schema has views else FALSE.
     */
    public function hasViews()
    {
        return !empty($this->views);
    }

    /**
     * Gets the schema views.
     *
     * @return array The schema views.
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Sets the schema views.
     *
     * @param array $views The schema views.
     */
    public function setViews(array $views)
    {
        foreach ($this->views as $view) {
            $this->dropView($view->getName());
        }

        foreach ($views as $view) {
            $this->addView($view);
        }
    }

    /**
     * Checks if a view exists.
     *
     * @param string $name The view name.
     *
     * @return boolean TRUE if the view exists else FALSE.
     */
    public function hasView($name)
    {
        return isset($this->views[$name]);
    }

    /**
     * Gets a view.
     *
     * @param string $name The view name.
     *
     * @return \Fridge\DBAL\Schema\View The view.
     */
    public function getView($name)
    {
        if (!$this->hasView($name)) {
            throw SchemaException::schemaViewDoesNotExist($this->getName(), $name);
        }

        return $this->views[$name];
    }

    /**
     * Adds a view to the schema.
     *
     * @param \Fridge\DBAL\Schema\View $view The view to add.
     */
    public function addView(View $view)
    {
        if ($this->hasView($view->getName())) {
            throw SchemaException::schemaViewAlreadyExists($this->getName(), $view->getName());
        }

        $this->views[$view->getName()] = $view;
    }

    /**
     * Renames a view.
     *
     * @param string $oldName The old view name.
     * @param string $newName The new view name.
     */
    public function renameView($oldName, $newName)
    {
        if (!$this->hasView($oldName)) {
            throw SchemaException::schemaViewDoesNotExist($this->getName(), $oldName);
        }

        if ($this->hasView($newName)) {
            throw SchemaException::schemaViewAlreadyExists($this->getName(), $newName);
        }

        $this->views[$oldName]->setName($newName);
        $this->views[$newName] = $this->views[$oldName];

        unset($this->views[$oldName]);
    }

    /**
     * Drops a view.
     *
     * @param string $name The view name.
     */
    public function dropView($name)
    {
        if (!$this->hasView($name)) {
            throw SchemaException::schemaViewDoesNotExist($this->getName(), $name);
        }

        unset($this->views[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function __clone()
    {
        foreach ($this->tables as $key => $table) {
            $this->tables[$key] = clone $table;
            $this->tables[$key]->setSchema($this);
        }

        foreach ($this->sequences as $key => $sequence) {
            $this->sequences[$key] = clone $sequence;
        }

        foreach ($this->views as $key => $view) {
            $this->views[$key] = clone $view;
        }
    }
}
