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
 * Describes a database primary Key.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PrimaryKey extends AbstractAsset implements ConstraintInterface
{
    /** @var array */
    protected $columnNames;

    /**
     * Creates a primary key.
     *
     * @param string $name        The primary key name.
     * @param array  $columnNames The primary key column names.
     */
    public function __construct($name, array $columnNames = array())
    {
        if ($name === null) {
            $name = $this->generateIdentifier('pk_', 20);
        }

        parent::__construct($name);

        $this->setColumnNames($columnNames);
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnNames()
    {
        return $this->columnNames;
    }

    /**
     * Sets the column names.
     *
     * @param array $columnNames The column names.
     */
    public function setColumnNames(array $columnNames)
    {
        $this->columnNames = array();

        foreach ($columnNames as $columnName) {
            $this->addColumnName($columnName);
        }
    }

    /**
     * Adds a column name to the primary key.
     *
     * @param string $columnName The column name to add.
     */
    public function addColumnName($columnName)
    {
        if (!is_string($columnName) || (strlen($columnName) <= 0)) {
            throw SchemaException::invalidPrimaryKeyColumnName($this->getName());
        }

        $this->columnNames[] = $columnName;
    }
}
