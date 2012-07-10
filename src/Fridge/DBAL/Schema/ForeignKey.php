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
 * Describes a database foreign key.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ForeignKey extends AbstractAsset implements ConstraintInterface
{
    /** @var array */
    protected $localColumnNames;

    /** @var string */
    protected $foreignTableName;

    /** @var array */
    protected $foreignColumnNames;

    /**
     * Creates a foreign key.
     *
     * @param string $name               The foreign key name.
     * @param array  $localColumnNames   The local column names.
     * @param string $foreignTableName   The foreign table name.
     * @param array  $foreignColumnNames The foreign column names.
     */
    public function __construct($name, array $localColumnNames, $foreignTableName, array $foreignColumnNames)
    {
        if ($name === null) {
            $name = $this->generateIdentifier('fk_', 20);
        }

        parent::__construct($name);

        $this->setLocalColumnNames($localColumnNames);
        $this->setForeignTableName($foreignTableName);
        $this->setForeignColumnNames($foreignColumnNames);
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnNames()
    {
        return $this->getLocalColumnNames();
    }

    /**
     * Gets the local column names.
     *
     * @return array The local column names.
     */
    public function getLocalColumnNames()
    {
        return $this->localColumnNames;
    }

    /**
     * Sets the local column names.
     *
     * @param array $localColumnNames The local column names.
     */
    public function setLocalColumnNames(array $localColumnNames)
    {
        $this->localColumnNames = array();

        foreach ($localColumnNames as $localColumnName) {
            $this->addLocalColumnName($localColumnName);
        }
    }

    /**
     * Adds a local column name to the foreign key.
     *
     * @param string $localColumnName The local column name to add.
     */
    public function addLocalColumnName($localColumnName)
    {
        if (!is_string($localColumnName) || (strlen($localColumnName) <= 0)) {
            throw SchemaException::invalidForeignKeyLocalColumnName($this->getName());
        }

        $this->localColumnNames[] = $localColumnName;
    }

    /**
     * Gets the foreign table name.
     *
     * @return string The foreign table name.
     */
    public function getForeignTableName()
    {
        return $this->foreignTableName;
    }

    /**
     * Sets the foreign table name.
     *
     * @param string $foreignTableName The foreign table name.
     */
    public function setForeignTableName($foreignTableName)
    {
        if (!is_string($foreignTableName) || (strlen($foreignTableName) <= 0)) {
            throw SchemaException::invalidForeignKeyForeignTableName($this->getName());
        }

        $this->foreignTableName = $foreignTableName;
    }

    /**
     * Gets the foreign column names.
     *
     * @return array The foreign column names.
     */
    public function getForeignColumnNames()
    {
        return $this->foreignColumnNames;
    }

    /**
     * Sets the foreign column names.
     *
     * @param array $foreignColumnNames The foreign column names.
     */
    public function setForeignColumnNames(array $foreignColumnNames)
    {
        foreach ($foreignColumnNames as $foreignColumnName) {
            $this->addForeignColumnName($foreignColumnName);
        }
    }

    /**
     * Adds a foreign column name to the foreign key.
     *
     * @param string $foreignColumnName The foreign column name to add.
     */
    public function addForeignColumnName($foreignColumnName)
    {
        if (!is_string($foreignColumnName) || (strlen($foreignColumnName) <= 0)) {
            throw SchemaException::invalidForeignKeyForeignColumnName($this->getName());
        }

        $this->foreignColumnNames[] = $foreignColumnName;
    }
}
