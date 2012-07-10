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
    Fridge\DBAL\Schema\Table;

/**
 * Collects queries to create tables.
 *
 * The queries order are:
 *  - Create tables without foreign keys.
 *  - Create foreign keys.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CreateTableSQLCollector
{
    /** @var \Fridge\DBAL\Platform\PlatformInterface */
    protected $platform;

    /** @var array */
    protected $createTableQueries;

    /** @var array */
    protected $createForeignKeyQueries;

    /**
     * Create tables SQL collector constructor.
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
     * @param \Fridge\DBAL\Platform\PlatformInterface $platform
     */
    public function setPlatform(PlatformInterface $platform)
    {
        $this->platform = $platform;

        $this->init();
    }

    /**
     * Reinitializes the SQL collector.
     */
    public function init()
    {
        $this->createTableQueries = array();
        $this->createForeignKeyQueries = array();
    }

    /**
     * Collects queries to create tables.
     *
     * @param \Fridge\DBAL\Schema\Table $table The table.
     */
    public function collect(Table $table)
    {
        $this->createTableQueries = array_merge(
            $this->createTableQueries,
            $this->platform->getCreateTableSQLQueries($table, array('foreign_key' => false))
        );

        foreach ($table->getForeignKeys() as $foreignKey) {
            $this->createForeignKeyQueries[] = $this->platform->getCreateForeignKeySQLQuery($foreignKey, $table->getName());
        }
    }

    /**
     * Gets the create table queries.
     *
     * @return array The create table queries.
     */
    public function getCreateTableQueries()
    {
        return $this->createTableQueries;
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
     * Gets the queries collected to create tables.
     *
     * @return array The queries collected to create tables.
     */
    public function getQueries()
    {
        return array_merge($this->getCreateTableQueries(), $this->getCreateForeignKeyQueries());
    }
}
