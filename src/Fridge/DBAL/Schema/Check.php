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

/**
 * Describes a check constraint.
 *
 * @author Benjamin Lazarecki <benjamin.lazarecki@gmail.com>
 */
class Check extends AbstractAsset
{
    /** @var string */
    protected $constraint;

    /**
     * Gets the constraint.
     *
     * @return string The constraint.
     */
    public function getConstraint()
    {
        return $this->constraint;
    }

    /**
     * Set the constraint.
     *
     * @param string $constraint
     */
    public function setConstraint($constraint)
    {
        $this->constraint = $constraint;
    }
}
