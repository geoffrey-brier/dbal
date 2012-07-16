<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Query;

/**
 * Rewrites a query in order to expand it according to the Connection::PARAM_ARRAY.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class QueryRewriter
{
    /**
     * Rewrites a positional/named query in order to expand it according to
     * the Connection::PARAM_ARRAY ([]).
     *
     * Before rewriting:
     *
     * $query = 'SELECT * FROM foo WHERE id IN (?)';
     * $parameters = array(array(1, 2, 3));
     * $types = array('integer[]');
     *
     * After rewritting:
     *
     * $query = 'SELECT * FROM foo WHERE id IN (?, ?, ?)';
     * $parameters = array(1, 2, 3);
     * $types = array('integer', 'integer', 'integer');
     *
     * @param string $query      The query.
     * @param array  $parameters The query parameters.
     * @param array  $types      The query types.
     *
     * @return array 0 => query, 1 => parameters, 2 => types.
     */
    static public function rewrite($query, array $parameters, array $types)
    {
        return array($query, $parameters, $types);
    }
}
