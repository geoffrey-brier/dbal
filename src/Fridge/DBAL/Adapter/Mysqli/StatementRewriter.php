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
 * A mysqli statement rewriter allows to deal with named placeholder. It rewrites named query to positional query
 * and rewrites each named parameter to positional parameters according to the statement.
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

        $rewritedParameter = 1;
        while(($parameterPosition = strpos($this->statement, ':')) !== false) {
            $parameterLength = 1;

            while(isset($this->statement[$parameterPosition + $parameterLength])
                && $this->isParameterCharacter($this->statement[$parameterPosition + $parameterLength])) {
                $parameterLength++;
            }


            $parameter = substr($this->statement, $parameterPosition, $parameterLength);

            if (!isset($this->parameters[$parameter])) {
                $this->parameters[$parameter] = array();
            }

            $this->parameters[$parameter][] = $rewritedParameter;

            $this->statement = substr($this->statement, 0, $parameterPosition).
                '?'.
                substr($this->statement, $parameterPosition + $parameterLength);

            $rewritedParameter++;
        }
    }

    /**
     * Rewrites the named statement to a positional statement.
     *
     * @return string The rewrited statement.
     */
    public function rewriteStatement()
    {
        return $this->statement;
    }

    /**
     * Rewrites a named parameter to positional parameters.
     *
     * @param string $parameter The parameter to rewrite.
     *
     * @return array The rewrited parameters.
     */
    public function rewriteParameter($parameter)
    {
        if (!isset($this->parameters[$parameter])) {
            throw MysqliException::parameterDoesNotExist($parameter);
        }

        return $this->parameters[$parameter];
    }

    /**
     * Checks if the character is a valid parameter character.
     *
     * @param string $character The character to check.
     *
     * @return boolean TRUE if the character is a valid parameter character else FALSE.
     */
    protected function isParameterCharacter($character)
    {
        $asciiCode = ord($character);

        return (($asciiCode >= 48) && ($asciiCode <= 57))
            || (($asciiCode >= 65) && ($asciiCode <= 90))
            || (($asciiCode >= 97) && ($asciiCode <= 122));
    }
}
