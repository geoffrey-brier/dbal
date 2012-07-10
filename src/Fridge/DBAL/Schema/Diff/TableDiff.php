<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Schema\Diff;

use Fridge\DBAL\Schema;

/**
 * Describes the difference between to two tables.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class TableDiff extends AbstractAssetDiff
{
    /** @var array */
    protected $createdColumns;

    /** @var array */
    protected $alteredColumns;

    /** @var array */
    protected $droppedColumns;

    /** @var \Fridge\DBAL\Schema\PrimaryKey */
    protected $createdPrimaryKey;

    /** @var \Fridge\DBAL\Schema\PrimaryKey */
    protected $droppedPrimaryKey;

    /** @var array */
    protected $createdForeignKeys;

    /** @var array */
    protected $droppedForeignKeys;

    /** @var array */
    protected $createdIndexes;

    /** @var array */
    protected $droppedIndexes;

    /** @var array */
    protected $createdChecks;

    /** @var array */
    protected $droppedChecks;

    /**
     * Table diff constructor.
     *
     * @param \Fridge\DBAL\Schema\Table      $oldTable           The old table.
     * @param \Fridge\DBAL\Schema\Table      $newTable           The new table.
     * @param array                          $createdColumns     The created table columns.
     * @param array                          $alteredColumns     The altered table columns.
     * @param array                          $droppedColumns     The dropped table columns.
     * @param \Fridge\DBAL\Schema\PrimaryKey $createdPrimaryKey  The created table primary key.
     * @param \Fridge\DBAL\Schema\PrimaryKey $droppedPrimaryKey  The dropped table primary key.
     * @param array                          $createdForeignKeys The created table foreign keys.
     * @param array                          $droppedForeignKeys The dropped table foreign keys.
     * @param array                          $createdIndexes     The created table indexes.
     * @param array                          $droppedIndexes     The dropped table indexes.
     * @param array                          $createdChecks      The created table checks.
     * @param array                          $droppedChecks      The dropped table checks.
     */
    public function __construct(
        Schema\Table $oldTable,
        Schema\Table $newTable,
        array $createdColumns = array(),
        array $alteredColumns = array(),
        array $droppedColumns = array(),
        Schema\PrimaryKey $createdPrimaryKey = null,
        Schema\PrimaryKey $droppedPrimaryKey = null,
        array $createdForeignKeys = array(),
        array $droppedForeignKeys = array(),
        array $createdIndexes = array(),
        array $droppedIndexes = array(),
        array $createdChecks = array(),
        array $droppedChecks = array()
    )
    {
        parent::__construct($oldTable, $newTable);

        $this->createdColumns = $createdColumns;
        $this->alteredColumns = $alteredColumns;
        $this->droppedColumns = $droppedColumns;

        $this->createdPrimaryKey = $createdPrimaryKey;
        $this->droppedPrimaryKey = $droppedPrimaryKey;

        $this->createdForeignKeys = $createdForeignKeys;
        $this->droppedForeignKeys = $droppedForeignKeys;

        $this->createdIndexes = $createdIndexes;
        $this->droppedIndexes = $droppedIndexes;

        $this->createdChecks = $createdChecks;
        $this->droppedChecks = $droppedChecks;
    }

    /**
     * Gets the created columns
     *
     * @return array The created columns.
     */
    public function getCreatedColumns()
    {
        return $this->createdColumns;
    }

    /**
     * Gets the altered columns.
     *
     * @return array The altered columns.
     */
    public function getAlteredColumns()
    {
        return $this->alteredColumns;
    }

    /**
     * Gets the dropped columns.
     *
     * @return array The dropped columns.
     */
    public function getDroppedColumns()
    {
        return $this->droppedColumns;
    }

    /**
     * Gets the created primary key.
     *
     * @return \Fridge\DBAL\Schema\PrimaryKey|null The created primary key.
     */
    public function getCreatedPrimaryKey()
    {
        return $this->createdPrimaryKey;
    }

    /**
     * Gets the dropped primary key.
     *
     * @return \Fridge\DBAL\Schema\PrimaryKey|null The dropped primary key.
     */
    public function getDroppedPrimaryKey()
    {
        return $this->droppedPrimaryKey;
    }

    /**
     * Gets the created foreign keys.
     *
     * @return array The created foreign keys.
     */
    public function getCreatedForeignKeys()
    {
        return $this->createdForeignKeys;
    }

    /**
     * Gets the dropped foreign keys.
     *
     * @return array The dropped foreign keys.
     */
    public function getDroppedForeignKeys()
    {
        return $this->droppedForeignKeys;
    }

    /**
     * Gets the created indexes.
     *
     * @return array The created indexes.
     */
    public function getCreatedIndexes()
    {
        return $this->createdIndexes;
    }

    /**
     * Gets the dropped indexes.
     *
     * @return array The dropped indexes.
     */
    public function getDroppedIndexes()
    {
        return $this->droppedIndexes;
    }

    /**
     * Gets the created checks.
     *
     * @return array The created checks.
     */
    public function getCreatedChecks()
    {
        return $this->createdChecks;
    }

    /**
     * Gets the dropped checks.
     *
     * @return array The dropped checks.
     */
    public function getDroppedChecks()
    {
        return $this->droppedChecks;
    }

    /**
     * {@inheritdoc}
     */
    public function hasDifference()
    {
        return parent::hasDifference()
            || $this->hasColumnDifference()
            || $this->hasPrimaryKeyDifference()
            || $this->hasForeignKeyDifference()
            || $this->hasIndexDifference()
            || $this->hasCheckDifference();
    }

    /**
     * {@inheritdoc}
     */
    public function hasNameDifferenceOnly()
    {
        return parent::hasNameDifferenceOnly()
            && !$this->hasColumnDifference()
            && !$this->hasPrimaryKeyDifference()
            && !$this->hasForeignKeyDifference()
            && !$this->hasIndexDifference()
            && !$this->hasCheckDifference();
    }

    /**
     * Checks if the table diff has column difference.
     *
     * @return boolean TRUE if the table has column difference else FALSE.
     */
    protected function hasColumnDifference()
    {
        return !empty($this->createdColumns) || !empty($this->alteredColumns) || !empty($this->droppedColumns);
    }

    /**
     * Checks if the table diff has primary key difference.
     *
     * @return boolean TRUE if the table diff has primary key difference else FALSE.
     */
    protected function hasPrimaryKeyDifference()
    {
        return ($this->createdPrimaryKey !== null) || ($this->droppedPrimaryKey !== null);
    }

    /**
     * Checks if the table diff has foreign key difference.
     *
     * @return boolean TRUE if the table diff has foreign key difference else FALSE.
     */
    protected function hasForeignKeyDifference()
    {
        return !empty($this->createdForeignKeys) || !empty($this->droppedForeignKeys);
    }

    /**
     * Checks if the table diff has index difference.
     *
     * @return boolean TRUE if the table diff has index difference else FALSE.
     */
    protected function hasIndexDifference()
    {
        return !empty($this->createdIndexes) || !empty($this->droppedIndexes);
    }

    /**
     * Checks if the table diff has check difference.
     *
     * @return boolean TRUE if the table diff has check difference else FALSE.
     */
    protected function hasCheckDifference()
    {
        return !empty($this->createdChecks) || !empty($this->droppedChecks);
    }
}
