<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\SchemaManager\SQLCollector;

use Fridge\DBAL\Platform\PlatformInterface,
    Fridge\DBAL\Schema\Diff\TableDiff;

/**
 * Collects queries to alter tables.
 *
 * The queries order are:
 *  - Rename tables.
 *  - Drop indexes.
 *  - Drop foreign keys.
 *  - Drop primary keys.
 *  - Drop columns.
 *  - Alter columns.
 *  - Create columns.
 *  - Create primary keys.
 *  - Create indexes.
 *  - Create foreign keys.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class AlterTableSQLCollector
{
    /** @var \Fridge\DBAL\Platform\PlatformInterface */
    protected $platform;

    /** @var array */
    protected $renameTableQueries;

    /** @var array */
    protected $dropIndexQueries;

    /** @var array */
    protected $dropForeignKeyQueries;

    /** @var array */
    protected $dropPrimaryKeyQueries;

    /** @var array */
    protected $dropColumnQueries;

    /** @var array */
    protected $alterColumnQueries;

    /** @var array */
    protected $createColumnQueries;

    /** @var array */
    protected $createPrimaryKeyQueries;

    /** @var array */
    protected $createIndexQueries;

    /** @var array */
    protected $createForeignKeyQueries;

    /**
     * Alter table SQL collector constructor.
     *
     * @param \Fridge\DBAL\Platform\PlatformInterface $platform The platform used to collect queries.
     */
    public function __construct(PlatformInterface $platform)
    {
        $this->setPlatform($platform);
    }

    /**
     * Gets the platform used to collect queries..
     *
     * @return \Fridge\DBAL\Platform\PlatformInterface The platform used to collect queries..
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * Sets the platform used to collect queries.
     *
     * @param \Fridge\DBAL\Platform\PlatformInterface $this->platform
     */
    public function setPlatform(PlatformInterface $platform)
    {
        $this->platform = $platform;

        $this->init();
    }

    /**
     * Reinitilizes SQL collector.
     */
    public function init()
    {
        $this->renameTableQueries = array();
        $this->dropIndexQueries = array();
        $this->dropForeignKeyQueries = array();
        $this->dropPrimaryKeyQueries = array();
        $this->dropColumnQueries = array();
        $this->alterColumnQueries = array();
        $this->createColumnQueries = array();
        $this->createPrimaryKeyQueries = array();
        $this->createIndexQueries = array();
        $this->createForeignKeyQueries = array();
    }

    /**
     * Collects queries to alter tables.
     *
     * @param \Fridge\DBAL\Schema\Diff\TableDiff $tableDiff The table difference.
     */
    public function collect(TableDiff $tableDiff)
    {
        if ($tableDiff->getOldName() !== $tableDiff->getNewName()) {
            $this->renameTableQueries[] = $this->platform->getRenameTableSQLQuery($tableDiff);
        }

        $this->collectColumns($tableDiff);
        $this->collectPrimaryKeys($tableDiff);
        $this->collectForeignKeys($tableDiff);
        $this->collectIndexes($tableDiff);
    }

    /**
     * Gets the rename table queries.
     *
     * @return array The rename table queries.
     */
    public function getRenameTableQueries()
    {
        return $this->renameTableQueries;
    }

    /**
     * Gets the drop index queries.
     *
     * @return array The drop index queries.
     */
    public function getDropIndexQueries()
    {
        return $this->dropIndexQueries;
    }

    /**
     * Gets the drop foreign key queries.
     *
     * @return array The drop foreign key queries.
     */
    public function getDropForeignKeyQueries()
    {
        return $this->dropForeignKeyQueries;
    }

    /**
     * Gets the drop primary key queries.
     *
     * @return array The drop primary key queries.
     */
    public function getDropPrimaryKeyQueries()
    {
        return $this->dropPrimaryKeyQueries;
    }

    /**
     * Gets the drop column queries.
     *
     * @return array The drop column queries.
     */
    public function getDropColumnQueries()
    {
        return $this->dropColumnQueries;
    }

    /**
     * Gets the alter column queries.
     *
     * @return array The alter column queries.
     */
    public function getAlterColumnQueries()
    {
        return $this->alterColumnQueries;
    }

    /**
     * Gets the create column queries.
     *
     * @return array The create column queries.
     */
    public function getCreateColumnQueries()
    {
        return $this->createColumnQueries;
    }

    /**
     * Gets the create primary key queries.
     *
     * @return array The create primary key queries.
     */
    public function getCreatePrimaryKeyQueries()
    {
        return $this->createPrimaryKeyQueries;
    }

    /**
     * Gets the create index queries.
     *
     * @return array The create index queries.
     */
    public function getCreateIndexQueries()
    {
        return $this->createIndexQueries;
    }

    /**
     * Gets the create foreign key queries.
     *
     * @return array The create foreign key queries.
     */
    public function getCreateForeignKeyQueries()
    {
        return $this->createForeignKeyQueries;
    }

    /**
     * Gets the queries collected to alter the table.
     *
     * @return array The queries collected to alter the table.
     */
    public function getQueries()
    {
        return array_merge(
            $this->getRenameTableQueries(),
            $this->getDropIndexQueries(),
            $this->getDropForeignKeyQueries(),
            $this->getDropPrimaryKeyQueries(),
            $this->getDropColumnQueries(),
            $this->getAlterColumnQueries(),
            $this->getCreateColumnQueries(),
            $this->getCreatePrimaryKeyQueries(),
            $this->getCreateIndexQueries(),
            $this->getCreateForeignKeyQueries()
        );
    }

    /**
     * Collects queries about column to alter a table.
     *
     * @param \Fridge\DBAL\Schema\Diff\TableDiff $tableDiff The table difference.
     */
    protected function collectColumns(TableDiff $tableDiff)
    {
        foreach ($tableDiff->getCreatedColumns() as $column) {
            $this->createColumnQueries = array_merge(
                $this->createColumnQueries,
                $this->platform->getCreateColumnSQLQueries($column, $tableDiff->getNewName())
            );
        }

        foreach ($tableDiff->getDroppedColumns() as $column) {
            $this->dropColumnQueries[] = $this->platform->getDropColumnSQLQuery($column, $tableDiff->getNewName());
        }

        foreach ($tableDiff->getAlteredColumns() as $columnDiff) {
            $this->alterColumnQueries = array_merge(
                $this->alterColumnQueries,
                $this->platform->getRenameColumnSQLQueries($columnDiff, $tableDiff->getNewName())
            );
        }
    }

    /**
     * Collect queries about primary keys to alter a table.
     *
     * @param \Fridge\DBAL\Schema\Diff\TableDiff $tableDiff The table difference.
     */
    protected function collectPrimaryKeys(TableDiff $tableDiff)
    {
        if ($tableDiff->getCreatedPrimaryKey() !== null) {
            $this->createPrimaryKeyQueries[] = $this->platform->getCreatePrimaryKeySQLQuery(
                $tableDiff->getCreatedPrimaryKey(),
                $tableDiff->getNewName()
            );
        }

        if ($tableDiff->getDroppedPrimaryKey() !== null) {
            $this->dropPrimaryKeyQueries[] = $this->platform->getDropPrimaryKeySQLQuery(
                $tableDiff->getDroppedPrimaryKey(),
                $tableDiff->getNewName()
            );
        }
    }

    /**
     * Collects queries about foreign keys to alter a table.
     *
     * @param \Fridge\DBAL\Schema\Diff\TableDiff $tableDiff The table difference.
     */
    protected function collectForeignKeys(TableDiff $tableDiff)
    {
        foreach ($tableDiff->getCreatedForeignKeys() as $foreignKey) {
            $this->createForeignKeyQueries[] = $this->platform->getCreateForeignKeySQLQuery(
                $foreignKey,
                $tableDiff->getNewName()
            );
        }

        foreach ($tableDiff->getDroppedForeignKeys() as $foreignKey) {
            $this->dropForeignKeyQueries[] = $this->platform->getDropForeignKeySQLQuery(
                $foreignKey,
                $tableDiff->getNewName()
            );
        }
    }

    /**
     * Collects queries about indexes to alter a table.
     *
     * @param \Fridge\DBAL\Schema\Diff\TableDiff $tableDiff The table difference.
     */
    protected function collectIndexes(TableDiff $tableDiff)
    {
        foreach ($tableDiff->getCreatedIndexes() as $index) {
            $this->createIndexQueries[] = $this->platform->getCreateIndexSQLQuery($index, $tableDiff->getNewName());
        }

        foreach ($tableDiff->getDroppedIndexes() as $index) {
            $this->dropIndexQueries[] = $this->platform->getDropIndexSQLQuery($index, $tableDiff->getNewName());
        }
    }
}
