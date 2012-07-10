<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Schema\Comparator;

use Fridge\DBAL\Schema;

/**
 * Table comparator.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class TableComparator
{
    /** @var \Fridge\DBAL\Schema\Comparator\ColumnComparator */
    protected $columnComparator;

    /**
     * Table comparator constructor.
     */
    public function __construct()
    {
        $this->columnComparator = new ColumnComparator();
    }

    /**
     * Compares two tables.
     *
     * @param \Fridge\DBAL\Schema\Table $oldTable The old table.
     * @param \Fridge\DBAL\Schema\Table $newTable The new table.
     *
     * @return \Fridge\DBAL\Schema\Diff\TableDiff The difference between the two tables.
     */
    public function compare(Schema\Table $oldTable, Schema\Table $newTable)
    {
        return new Schema\Diff\TableDiff(
            $oldTable->getName(),
            $newTable->getName(),
            $this->getCreatedColumns($oldTable, $newTable),
            $this->getAlteredColumns($oldTable, $newTable),
            $this->getDroppedColumns($oldTable, $newTable),
            $this->getCreatedPrimaryKey($oldTable, $newTable),
            $this->getDroppedPrimaryKey($oldTable, $newTable),
            $this->getCreatedForeignKeys($oldTable, $newTable),
            $this->getDroppedForeignKeys($oldTable, $newTable),
            $this->getCreatedIndexes($oldTable, $newTable),
            $this->getDroppedIndexes($oldTable, $newTable)
        );
    }

    /**
     * Compares two primary keys.
     *
     * @param \Fridge\DBAL\Schema\PrimaryKey $oldPrimaryKey The old primary key.
     * @param \Fridge\DBAL\Schema\PrimaryKey $newPrimaryKey The new primary key.
     *
     * @return boolean TRUE if the primary keys have difference else FALSE.
     */
    public function comparePrimaryKeys(Schema\PrimaryKey $oldPrimaryKey, Schema\PrimaryKey $newPrimaryKey)
    {
        return ($oldPrimaryKey->getName() !== $newPrimaryKey->getName())
            || ($oldPrimaryKey->getColumnNames() !== $newPrimaryKey->getColumnNames());
    }

    /**
     * Compares two foreign keys.
     *
     * @param \Fridge\DBAL\Schema\ForeignKey $oldForeignKey The old foreign key.
     * @param \Fridge\DBAL\Schema\ForeignKey $newForeignKey The new foreign key.
     *
     * @return boolean TRUE if foreign keys have difference else FALSE.
     */
    public function compareForeignKeys(Schema\ForeignKey $oldForeignKey, Schema\ForeignKey $newForeignKey)
    {
        return ($oldForeignKey->getName() !== $newForeignKey->getName())
            || ($oldForeignKey->getLocalColumnNames() !== $newForeignKey->getLocalColumnNames())
            || ($oldForeignKey->getForeignTableName() !== $newForeignKey->getForeignTableName())
            || ($oldForeignKey->getForeignColumnNames() !== $newForeignKey->getForeignColumnNames());
    }

    /**
     * Compares two indexes.
     *
     * @param \Fridge\DBAL\Schema\Index $oldIndex The old index.
     * @param \Fridge\DBAL\Schema\Index $newIndex The new index.
     *
     * @return boolean TRUE if indexes have difference else FALSE.
     */
    public function compareIndexes(Schema\Index $oldIndex, Schema\Index $newIndex)
    {
        return ($oldIndex->getName() !== $newIndex->getName())
            || ($oldIndex->getColumnNames() !== $newIndex->getColumnNames())
            || ($oldIndex->isUnique() !== $newIndex->isUnique());
    }

    /**
     * Gets the created columns according to the old/new tables.
     *
     * @param \Fridge\DBAL\Schema\Table $oldTable The old table.
     * @param \Fridge\DBAL\Schema\Table $newTable The new tabme.
     *
     * @return array The created columns.
     */
    protected function getCreatedColumns(Schema\Table $oldTable, Schema\Table $newTable)
    {
        $createdColumns = array();

        foreach ($newTable->getColumns() as $column) {
            if (!$oldTable->hasColumn($column->getName())) {
                $createdColumns[] = $column;
            }
        }

        return $createdColumns;
    }

    /**
     * Gets the altered columns according to the old/new tables.
     *
     * @param \Fridge\DBAL\Schema\Table $oldTable The old table.
     * @param \Fridge\DBAL\Schema\Table $newTable The new table.
     *
     * @return array The altered columns
     */
    protected function getAlteredColumns(Schema\Table $oldTable, Schema\Table $newTable)
    {
        $alteredColumns = array();

        foreach ($newTable->getColumns() as $column) {
            if ($oldTable->hasColumn($column->getName())) {
                $columnDiff = $this->columnComparator->compare($oldTable->getColumn($column->getName()), $column);

                if ($columnDiff->hasDifference()) {
                    $alteredColumns[] = $columnDiff;
                }
            }
        }

        return $alteredColumns;
    }

    /**
     * Gets the dropped columns according to the old/new tables.
     *
     * @param \Fridge\DBAL\Schema\Table $oldTable The old table.
     * @param \Fridge\DBAL\Schema\Table $newTable The new table.
     *
     * @return array The dropped columns.
     */
    protected function getDroppedColumns(Schema\Table $oldTable, Schema\Table $newTable)
    {
        $droppedColumns = array();

        foreach ($oldTable->getColumns() as $column) {
            if (!$newTable->hasColumn($column->getName())) {
                $droppedColumns[] = $column;
            }
        }

        return $droppedColumns;
    }

    /**
     * Gets the created primary key according to the old/new tables.
     *
     * @param \Fridge\DBAL\Schema\Table $oldTable The old table.
     * @param \Fridge\DBAL\Schema\Table $newTable The new table.
     *
     * @return \Fridge\DBAL\Schema\PrimaryKey|null The created primary key.
     */
    protected function getCreatedPrimaryKey(Schema\Table $oldTable, Schema\Table $newTable)
    {
        if ((!$oldTable->hasPrimaryKey() && $newTable->hasPrimaryKey())
            || ($oldTable->hasPrimaryKey() && $newTable->hasPrimaryKey()
            && $this->comparePrimaryKeys($oldTable->getPrimaryKey(), $newTable->getPrimaryKey()))) {
            return $newTable->getPrimaryKey();
        }
    }

    /**
     * Gets the dropped primary key according to the old/new tables.
     *
     * @param \Fridge\DBAL\Schema\Table $oldTable The old table.
     * @param \Fridge\DBAL\Schema\Table $newTable The new table.
     *
     * @return \Fridge\DBAL\Schema\PrimaryKey|null The dropped primary key.
     */
    protected function getDroppedPrimaryKey(Schema\Table $oldTable, Schema\Table $newTable)
    {
        if (($oldTable->hasPrimaryKey() && !$newTable->hasPrimaryKey())
            || ($oldTable->hasPrimaryKey() && $newTable->hasPrimaryKey()
            && $this->comparePrimaryKeys($oldTable->getPrimaryKey(), $newTable->getPrimaryKey()))) {
            return $oldTable->getPrimaryKey();
        }
    }

    /**
     * Gets the created foreign keys according to the old/new tables.
     *
     * @param \Fridge\DBAL\Schema\Table $oldTable The old table.
     * @param \Fridge\DBAL\Schema\Table $newTable The new table.
     *
     * @return array The created foreign keys.
     */
    protected function getCreatedForeignKeys(Schema\Table $oldTable, Schema\Table $newTable)
    {
        $createdForeignKeys = array();

        foreach ($newTable->getForeignKeys() as $foreignKey) {
            if (!$oldTable->hasForeignKey($foreignKey->getName())
                || $this->compareForeignKeys($oldTable->getForeignKey($foreignKey->getName()), $foreignKey)) {
                $createdForeignKeys[] = $foreignKey;
            }
        }

        return $createdForeignKeys;
    }

    /**
     * Gets the dropped foreign keys according to the old/new tables.
     *
     * @param \Fridge\DBAL\Schema\Table $oldTable The old table.
     * @param \Fridge\DBAL\Schema\Table $newTable The new table.
     *
     * @return array The dropped foreign keys.
     */
    protected function getDroppedForeignKeys(Schema\Table $oldTable, Schema\Table $newTable)
    {
        $droppedForeignKeys = array();

        foreach ($oldTable->getForeignKeys() as $foreignKey) {
            if (!$newTable->hasForeignKey($foreignKey->getName())
                || $this->compareForeignKeys($foreignKey, $newTable->getForeignKey($foreignKey->getName()))) {
                $droppedForeignKeys[] = $foreignKey;
            }
        }

        return $droppedForeignKeys;
    }

    /**
     * Gets the created indexes according to the old/new tables.
     *
     * @param \Fridge\DBAL\Schema\Table $oldTable The old table.
     * @param \Fridge\DBAL\Schema\Table $newTable The new table.
     *
     * @return array The crated indexes.
     */
    protected function getCreatedIndexes(Schema\Table $oldTable, Schema\Table $newTable)
    {
        $createdIndexes = array();

        foreach ($newTable->getIndexes() as $index) {
            if (!$oldTable->hasIndex($index->getName())
                || $this->compareIndexes($oldTable->getIndex($index->getName()), $index)) {
                $createdIndexes[] = $index;
            }
        }

        return $createdIndexes;
    }

    /**
     * Gets the dropped indexes according to the old/new tables.
     *
     * @param \Fridge\DBAL\Schema\Table $oldTable The old table.
     * @param \Fridge\DBAL\Schema\Table $newTable The new table.
     *
     * @return array The dropped indexes.
     */
    protected function getDroppedIndexes(Schema\Table $oldTable, Schema\Table $newTable)
    {
        $droppedIndexes = array();

        foreach ($oldTable->getIndexes() as $index) {
            if (!$newTable->hasIndex($index->getName())
                || $this->compareIndexes($index, $newTable->getIndex($index->getName()))) {
                $droppedIndexes[] = $index;
            }
        }

        return $droppedIndexes;
    }
}
