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
    Fridge\DBAL\Schema\Diff\SchemaDiff;

/**
 * Collects queries to alter a schemma.
 *
 * The queries order are:
 *  - Drop sequences.
 *  - Drop views.
 *  - Rename tables.
 *  - Drop checks.
 *  - Drop foreign keys.
 *  - Drop indexes.
 *  - Drop primary keys.
 *  - Drop tables.
 *  - Drop columns.
 *  - Alter columns.
 *  - Create columns.
 *  - Create tables without foreign keys.
 *  - Create primary keys.
 *  - Create indexes.
 *  - Create foreign keys.
 *  - Create checks.
 *  - Create views.
 *  - Create sequences.
 *  - Rename schemas.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class AlterSchemaSQLCollector
{
    /** @var \Fridge\DBAL\Platform\PlatformInterface */
    protected $platform;

    /** @var \Fridge\DBAL\SchemaManager\SQLCollector\CreateTableSQLCollector */
    protected $createTableSQLCollector;

    /** @var \Fridge\DBAL\SchemaManager\SQLCollector\DropTableSQLCollector */
    protected $dropTableSQLCollector;

    /** @var \Fridge\DBAL\SchemaManager\SQLCollector\AlterTableSQLCollector */
    protected $alterTableSQLCollector;

    /** @var array */
    protected $dropSequenceQueries;

    /** @var array */
    protected $dropViewQueries;

    /** @var array */
    protected $createViewQueries;

    /** @var array */
    protected $createSequenceQueries;

    /** @var array */
    protected $renameSchemaQueries;

    /**
     * ALter schema SQL collector constructor.
     *
     * @param \Fridge\DBAL\Platform\PlatformInterface $platform The platform used to collect queries.
     */
    public function __construct(PlatformInterface $platform)
    {
        $this->createTableSQLCollector = new CreateTableSQLCollector($platform);
        $this->dropTableSQLCollector = new DropTableSQLCollector($platform);
        $this->alterTableSQLCollector = new AlterTableSQLCollector($platform);

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

        $this->createTableSQLCollector->setPlatform($platform);
        $this->dropTableSQLCollector->setPlatform($platform);
        $this->alterTableSQLCollector->setPlatform($platform);

        $this->init();
    }

    /**
     * Reinitializes the SQL collector.
     */
    public function init()
    {
        $this->createTableSQLCollector->init();
        $this->dropTableSQLCollector->init();
        $this->alterTableSQLCollector->init();

        $this->dropSequenceQueries = array();
        $this->dropViewQueries = array();
        $this->createViewQueries = array();
        $this->createSequenceQueries = array();
        $this->renameSchemaQueries = array();
    }

    /**
     * Collects queries to alter a schema.
     *
     * @param \Fridge\DBAL\Schema\Diff\SchemaDiff $schemaDiff The schema difference.
     */
    public function collect(SchemaDiff $schemaDiff)
    {
        if ($schemaDiff->getOldAsset()->getName() !== $schemaDiff->getNewAsset()->getName()) {
            $this->renameSchemaQueries = array_merge(
                $this->renameSchemaQueries,
                $this->platform->getRenameDatabaseSQLQueries($schemaDiff)
            );
        }

        $this->collectTables($schemaDiff);
        $this->collectSequences($schemaDiff);
        $this->collectViews($schemaDiff);
    }

    /**
     * Gets the drop sequence queries.
     *
     * @return array The drop sequence queries.
     */
    public function getDropSequenceQueries()
    {
        return $this->dropSequenceQueries;
    }

    /**
     * Gets the drop view queries.
     *
     * @return array The drop view queries.
     */
    public function getDropViewQueries()
    {
        return $this->dropViewQueries;
    }

    /**
     * Gets the renam table queries.
     *
     * @return array The rename table queries.
     */
    public function getRenameTableQueries()
    {
        return $this->alterTableSQLCollector->getRenameTableQueries();
    }

    /**
     * Gets the drop check queries.
     *
     * @return array The drop check queries.
     */
    public function getDropCheckQueries()
    {
        return $this->alterTableSQLCollector->getDropCheckQueries();
    }

    /**
     * Gets the drop foreign key queries.
     *
     * @return array The drop foreign key queries.
     */
    public function getDropForeignKeyQueries()
    {
        return array_merge(
            $this->dropTableSQLCollector->getDropForeignKeyQueries(),
            $this->alterTableSQLCollector->getDropForeignKeyQueries()
        );
    }

    /**
     * Gets the drop index queries.
     *
     * @return array The drop index queries.
     */
    public function getDropIndexQueries()
    {
        return $this->alterTableSQLCollector->getDropIndexQueries();
    }

    /**
     * Gets the drop primary key queries.
     *
     * @return array The drop primary key queries.
     */
    public function getDropPrimaryKeyQueries()
    {
        return $this->alterTableSQLCollector->getDropPrimaryKeyQueries();
    }

    /**
     * Gets the drop table queries.
     *
     * @return array The drop table queries.
     */
    public function getDropTableQueries()
    {
        return $this->dropTableSQLCollector->getDropTableQueries();
    }

    /**
     * Gets the drop column queries.
     *
     * @return array The drop column queries.
     */
    public function getDropColumnQueries()
    {
        return $this->alterTableSQLCollector->getDropColumnQueries();
    }

    /**
     * Gets the alter column queries.
     *
     * @return array The alter column queries.
     */
    public function getAlterColumnQueries()
    {
        return $this->alterTableSQLCollector->getAlterColumnQueries();
    }

    /**
     * Gets the create column queries.
     *
     * @return array The create column queries.
     */
    public function getCreateColumnQueries()
    {
        return $this->alterTableSQLCollector->getCreateColumnQueries();
    }

    /**
     * Gets the create table queries without foreign keys.
     *
     * @return array The create table queries without foreign keys.
     */
    public function getCreateTableQueries()
    {
        return $this->createTableSQLCollector->getCreateTableQueries();
    }

    /**
     * Gets the create primary key queries.
     *
     * @return array The create primary key queries.
     */
    public function getCreatePrimaryKeyQueries()
    {
        return $this->alterTableSQLCollector->getCreatePrimaryKeyQueries();
    }

    /**
     * Gets the create index queries.
     *
     * @return array The create index queries.
     */
    public function getCreateIndexQueries()
    {
        return $this->alterTableSQLCollector->getCreateIndexQueries();
    }

    /**
     * Gets the create foreign key queries.
     *
     * @return array The create foreign key queries.
     */
    public function getCreateForeignKeyQueries()
    {
        return array_merge(
            $this->createTableSQLCollector->getCreateForeignKeyQueries(),
            $this->alterTableSQLCollector->getCreateForeignKeyQueries()
        );
    }

    /**
     * Gets the create check queries.
     *
     * @return array The create check queries.
     */
    public function getCreateCheckQueries()
    {
        return $this->alterTableSQLCollector->getCreateCheckQueries();
    }

    /**
     * Gets the create view queries.
     *
     * @return array The create view queries.
     */
    public function getCreateViewQueries()
    {
        return $this->createViewQueries;
    }

    /**
     * Gets the create sequence queries.
     *
     * @return array The create sequence queries.
     */
    public function getCreateSequenceQueries()
    {
        return $this->createSequenceQueries;
    }

    /**
     * Gets the rename schema queries.
     *
     * @return array The rename schema queries.
     */
    public function getRenameSchemaQueries()
    {
        return $this->renameSchemaQueries;
    }

    /**
     * Gets the queries to alter the schema.
     *
     * @return array the queries to alter the schema.
     */
    public function getQueries()
    {
        return array_merge(
            $this->getDropSequenceQueries(),
            $this->getDropViewQueries(),
            $this->getRenameTableQueries(),
            $this->getDropCheckQueries(),
            $this->getDropForeignKeyQueries(),
            $this->getDropIndexQueries(),
            $this->getDropPrimaryKeyQueries(),
            $this->getDropTableQueries(),
            $this->getDropColumnQueries(),
            $this->getAlterColumnQueries(),
            $this->getCreateColumnQueries(),
            $this->getCreateTableQueries(),
            $this->getCreatePrimaryKeyQueries(),
            $this->getCreateIndexQueries(),
            $this->getCreateForeignKeyQueries(),
            $this->getCreateCheckQueries(),
            $this->getCreateViewQueries(),
            $this->getCreateSequenceQueries(),
            $this->getRenameSchemaQueries()
        );
    }

    /**
     * Collects queries about tables to alter a schema.
     *
     * @param \Fridge\DBAL\Schema\Diff\SchemaDiff $schemaDiff The schema difference.
     */
    protected function collectTables(SchemaDiff $schemaDiff)
    {
        foreach ($schemaDiff->getCreatedTables() as $table) {
            $this->createTableSQLCollector->collect($table);
        }

        foreach ($schemaDiff->getDroppedTables() as $table) {
            $this->dropTableSQLCollector->collect($table);
        }

        foreach ($schemaDiff->getAlteredTables() as $tableDiff) {
            $this->alterTableSQLCollector->collect($tableDiff);
        }
    }

    /**
     * Collects queries about views to alter a schema.
     *
     * @param \Fridge\DBAL\Schema\Diff\SchemaDiff $schemaDiff The schema difference.
     */
    protected function collectViews(SchemaDiff $schemaDiff)
    {
        foreach ($schemaDiff->getCreatedViews() as $view) {
            $this->createViewQueries[] = $this->platform->getCreateViewSQLQuery($view);
        }

        foreach ($schemaDiff->getDroppedViews() as $view) {
            $this->dropViewQueries[] = $this->platform->getDropViewSQLQuery($view);
        }
    }

    /**
     * Collects queries about sequences to a schema.
     *
     * @param \Fridge\DBAL\Schema\Diff\SchemaDiff $schemaDiff The schema difference.
     */
    protected function collectSequences(SchemaDiff $schemaDiff)
    {
        foreach ($schemaDiff->getCreatedSequences() as $sequence) {
            $this->createSequenceQueries[] = $this->platform->getCreateSequenceSQLQuery($sequence);
        }

        foreach ($schemaDiff->getDroppedSequences() as $sequence) {
            $this->dropSequenceQueries[] = $this->platform->getDropSequenceSQLQuery($sequence);
        }
    }
}
