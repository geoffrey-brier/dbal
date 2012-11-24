<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Exception\Adapter;

/**
 * Mysqli adapter exception.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class MysqliException extends AbstractAdapterException
{
    /**
     * Gets the "MAPPED TYPE DOES NOT EXIST" exception
     *
     * @param integer $type The type.
     *
     * @return \Fridge\DBAL\Exception\Adapter\MysqliException The "MAPPED TYPE DOES NOT EXIST" exception.
     */
    static public function mappedTypeDoesNotExist($type)
    {
        return new static(sprintf('The mapped type "%s" does not exist.', $type));
    }
}
