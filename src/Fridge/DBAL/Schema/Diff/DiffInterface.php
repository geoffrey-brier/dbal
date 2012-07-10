<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Schema\Diff;

/**
 * Describes a difference.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface DiffInterface
{
    /**
     * Checks if the diff has difference.
     *
     * @return boolean TRUE if the diff has difference else FALSE.
     */
    function hasDifference();
}
