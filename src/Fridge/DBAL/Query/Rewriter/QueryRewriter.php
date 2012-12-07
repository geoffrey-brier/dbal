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

/**
 * Rewrites a query in order to expand it according to the Connection::PARAM_ARRAY.
 *
 * This implementation can rewrite a positional or named query.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class QueryRewriter
{
    /**
     * Rewrites a query in order to expand it according to the Connection::PARAM_ARRAY ([]).
     *
     * @param string $query      The query.
     * @param array  $parameters The query parameters.
     * @param array  $types      The query types.
     *
     * @see \Fridge\DBAL\Query\Rewriter\PositionalQueryRewriter::rewrite
     * @see \Fridge\DBAL\Query\Rewriter\NamedQueryRewriter::rewrite
     *
     * @return array 0 => The rewritten query, 1 => The rewritten query parameters, 2 => The rewritten query types.
     */
    static public function rewrite($query, array $parameters, array $types)
    {
        if (is_int(key($types))) {
            return PositionalQueryRewriter::rewrite($query, $parameters, $types);
        }

        return NamedQueryRewriter::rewrite($query, $parameters, $types);
    }
}
