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

use Fridge\DBAL\Exception\Query\Rewriter\QueryRewriterException;

/**
 * {@inheritdoc}
 *
 * This implementation can only rewrite a named query.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class NamedQueryRewriter extends AbstractQueryRewriter
{
    /**
     * Rewrites a named query in order to expand it according to the Connection::PARAM_ARRAY ([]).
     *
     * Example:
     *   - parameters:
     *     - query: SELECT * FROM foo WHERE id IN (:foo)
     *     - parameters: array('foo' => array(1, 2, 3))
     *     - types: array('foo' => 'integer[]')
     *   - result:
     *     - rewrittenQuery: SELECT * FROM foo WHERE id IN (:foo1, :foo2, :foo3)
     *     - rewrittenParameters: array('foo1' => 1, 'foo2' => 2, 'foo3' => 3)
     *     - rewrittenTypes: array('foo1' => 'integer', 'foo2' => 'integer', 'foo3' => 'integer')
     *
     * @param string $query      The query.
     * @param array  $parameters The query parameters.
     * @param array  $types      The query types.
     *
     * @return array 0 => The rewritten query, 1 => The rewritten parameters, 2 => The rewritten types.
     */
    static public function rewrite($query, array $parameters, array $types)
    {
        if (empty($types)) {
            return array($query, $parameters, $types);
        }

        // Copy each variables.
        $rewrittenQuery = $query;
        $rewrittenParameters = $parameters;
        $rewrittenTypes = $types;

        // Iterate types to find each one which needs to be rewritten.
        foreach ($types as $parameter => $type) {

            // Check if the type needs to be rewritten.
            if (static::extractType($type) !== false) {

                // Generate the placeholder and determine his length.
                $placeholder = ':'.$parameter;
                $placeholderLength = strlen($placeholder);

                // Determine placeholder positions.
                $placeholderPositions = static::determinePlaceholderPositions($rewrittenQuery, $placeholder);

                // Generate new placeholders.
                $newPlaceholders = static::generateNewPlaceholders($placeholder, count($parameters[$parameter]));

                // Rewrite the query.
                $rewrittenQuery = static::rewriteQuery(
                    $rewrittenQuery,
                    $placeholderPositions,
                    $placeholderLength,
                    $newPlaceholders
                );

                // Rewrite the parameter & type.
                list($rewrittenParameters, $rewrittenTypes) = static::rewriteParameterAndType(
                    $rewrittenParameters,
                    $rewrittenTypes,
                    $parameter,
                    $newPlaceholders
                );
            }
        }

        return array($rewrittenQuery, $rewrittenParameters, $rewrittenTypes);
    }

    /**
     * Determines the placeholder positions in the query.
     *
     * Example:
     *   - parameters:
     *     - query: SELECT * FROM foo WHERE foo IN (:foo) AND bar IN (:foo)
     *                                              ^ (32)            ^ (50)
     *     - placeholder: :foo
     *  - result:
     *     - placeholderPositions: array(32, 50)
     *
     * @param string $query       The query.
     * @param string $placeholder The placeholder.
     *
     * @throws \Fridge\DBAL\Exception\Query\Rewriter\QueryRewriterException If the placeholder does not exist.
     *
     * @return array The placeholder positions.
     */
    static protected function determinePlaceholderPositions($query, $placeholder)
    {
        // Placeholder positions.
        $placeholderPositions = array();

        // TRUE if we are in an escaped section else FALSE.
        $escaped = false;

        // The placeholder length.
        $placeholderLength = strlen($placeholder);

        // The placeholder position limit before which it can be found.
        $placeolderPositionLimit = strlen($query) - $placeholderLength + 1;

        // Iterache each query char to find the placeholder.
        for ($placeholderPosition = 0 ; $placeholderPosition < $placeolderPositionLimit ; $placeholderPosition++) {

            // Switch the escaped flag if the current query char is a escape delimiter.
            if (in_array($query[$placeholderPosition], array('\'', '"'))) {
                $escaped = !$escaped;
            }

            // Collect the placeholder position if we are not in an escaped section and there is the placeholder at the
            // current position.
            if (!$escaped && (substr($query, $placeholderPosition, $placeholderLength) === $placeholder)) {
                $placeholderPositions[] = $placeholderPosition;
            }
        }

        // Check if the placeholder has been found.
        if (empty($placeholderPositions)) {
            throw QueryRewriterException::namedPlaceholderDoesNotExist($placeholder, $query);
        }

        return $placeholderPositions;
    }

    /**
     * Generates the new placeholders according to the placeholder and the number of parameter.
     *
     * Example:
     *   - parameters:
     *     - placeholder: :foo
     *     - count: 3
     *   - result:
     *     - newPlaceholders: array(':foo1', ':foo2', ':foo3')
     *
     * @param string  $placeholder The placeholder.
     * @param integer $count       The number of parameter.
     *
     * @return array The new placeholders.
     */
    static protected function generateNewPlaceholders($placeholder, $count)
    {
        $newPlaceholders = array();

        for ($index = 1 ; $index <= $count ; $index++) {
            $newPlaceholders[] = $placeholder.$index;
        }

        return $newPlaceholders;
    }

    /**
     * Rewrites the named query according to the placeholder length and positions and the new placeholders.
     *
     * Example:
     *   - parameters:
     *     - query; SELECT * FROM foo WHERE foo IN (:foo) AND bar IN (:foo)
     *     - placeholderPositions: array(32, 50)
     *     - placeholderLength: 4
     *     - newPlaceholders: array(':foo1', ':foo2', ':foo3')
     *   - result:
     *     - rewrittenQuery: SELECT * FROM foo WHERE foo IN (:foo1, :foo2, :foo3) AND bar IN (:foo1, :foo2, :foo3)
     *
     * @param string  $query                The query.
     * @param array   $placeholderPositions The placeholder positions.
     * @param integer $placeholderLength    The placeholder length.
     * @param array   $newPlaceholders      The new placeholders.
     *
     * @return string The rewritten query.
     */
    static protected function rewriteQuery(
        $query,
        array $placeholderPositions,
        $placeholderLength,
        array $newPlaceholders
    )
    {
        // The position gap produced by the rewrite.
        $positionGap = 0;

        // Generates new placeholders and his length.
        $placeholders = implode(', ', $newPlaceholders);
        $placeholdersLength = strlen($placeholders);

        // Iterate placeholder positions to rewrite each one.
        foreach ($placeholderPositions as $placeholderPosition) {

            // Rewrite the query.
            $query = substr($query, 0, $placeholderPosition + $positionGap).
                $placeholders.
                substr($query, $placeholderPosition + $positionGap + $placeholderLength);

            // Increase the position gap.
            $positionGap += $placeholdersLength - $placeholderLength;
        }

        return $query;
    }

    /**
     * Rewrites the query parameter & type by expanding them according to the parameter and the new placeholders.
     *
     * Example:
     *   - parameters:
     *     - parameters: array('foo' => array(1, 3, 5))
     *     - types: array('foo' => 'integer[]')
     *     - parameter: foo
     *     - newPlaceholders: array(':foo1', ':foo2', ':foo3')
     *   - result:
     *     - rewrittenParameters: array('foo1' => 1, 'foo2' => 3, 'foo3' => 5)
     *     - rewrittenTypes: array('foo1' => 'integer', 'foo2' => 'integer', 'foo3' => 'integer')
     *
     * @param array  $parameters      The query parameters.
     * @param array  $types           The query types.
     * @param string $parameter       The query parameter to rewrite.
     * @param array  $newPlaceholders The new placeholders.
     *
     * @return array 0 => The rewritten parameters, 1 => The rewritten types.
     */
    static protected function rewriteParameterAndType(
        array $parameters,
        array $types,
        $parameter,
        array $newPlaceholders
    )
    {
        // Extract the fridge type.
        $type = static::extractType($types[$parameter]);

        // Iterate new placeholders to rewrite each one.
        foreach ($newPlaceholders as $newPlaceholderIndex => $newPlaceholder) {

            // Determine the new parameter.
            $newParameter = substr($newPlaceholder, 1);

            // Rewrites parameters and types.
            $parameters[$newParameter] = $parameters[$parameter][$newPlaceholderIndex];
            $types[$newParameter] = $type;
        }

        // Remove rewritten parameter and type.
        unset($parameters[$parameter]);
        unset($types[$parameter]);

        return array($parameters, $types);
    }
}
