<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Type;

use Fridge\DBAL\Platform\PlatformInterface;

/**
 * Represents a generic database type.
 *
 * All types must implement this interface.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface TypeInterface
{
    /**
     * Gets the type SQL declaration.
     *
     * @param \Fridge\DBAL\Platform\PlatformInterface $platform The platform used.
     * @param array                                   $options  The type options.
     *
     * @return string The type SQL declaration.
     */
    function getSQLDeclaration(PlatformInterface $platform, array $options = array());

    /**
     * Converts a PHP value to this database value.
     *
     * @param mixed                                   $value    The PHP value.
     * @param \Fridge\DBAL\Platform\PlatformInterface $platform The platform used.
     *
     * @return mixed The database value.
     */
    function convertToDatabaseValue($value, PlatformInterface $platform);

    /**
     * Converts a database value to this PHP value.
     *
     * @param mixed                                   $value    The database value.
     * @param \Fridge\DBAL\Platform\PlatformInterface $platform The platform used.
     *
     * @return mixed The PHP value.
     */
    function convertToPHPValue($value, PlatformInterface $platform);

    /**
     * Gets the binding type.
     *
     * @return integer The Binding type.
     */
    function getBindingType();

    /**
     * Gets the type name.
     *
     * @return string The type name.
     */
    function getName();
}
