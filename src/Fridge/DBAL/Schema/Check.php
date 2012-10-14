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

use Fridge\DBAL\Query\Expression\Expression;

/**
 * Describes a check definition.
 *
 * @author Benjamin Lazarecki <benjamin.lazarecki@gmail.com>
 */
class Check extends AbstractAsset implements ConstraintInterface
{
    /** @var string */
    protected $definition;

    /**
     * Creates a check.
     *
     * @param string  $name        The check name.
     * @param string  $definition  The check definition.
     */
    public function __construct($name, $definition = null)
    {
        if ($name === null) {
            $name = $this->generateIdentifier('cct_', 20);
        }

        parent::__construct($name);

        $this->setDefinition($definition);
    }

    /**
     * Gets the definition.
     *
     * @return string The definition.
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * Set the definition.
     *
     * @param string|\Fridge\DBAL\Query\Expression\Expression $definition The definition.
     */
    public function setDefinition($definition)
    {
        if ($definition instanceof Expression) {
            $definition = (string) $definition;
        }

        $this->definition = $definition;
    }
}
