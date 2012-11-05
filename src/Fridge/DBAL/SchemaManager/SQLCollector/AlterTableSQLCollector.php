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
 *  - Drop checks.
 *  - Drop foreign keys.
 *  - Drop indexes.
 *  - Drop primary keys.
 *  - Drop columns.
 *  - Alter columns.
 *  - Create columns.
 *  - Create primary keys.
 *  - Create indexes.
 *  - Create foreign keys.
 *  - Create checks.
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
    protected $dropCheckQueries;

    /** @var array */
    protected $dropForeignKeyQueries;

    /** @var array */
    protected $dropIndexQueries;

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

    /** @var array */
    protected $createCheckQueries;

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
     * @param \Fridge\DBAL\Platform\PlatformInterface $platform The platform used to collect queries.
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
        $this->dropCheckQueries = array();
        $this->dropForeignKeyQueries = array();
        $this->dropIndexQueries = array();
        $this->dropPrimaryKeyQueries = array();
        $this->dropColumnQueries = array();
        $this->alterColumnQueries = array();
        $this->createColumnQueries = array();
        $this->createPrimaryKeyQueries = array();
        $this->createIndexQueries = array();
        $this->createForeignKeyQueries = array();
        $this->createCheckQueries = array();
    }

    /**
     * Collects queries to alter tables.
     *
     * @param \Fridge\DBAL\Schema\Diff\TableDiff $tableDiff The table difference.
     */
    public function collect(TableDiff $tableDiff)
    {
        if ($tableDiff->getOldAsset()->getName() !== $tableDiff->getNewAsset()->getName()) {
            $this->renameTableQueries[] = $this->platform->getRenameTableSQLQuery($tableDiff);
        }

        $this->collectColumns($tableDiff);
        $this->collectPrimaryKeys($tableDiff);
        $this->collectForeignKeys($tableDiff);
        $this->collectIndexes($tableDiff);
        $this->collectChecks($tableDiff);
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
     * Gets the drop check queries.
     *
     * @return array The drop check queries.
     */
    public function getDropCheckQueries()
    {
        return $this->dropCheckQueries;
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
     * Gets the drop index queries.
     *
     * @return array The drop index queries.
     */
    public function getDropIndexQueries()
    {
        return $this->dropIndexQueries;
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
     * Gets the create check queries.
     *
     * @return array The create check queries.
     */
    public function getCreateCheckQueries()
    {
        return $this->createCheckQueries;
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
            $this->getDropCheckQueries(),
            $this->getDropForeignKeyQueries(),
            $this->getDropIndexQueries(),
            $this->getDropPrimaryKeyQueries(),
            $this->getDropColumnQueries(),
            $this->getAlterColumnQueries(),
            $this->getCreateColumnQueries(),
            $this->getCreatePrimaryKeyQueries(),
            $this->getCreateIndexQueries(),
            $this->getCreateForeignKeyQueries(),
            $this->getCreateCheckQueries()
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
                $this->platform->getCreateColumnSQLQueries($column, $tableDiff->getNewAsset()->getName())
            );
        }

        foreach ($tableDiff->getDroppedColumns() as $column) {
            $this->dropColumnQueries[] = $this->platform->getDropColumnSQLQuery(
                $column,
                $tableDiff->getNewAsset()->getName()
            );
        }

        foreach ($tableDiff->getAlteredColumns() as $columnDiff) {
            $this->alterColumnQueries = array_merge(
                $this->alterColumnQueries,
                $this->platform->getAlterColumnSQLQueries($columnDiff, $tableDiff->getNewAsset()->getName())
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
            $this->createPrimaryKeyQueries = array_merge(
                $this->createPrimaryKeyQueries,
                $this->platform->getCreatePrimaryKeySQLQueries(
                    $tableDiff->getCreatedPrimaryKey(),
                    $tableDiff->getNewAsset()->getName()
                )
            );
        }

        if ($tableDiff->getDroppedPrimaryKey() !== null) {
            $this->dropPrimaryKeyQueries[] = $this->platform->getDropPrimaryKeySQLQuery(
                $tableDiff->getDroppedPrimaryKey(),
                $tableDiff->getNewAsset()->getName()
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
            $this->createForeignKeyQueries = array_merge(
                $this->createForeignKeyQueries,
                $this->platform->getCreateForeignKeySQLQueries(
                    $foreignKey,
                    $tableDiff->getNewAsset()->getName()
                )
            );
        }

        foreach ($tableDiff->getDroppedForeignKeys() as $foreignKey) {
            $this->dropForeignKeyQueries[] = $this->platform->getDropForeignKeySQLQuery(
                $foreignKey,
                $tableDiff->getNewAsset()->getName()
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
            $this->createIndexQueries = array_merge(
                $this->createIndexQueries,
                $this->platform->getCreateIndexSQLQueries(
                    $index,
                    $tableDiff->getNewAsset()->getName()
                )
            );
        }

        foreach ($tableDiff->getDroppedIndexes() as $index) {
            $this->dropIndexQueries[] = $this->platform->getDropIndexSQLQuery(
                $index,
                $tableDiff->getNewAsset()->getName()
            );
        }
    }

    /**
     * Collects queries about checks to alter a table.
     *
     * @param \Fridge\DBAL\Schema\Diff\TableDiff $tableDiff The table difference.
     */
    protected function collectChecks(TableDiff $tableDiff)
    {
        foreach ($tableDiff->getCreatedChecks() as $check) {
            $this->createCheckQueries[] = $this->platform->getCreateCheckSQLQuery(
                $check,
                $tableDiff->getNewAsset()->getName()
            );
        }

        foreach ($tableDiff->getDroppedChecks() as $check) {
            $this->dropCheckQueries[] = $this->platform->getDropCheckSQLQuery(
                $check,
                $tableDiff->getNewAsset()->getName()
            );
        }
    }
}
