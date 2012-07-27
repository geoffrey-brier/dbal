<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Adapter\Mysqli;

use Fridge\DBAL\Exception\Adapter\MysqliException;

/**
 * A mysqli statement rewriter allows to deal with named placeholder.
 *
 * It rewrites named query to positional query and rewrites each named parameter
 * to his corresponding positional parameters.
 *
 * If the statement is a positional statement, the statement rewriter simply
 * returns the statement & parameters like they are given.
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

        $this->rewriteStatement();
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
     * @param string $parameter The named parameter.
     *
     * @return array The rewrited positional parameters.
     */
    public function getRewritedParameters($parameter)
    {
        if (is_int($parameter)) {
            return array($parameter);
        }

        if (!isset($this->parameters[$parameter])) {
            throw MysqliException::parameterDoesNotExist($parameter);
        }

        return $this->parameters[$parameter];
    }

    /**
     * Rewrite the a named statement & parameters to positional.
     *
     * Example:
     *  - before:
     *    - statement: SELECT * FROM foo WHERE bar = :bar
     *    - parameters: array()
     *  - after:
     *    - statement: SELECT * FROM foo WHERE bar = ?
     *    - parameters: array(':bar' => 1)
     */
    protected function rewriteStatement()
    {
        // Current positional parameter.
        $positionalParameter = 1;

        // Find each named placeholder position.
        $placeholderPosition = 0;
        while(($placeholderPosition = strpos($this->statement, ':', $placeholderPosition)) !== false) {

            // Determine placeholder length.
            $placeholderLength = 1;
            while(isset($this->statement[$placeholderPosition + $placeholderLength])
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

            $positionalParameter++;
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
