<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Exception;

/**
 * Type exception.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class TypeException extends Exception
{
    /**
     * Gets the "CONVERSION TO PHP FAILED" exception.
     *
     * @param mixed  $value The value to be converted.
     * @param string $type  The Fridge type conversion.
     *
     * @return \Fridge\DBAL\Exception\TypeException The "CONVERSION TO PHP FAILED" exception.
     */
    static public function conversionToPHPFailed($value, $type)
    {
        return new static(sprintf('The value "%s" can not be converted to the type "%s".', $value, $type));
    }

    /**
     * Gets the "TYPE ALREADY EXISTS" exception.
     *
     * @param string $type The type that already exists.
     *
     * @return \Fridge\DBAL\Exception\TypeException The "TYPE ALREADY EXISTS" exception.
     */
    static public function typeAlreadyExists($type)
    {
        return new static(sprintf('The type "%s" already exists.', $type));
    }

    /**
     * Gets the "TYPE DOES NOT EXIST" exception.
     *
     * @param string $type The type that does not exist.
     *
     * @return \Fridge\DBAL\Exception\TypeException The "TYPE DOES NOT EXIST" exception.
     */
    static public function typeDoesNotExist($type)
    {
        return new static(sprintf('The type "%s" does not exist.', $type));
    }

    /**
     * Gets the "TYPE MUST IMPLEMENT TYPE INTERFACE" exception.
     *
     * @param string $type The type class.
     *
     * @return \Fridge\DBAL\Exception\TypeException The "TYPE MUST IMPLEMENT TYPE INTERFACE" exception.
     */
    static public function typeMustImplementTypeInterface($type)
    {
        return new static(sprintf('The type "%s" must implement the Fridge\DBAL\Type\TypeInterface.', $type));
    }
}
