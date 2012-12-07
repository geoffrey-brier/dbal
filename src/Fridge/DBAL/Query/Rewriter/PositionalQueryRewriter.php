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
 * This implementation can only rewrite a positional query.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PositionalQueryRewriter extends AbstractQueryRewriter
{
    /**
     * Rewrites a positional query in order to expand it according to the Connection::PARAM_ARRAY ([]).
     *
     * Example:
     *   - parameters:
     *     - query: SELECT * FROM foo WHERE id IN (?)
     *     - parameters: array(array(1, 2, 3))
     *     - types: array('integer[]')
     *   - result:
     *     - query: SELECT * FROM foo WHERE id IN (?, ?, ?)
     *     - parameters: array(1, 2, 3);
     *     - types: array('integer', 'integer', 'integer');
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

        // The position gap produced by the rewrite.
        $positionGap = 0;

        // The cached placeholders positions.
        $cachedPlaceholdersPositions = array();

        // Iterate types to find the ones which need to be rewritten.
        foreach ($types as $index => $type) {

            // Check if the type needs to be rewritten.
            if (static::extractType($type) !== false) {

                // Determine the parameter count.
                $parameterCount = count($parameters[$index]);

                // Find the current placeholder position according to the current parameter and cached placeholders
                // positions.
                $placeholderPosition = static::determinePlaceholderPosition(
                    $rewrittenQuery,
                    $index,
                    $cachedPlaceholdersPositions
                );

                // Generate new placeholders according to the parameter count.
                $newPlaceholders = array_fill(0, $parameterCount, '?');

                // Rewrite the query.
                $rewrittenQuery = static::rewriteQuery($rewrittenQuery, $placeholderPosition, $newPlaceholders);

                // Rewrite parameter & type.
                list($rewrittenParameters, $rewrittenTypes) = static::rewriteParameterAndType(
                    $rewrittenParameters,
                    $rewrittenTypes,
                    $index + $positionGap
                );

                // Cache the placeholder position according to the parameter count.
                $cachedPlaceholdersPositions[$index] = $placeholderPosition + $parameterCount + (($parameterCount - 1) * 2);

                // Increase the position gap according to the parameter count.
                $positionGap += $parameterCount - 1;
            }
        }

        return array($rewrittenQuery, $rewrittenParameters, $rewrittenTypes);
    }

    /**
     * Determines the placeholder position in the query.
     *
     * Example:
     *   - parameters:
     *     - query: SELECT * FROM foo WHERE foo IN (?) AND bar IN (?)
     *                                               ^ (33)        ^ (47)
     *     - index: 1
     *     - cachedPlaceholdersPositions: array(33)
     *   - result:
     *     - placeholderPosition: 47
     *
     * @param string  $query                 The query.
     * @param integer $index                 The query parameter index (indexed by 0).
     * @param array   $placeholdersPositions The cached placeholders positions.
     *
     * @throws \Fridge\DBAL\Exception\Query\Rewriter\QueryRewriterException If the placeholder does not exist.
     *
     * @return integer The placeholder position.
     */
    static protected function determinePlaceholderPosition($query, $index, array $placeholdersPositions = array())
    {
        // The placeholder position.
        $placeholderPosition = null;

        // Find the previous cached placeholder position in the stack to optimize the current placeholder position
        // search.

        // The previous parameter index cached.
        $previousIndex = $index - 1;

        // Iterate each cached placeholder positions to find the previous one & the previous parameter index.
        while (($previousIndex >= 0) && ($placeholderPosition === null)) {

            // Check if the previous cached placeholder positions exists.
            if (isset($placeholdersPositions[$previousIndex])) {
                $placeholderPosition = $placeholdersPositions[$previousIndex];
                $previousIndex++;
            } else {
                $previousIndex--;
            }
        }

        // Check if the previous placeholder position has been found.
        if ($placeholderPosition === null) {
            $previousIndex = 0;
            $placeholderPosition = 0;
        }

        // Determine the query length.
        $queryLength = strlen($query);

        // Iterate from the previous cached parameter index to the current one.
        for ($placeholderIndex = $previousIndex ; $placeholderIndex <= $index ; $placeholderIndex++) {

            // TRUE if we are in an escaped section else FALSE.
            $escaped = false;

            // Iterate each query char from the previous placeholder position to the end in order to find the current
            // placeholder.
            while ($placeholderPosition < $queryLength) {

                // Switch the escaped flag if the current statement char is an escape delimiter.
                if (in_array($query[$placeholderPosition], array('\'', '"'))) {
                    $escaped = !$escaped;
                }

                // Check if we are not in an escaped section and if the current char is a placeholder.
                if (!$escaped && ($query[$placeholderPosition] === '?')) {

                    // If we are not on the current placeholder, we increment the placeholder position.
                    if ($placeholderIndex < $index) {
                        $placeholderPosition++;
                    }

                    break;
                }

                $placeholderPosition++;
            }

            // Check if we have found the placeholder position.
            if ($placeholderPosition === $queryLength) {
                throw QueryRewriterException::positionalPlaceholderDoesNotExist($index, $query);
            }
        }

        return $placeholderPosition;
    }

    /**
     * Rewrites the query according to the placeholder position & new placeholders.
     *
     * Example:
     *   - parameters:
     *     - query: SELECT * FROM foo WHERE foo IN (?)
     *                                              ^ (32)
     *     - placeholderPosition: 32
     *     - newPlaceholders: array('?', '?', '?')
     *   - result:
     *     - rewrittenQuery: SELECT * FROM foo WHERE foo IN (?, ?, ?)
     *
     * @param string  $query               The query.
     * @param integer $placeholderPosition The placeholder position.
     * @param array   $newPlaceholders     The new placeholders.
     *
     * @return string The rewritten query.
     */
    static protected function rewriteQuery($query, $placeholderPosition, array $newPlaceholders)
    {
        return substr($query, 0, $placeholderPosition).
            implode(', ', $newPlaceholders).
            substr($query, $placeholderPosition + 1);
    }

    /**
     * Rewrites the parameter & type according to the parameter index .
     *
     * Example:
     *   - parameters:
     *     - parameters: array(array(1, 3, 5))
     *     - types: array('integer[]')
     *     - index: 0
     *   - result:
     *     - rewrittenParameters: array(1, 3, 5)
     *     - rewrittenTypes: array('integer', 'integer', 'integer')
     *
     * @param array   $parameters The query parameters.
     * @param array   $types      The query types.
     * @param integer $index      The query parameter index.
     *
     * @return array 0 => The rewritten parameters, 1 => The rewritten types.
     */
    static protected function rewriteParameterAndType(array $parameters, array $types, $index)
    {
        // The parameter value according.
        $parameterValue = $parameters[$index];

        // The parameter count.
        $parameterCount = count($parameterValue);

        // Extract the fridge type.
        $type = static::extractType($types[$index]);

        // Shift parameters & types placed just after the current rewritten position according to the gap.
        // This will prepare the parameters & types to be rewritten with new placeholders.

        // Determine the interval to shift.
        $minPosition = $index;
        $maxPosition = max(array_keys($parameters));

        // Iterate the interval to shift each of them according to the parameter count.
        for ($rewritePosition = $maxPosition ; $rewritePosition > $minPosition ; $rewritePosition--) {

            // Determine the new position.
            $newPosition = $rewritePosition + $parameterCount - 1;

            // Shift the parameter.
            $parameters[$newPosition] = $parameters[$rewritePosition];

            // Shift or unset the type if it does not exist.
            if (isset($types[$rewritePosition])) {
                $types[$newPosition] = $types[$rewritePosition];
            } else if (isset($types[$newPosition])) {
                unset($types[$newPosition]);
            }
        }

        // Rewrite parameters & types according to the parameter count.
        for ($newPlaceholderIndex = 0 ; $newPlaceholderIndex < $parameterCount ; $newPlaceholderIndex++) {

            // Determine the new position.
            $newPosition = $index + $newPlaceholderIndex;

            // Rewrite the parameter & type.
            $parameters[$newPosition] = $parameterValue[$newPlaceholderIndex];
            $types[$newPosition] = $type;
        }

        return array($parameters, $types);
    }
}
