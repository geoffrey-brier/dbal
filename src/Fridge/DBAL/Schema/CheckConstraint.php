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
 * Describe a database check constraint.
 *
 * @author Benjamin Lazarecki <benjamin.lazarecki@gmail.com>
 */
class CheckConstraint extends AbstractAsset implements ConstraintInterface
{
    /** @var array */
    protected $columnNames;

    /** @var string */
    protected $constraint;

    /**
     * Create a check constraint.
     *
     * @param string $name        The check constraint name.
     * @param string $constraint  The constraint.
     * @param array  $columnNames The columnNames.
     */
    public function __construct($name, $constraint, array $columnNames = array())
    {
        if ($name === null) {
            $name = $this->generateIdentifier('constraint_', 30);
        }

        parent::__construct($name);

        $this->setConstraint($constraint);
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
     * Return the constraint.
     *
     * @return string
     */
    public function getConstraint()
    {
        return $this->constraint;
    }

    /**
     * Set the columnNames.
     *
     * @param array $columnNames
     */
    public function setColumnNames(array $columnNames)
    {
        $this->columnNames = array();

        foreach ($columnNames as $columnName) {
            $this->addColumnName($columnName);
        }
    }

    /**
     * Add a column name to the check constraint.
     *
     * @param string $columnName The check constraint column name.
     */
    public function addColumnName($columnName)
    {
        if (!is_string($columnName) || (strlen($columnName) == 0)) {
            throw SchemaException::invalidCheckConstraintColumnName($this->getName());
        }

        $this->columnNames[] = $columnName;
    }

    /**
     * Set the constraint.
     *
     * @param string $constraint The constraint.
     */
    public function setConstraint($constraint)
    {
        if (!is_string($constraint) || strlen($constraint) == 0) {
           throw SchemaException::invalidCheckConstraintConstraint($this->getName());
        }

        $this->constraint = $constraint;
    }
}
