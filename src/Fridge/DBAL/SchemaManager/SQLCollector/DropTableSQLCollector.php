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
 * Collects queries to drop tables.
 *
 * The queries order are:
 *  - Drop foreign keys.
 *  - Drop tables.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class DropTableSQLCollector
{
    /** @var \Fridge\DBAL\Platform\PlatformInterface */
    protected $platform;

    /** @var array */
    protected $dropForeignKeyQueries;

    /** @var array */
    protected $dropTableQueries;

    /**
     * Drop table SQL collector constructor.
     *
     * @param \Fridge\DBAL\Platform\PlatformInterface $platform The platform used to collect queries.
     */
    public function __construct(PlatformInterface $platform)
    {
        $this->setPlatform($platform);
    }

    /**
     * Gets the platform used to collect queries.
     *
     * @return \Fridge\DBAL\Platform\PlatformInterface The platform used to collect queries.
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
        $this->dropForeignKeyQueries = array();
        $this->dropTableQueries = array();
    }

    /**
     * Collects queries to drop tables.
     *
     * @param \Fridge\DBAL\Schema\Table $table The table.
     */
    public function collect(Table $table)
    {
        foreach ($table->getForeignKeys() as $foreignKey) {
            $this->dropForeignKeyQueries = array_merge(
                $this->dropForeignKeyQueries,
                $this->platform->getDropForeignKeySQLQueries($foreignKey, $table->getName())
            );
        }

        $this->dropTableQueries = array_merge(
            $this->dropTableQueries,
            $this->platform->getDropTableSQLQueries($table)
        );
    }

    /**
     * Gets the drop foreign key queries.
     *
     * @return array the drop foreign key queries.
     */
    public function getDropForeignKeyQueries()
    {
        return $this->dropForeignKeyQueries;
    }

    /**
     * Gets the drop table queries.
     *
     * @return array The drop table queries.
     */
    public function getDropTableQueries()
    {
        return $this->dropTableQueries;
    }

    /**
     * Gets the queries collected to drop tables.
     *
     * @return array The queries collected to drop tables.
     */
    public function getQueries()
    {
        return array_merge($this->getDropForeignKeyQueries(), $this->getDropTableQueries());
    }
}
