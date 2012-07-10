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
 * Describes a database index.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Index extends AbstractAsset implements ConstraintInterface
{
    /** @var array */
    protected $columnNames;

    /** @var boolean */
    protected $unique;

    /**
     * Creates an index.
     *
     * @param string  $name        The index name.
     * @param array   $columnNames The index column names.
     * @param boolean $unique      TRUE if the index is unique else FALSE.
     */
    public function __construct($name, array $columnNames = array(), $unique = false)
    {
        if ($name === null) {
            $name = $this->generateIdentifier('idx_', 20);
        }

        parent::__construct($name);

        $this->setColumnNames($columnNames);
        $this->setUnique($unique);
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
     * Adds a column name to the index.
     *
     * @param string $columnName The column name to add.
     */
    public function addColumnName($columnName)
    {
        if (!is_string($columnName) || (strlen($columnName) <= 0)) {
            throw SchemaException::invalidIndexColumnName($this->getName());
        }

        $this->columnNames[] = $columnName;
    }

    /**
     * Checks if the index is unique.
     *
     * @return boolean TRUE if the index is unique else FALSE.
     */
    public function isUnique()
    {
        return $this->unique;
    }

    /**
     * Sets the unique index flag.
     *
     * @param boolean $unique TRUE if the index is unique else FALSE.
     */
    public function setUnique($unique)
    {
        if (!is_bool($unique)) {
            throw SchemaException::invalidIndexUniqueFlag($this->getName());
        }

        $this->unique = $unique;
    }

    /**
     * Checks if the index column names are exactly equals to the given (count & order).
     *
     * @param array $columnNames The column names.
     *
     * @return boolean TRUE if if the index column names are exactly equals to the given else FALSE.
     */
    public function hasSameColumnNames(array $columnNames)
    {
        if (count($columnNames) !== count($this->getColumnNames())) {
            return false;
        }

        foreach ($columnNames as $i => $columnName) {
            if (!isset($this->columnNames[$i]) || ($columnName !== $this->columnNames[$i])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if the index is better than the given index.
     * Better means the index can replace the given.
     *
     * @param Fridge\DBAL\Schema\Index $index The candidate index.
     *
     * @return boolean TRUE if the index is better than the given else FALSE.
     */
    public function isBetterThan(Index $index)
    {
        if ($this->hasSameColumnNames($index->getColumnNames()) && $this->isUnique() && !$index->isUnique()) {
            return true;
        }

        return false;
    }
}
