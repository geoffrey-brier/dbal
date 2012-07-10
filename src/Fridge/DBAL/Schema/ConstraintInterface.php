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
 * Describes a database constraint.
 *
 * All database constraints must implement this interface.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface ConstraintInterface
{
    /**
     * Gets the constraint name.
     *
     * @return string The constraint name.
     */
    function getName();

    /**
     * Gets the constraint column names.
     *
     * @return array The constraint column names.
     */
    function getColumnNames();
}
