<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Adapter;

use Fridge\DBAL\Exception\Adapter\StatementRewriterException;

/**
 * A statement rewriter allows to deal with named placeholder.
 *
 * It rewrites named query to positional query and rewrites each named parameter
 * to its corresponding positional parameters.
 *
 * If the statement is a positional statement, the statement rewriter simply
 * returns the statement and parameters like they are given.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class StatementRewriter
{
    /** @var string */
    protected $statement;

    /** @var array */
    protected $parameters;

    /**
     * Statement rewriter constructor.
     *
     * @param string $statement The statement to rewrite.
     */
    public function __construct($statement)
    {
        $this->statement = $statement;
        $this->parameters = array();

        $this->rewrite();
    }

    /**
     * Gets the rewrited statement.
     *
     * @return string The rewrited statement.
     */
    public function getRewritedStatement()
    {
        return $this->statement;
    }

    /**
     * Gets the rewrited positional statement parameters according to the named parameter.
     *
     * The metod returns an array because a named parameter can be used multiple times in the statement.
     *
     * @param string $parameter The named parameter.
     *
     * @throws \Fridge\DBAL\Exception\Adapter\StatementRewriterException If the parameter does not exist.
     *
     * @return array The rewrited positional parameters.
     */
    public function getRewritedParameters($parameter)
    {
        if (is_int($parameter)) {
            return array($parameter);
        }

        if (!isset($this->parameters[$parameter])) {
            throw StatementRewriterException::parameterDoesNotExist($parameter);
        }

        return $this->parameters[$parameter];
    }

    /**
     * Rewrite the named statement and parameters to positional.
     *
     * Example:
     *  - before:
     *    - statement: SELECT * FROM foo WHERE bar = :bar
     *    - parameters: array()
     *  - after:
     *    - statement: SELECT * FROM foo WHERE bar = ?
     *    - parameters: array(':bar' => array(1))
     */
    protected function rewrite()
    {
        // Current positional parameter.
        $positionalParameter = 1;

        // TRUE if we are in a literal section else FALSE.
        $literal = false;

        // The statement length.
        $statementLength = strlen($this->statement);

        // Iterate each statement char.
        for ($placeholderPosition = 0 ; $placeholderPosition < $statementLength ; $placeholderPosition++) {

            // Switch the literal flag if the current statement char is a literal delimiter.
            if (in_array($this->statement[$placeholderPosition], array('\'', '"'))) {
                $literal = !$literal;
            }

            // Check if we are not in a literal section and the current statement char is a double colon.
            if (!$literal && $this->statement[$placeholderPosition] === ':') {

                // Determine placeholder length.
                $placeholderLength = 1;
                while (isset($this->statement[$placeholderPosition + $placeholderLength])
                    && $this->isValidPlaceholderCharacter($this->statement[$placeholderPosition + $placeholderLength])) {
                    $placeholderLength++;
                }

                // Extract placeholder from the statement.
                $placeholder = substr($this->statement, $placeholderPosition, $placeholderLength);

                // Initialize rewrites parameters.
                if (!isset($this->parameters[$placeholder])) {
                    $this->parameters[$placeholder] = array();
                }

                // Rewrites parameter.
                $this->parameters[$placeholder][] = $positionalParameter;

                // Rewrite statement.
                $this->statement = substr($this->statement, 0, $placeholderPosition).
                    '?'.
                    substr($this->statement, $placeholderPosition + $placeholderLength);

                // Decrement statement length.
                $statementLength = $statementLength - $placeholderLength + 1;

                // Increment position parameter.
                $positionalParameter++;
            }
        }
    }

    /**
     * Checks if the character is a valid placeholder character.
     *
     * @param string $character The character to check.
     *
     * @return boolean TRUE if the character is a valid placeholder character else FALSE.
     */
    protected function isValidPlaceholderCharacter($character)
    {
        $asciiCode = ord($character);

        return (($asciiCode >= 48) && ($asciiCode <= 57))
            || (($asciiCode >= 65) && ($asciiCode <= 90))
            || (($asciiCode >= 97) && ($asciiCode <= 122));
    }
}
