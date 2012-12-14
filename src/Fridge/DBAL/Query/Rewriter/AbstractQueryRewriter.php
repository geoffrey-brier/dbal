<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Query\Rewriter;

use Fridge\DBAL\Connection\Connection;

/**
 * Rewrites a query in order to expand it according to the Connection::PARAM_ARRAY.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractQueryRewriter
{
    /**
     * Extracts the fridge type from the expanded type.
     *
     * @param string $type The type.
     *
     * @return string|boolean The fridge type or false if the type is not an expanded one.
     */
    static protected function extractType($type)
    {
        if (substr($type, -2) === Connection::PARAM_ARRAY) {
            return substr($type, 0, strlen($type) - 2);
        }

        return false;
    }
}
