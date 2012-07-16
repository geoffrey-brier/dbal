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

use Fridge\DBAL\Connection\Connection;

/**
 * Rewrites a query in order to expand it according to the Connection::PARAM_ARRAY.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class QueryRewriter
{
    /**
     * Rewrites a positional/named query in order to expand it according to the Connection::PARAM_ARRAY ([]).
     *
     * @see \Fridge\DBAL\Query\QueryRewriter::rewritePositionalQuery
     * @see \Fridge\DBAL\Query\QueryRewriter::rewriteNamedQuery
     *
     * @param string $query      The query.
     * @param array  $parameters The query parameters.
     * @param array  $types      The query types.
     *
     * @return array 0 => query, 1 => parameters, 2 => types.
     */
    static public function rewrite($query, array $parameters, array $types)
    {
        if (empty($types)) {
            return array($query, $parameters, $types);
        }

        if (is_int(key($types))) {
            return self::rewritePositionalQuery($query, $parameters, $types);
        }

        return self::rewriteNamedQuery($query, $parameters, $types);
    }

    /**
     * Rewrites a positional query in order to expand it according to the Connection::PARAM_ARRAY ([]).
     *
     * Example:
     *   - before:
     *     - query: SELECT * FROM foo WHERE id IN (?)
     *     - parameters: array(array(1, 2, 3))
     *     - types: array('integer[]')
     *   - after:
     *     - query: SELECT * FROM foo WHERE id IN (?, ?, ?)
     *     - parameters: array(1, 2, 3);
     *     - types: array('integer', 'integer', 'integer');
     *
     * @param string $query      The query.
     * @param array  $parameters The query parameters.
     * @param array  $types      The query types.
     *
     * @return array 0 => query, 1 => parameters, 2 => types.
     */
    static public function rewritePositionalQuery($query, array $parameters, array $types)
    {
        $rewritedQuery = $query;
        $rewritedParameters = $parameters;
        $rewritedTypes = $types;

        $typePositionGap = 0;

        foreach ($types as $typePosition => $type) {
            if (substr($type, -2) === Connection::PARAM_ARRAY) {
                $fridgeType = substr($type, 0, strlen($type) - 2);

                $placeholderPosition = -1;
                for ($placeholderIndex = 0 ; $placeholderIndex <= $typePosition + $typePositionGap ; $placeholderIndex++) {
                    $placeholderPosition = strpos($rewritedQuery, '?', $placeholderPosition + 1);
                }

                $parameterCount = count($parameters[$typePosition]);
                $newPlaceholders = array_fill(0, $parameterCount, '?');

                $rewritedQuery = self::rewriteQuery($rewritedQuery, $placeholderPosition, 1, $newPlaceholders);

                for ($index = max(array_keys($parameters)) ; $index > $typePosition - $typePositionGap ; $index--) {
                    $newPosition = $index + $parameterCount + $typePositionGap - 1;

                    $rewritedParameters[$newPosition] = $parameters[$index];

                    if (isset($types[$index])) {
                        $rewritedTypes[$newPosition] = $types[$index];
                    } else if (isset($rewritedTypes[$newPosition])) {
                        unset($rewritedTypes[$newPosition]);
                    }
                }

                foreach (array_keys($newPlaceholders) as $newPlaceholderIndex) {
                    $newPosition = $typePosition + $typePositionGap + $newPlaceholderIndex;

                    $rewritedParameters[$newPosition] = $parameters[$typePosition][$newPlaceholderIndex];
                    $rewritedTypes[$newPosition] = $fridgeType;
                }

                $typePositionGap += $parameterCount - 1;
            }
        }

        return array($rewritedQuery, $rewritedParameters, $rewritedTypes);
    }

    /**
     * Rewrites a named query in order to expand it according to the Connection::PARAM_ARRAY ([]).
     *
     * Example:
     *   - before:
     *     - query: SELECT * FROM foo WHERE id IN (:foo)
     *     - parameters: array('foo' => array(1, 2, 3))
     *     - types: array('foo' => 'integer[]')
     *   - after:
     *     - query: SELECT * FROM foo WHERE id IN (:foo1, :foo2, :foo3)
     *     - parameters: array('foo1' => 1, 'foo2' => 2, 'foo3' => 3)
     *     - types: array('foo1' => 'integer', 'foo2' => 'integer', 'foo3' => 'integer')
     *
     * @param string $query      The query.
     * @param array  $parameters The query parameters.
     * @param array  $types      The query types.
     *
     * @return array 0 => query, 1 => parameters, 2 => types.
     */
    static public function rewriteNamedQuery($query, array $parameters, array $types)
    {
        $rewritedQuery = $query;
        $rewritedParameters = $parameters;
        $rewritedTypes = $types;

        foreach ($types as $parameter => $type) {
            if (substr($type, -2) === Connection::PARAM_ARRAY) {
                $fridgeType = substr($type, 0, strlen($type) - 2);

                $parameterCount = count($parameters[$parameter]);
                $placeholder = ':'.$parameter;

                $newPlaceholders = array();
                for ($index = 1 ; $index <= $parameterCount ; $index++) {
                    $newPlaceholders[] = $placeholder.$index;
                }

                $rewritedQuery = self::rewriteQuery(
                    $rewritedQuery,
                    strpos($rewritedQuery, $placeholder),
                    strlen($placeholder),
                    $newPlaceholders
                );

                foreach ($newPlaceholders as $newPlaceholderIndex => $newPlaceholder) {
                    $newParameter = substr($newPlaceholder, 1);

                    $rewritedParameters[$newParameter] = $parameters[$parameter][$newPlaceholderIndex];
                    $rewritedTypes[$newParameter] = $fridgeType;
                }

                unset($rewritedParameters[$parameter]);
                unset($rewritedTypes[$parameter]);
            }
        }

        return array($rewritedQuery, $rewritedParameters, $rewritedTypes);
    }

    /**
     * Rewrites a query by expanded a placeholder.
     *
     * Example with positional query:
     *   - before:
     *     - query: SELECT * FROM foo WHERE foo IN (?)
     *     - placeholderPosition: 32
     *     - placeholderLength: 1
     *     - newPlaceholders: array('?', '?', '?')
     *   - after: SELECT * FROM foo WHERE foo IN (?, ?, ?)
     *
     * Example with named query:
     *   - before:
     *     - query: SELECT * FROM foo WHERE foo IN (:foo)
     *     - placeholderPosition: 32
     *     - placeholderLength: 4
     *     - newPlaceholders: array(':foo1', ':foo2', ':foo3')
     *   - after: SELECT * FROM foo WHERE foo IN (:foo1, :foo2, :foo3)
     *
     * @param string  $query               The query.
     * @param integer $placeholderPosition The placeholder position.
     * @param integer $placeholderLength   The placeholder length.
     * @param array   $newPlaceholders     The new placeholders.
     *
     * @return string The rewrited query.
     */
    static protected function rewriteQuery(
        $query,
        $placeholderPosition,
        $placeholderLength,
        array $newPlaceholders
    )
    {
        return substr($query, 0, $placeholderPosition).
            implode(', ', $newPlaceholders).
            substr($query, $placeholderPosition + $placeholderLength);
    }
}
