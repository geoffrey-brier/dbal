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

use Fridge\DBAL\Exception\TypeException;

/**
 * This class follows the flyweight design pattern allowing to request a unique
 * instance per type. Additionally, it allows you to manage your types.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Type
{
    /** @const The array type constant */
    const TARRAY = 'array';

    /** @const The big integer type constant */
    const BIGINTEGER = 'biginteger';

    /** @const The blob type constant */
    const BLOB = 'blob';

    /** @const The boolean type constant */
    const BOOLEAN = 'boolean';

    /** @const The date type constant */
    const DATE = 'date';

    /** @const The date time type constant */
    const DATETIME = 'datetime';

    /** @const The decimal type constant */
    const DECIMAL = 'decimal';

    /** @const The float type constant */
    const FLOAT = 'float';

    /** @const The integer type constant */
    const INTEGER = 'integer';

    /** @const the object type constant */
    const OBJECT = 'object';

    /** @const The small integer type constant */
    const SMALLINTEGER = 'smallinteger';

    /** @const The string type constant */
    const STRING = 'string';

    /** @const The text type constant */
    const TEXT = 'text';

    /** @const The time type constant */
    const TIME = 'time';

    /** @var array */
    static protected $mappedTypeClasses = array(
        self::TARRAY       => 'Fridge\DBAL\Type\ArrayType',
        self::BIGINTEGER   => 'Fridge\DBAL\Type\BigIntegerType',
        self::BLOB         => 'Fridge\DBAL\Type\BlobType',
        self::BOOLEAN      => 'Fridge\DBAL\Type\BooleanType',
        self::DATE         => 'Fridge\DBAL\Type\DateType',
        self::DATETIME     => 'Fridge\DBAL\Type\DateTimeType',
        self::DECIMAL      => 'Fridge\DBAL\Type\DecimalType',
        self::FLOAT        => 'Fridge\DBAL\Type\FloatType',
        self::INTEGER      => 'Fridge\DBAL\Type\IntegerType',
        self::OBJECT       => 'Fridge\DBAL\Type\ObjectType',
        self::SMALLINTEGER => 'Fridge\DBAL\Type\SmallIntegerType',
        self::STRING       => 'Fridge\DBAL\Type\StringType',
        self::TEXT         => 'Fridge\DBAL\Type\TextType',
        self::TIME         => 'Fridge\DBAL\Type\TimeType',
    );

    /** @var array */
    static protected $mappedTypeInstances = array();

    /**
     * Checks if a type exists.
     *
     * @param string $type The type name.
     *
     * @return boolean TRUE if the type exists else FALSE.
     */
    static public function hasType($type)
    {
        return isset(static::$mappedTypeClasses[$type]);
    }

    /**
     * Gets a type.
     *
     * @param string $type The type name.
     *
     * @throws \Fridge\DBAL\Exception\TypeException If the type does not exist.
     *
     * @return \Fridge\DBAL\Type\TypeInterface The type.
     */
    static public function getType($type)
    {
        if (!isset(static::$mappedTypeInstances[$type])) {
            if (!static::hasType($type)) {
                throw TypeException::typeDoesNotExist($type);
            }

            static::$mappedTypeInstances[$type] = new static::$mappedTypeClasses[$type]();
        }

        return static::$mappedTypeInstances[$type];
    }

    /**
     * Adds a new type.
     *
     * @param string $type  The type name.
     * @param string $class The type class.
     *
     * @throws \Fridge\DBAL\Exception\TypeException If the type already exists, if the class can not be found or if
     *                                              the class does not implement the TypeInterface.
     */
    static public function addType($type, $class)
    {
        if (static::hasType($type)) {
            throw TypeException::typeAlreadyExists($type);
        }

        if (!class_exists($class)) {
            throw TypeException::classNotFound($class);
        }

        if (!in_array('Fridge\DBAL\Type\TypeInterface', class_implements($class))) {
            throw TypeException::typeMustImplementTypeInterface($class);
        }

        static::$mappedTypeClasses[$type] = $class;
    }

    /**
     * Overrides an existing type.
     *
     * @param string $type  The type name.
     * @param string $class The type class.
     *
     * @throws \Fridge\DBAL\Exception\TypeException If the type does not exist, if the class can not be found or if
     *                                              the class does not implement the TypeInterface.
     */
    static public function overrideType($type, $class)
    {
        if (!static::hasType($type)) {
            throw TypeException::typeDoesNotExist($type);
        }

        if (!class_exists($class)) {
            throw TypeException::classNotFound($class);
        }

        if (!in_array('Fridge\DBAL\Type\TypeInterface', class_implements($class))) {
            throw TypeException::typeMustImplementTypeInterface($class);
        }

        if (isset(static::$mappedTypeInstances[$type])) {
            unset(static::$mappedTypeInstances[$type]);
        }

        static::$mappedTypeClasses[$type] = $class;
    }

    /**
     * Removes a type.
     *
     * @param string $type The type name.
     *
     * @throws \Fridge\DBAL\Exception\TypeException If the type does not exist.
     */
    static public function removeType($type)
    {
        if (!static::hasType($type)) {
            throw TypeException::typeDoesNotExist($type);
        }

        if (isset(static::$mappedTypeInstances[$type])) {
            unset(static::$mappedTypeInstances[$type]);
        }

        unset(static::$mappedTypeClasses[$type]);
    }

    /**
     * Disabled constructor.
     *
     * @codeCoverageIgnore
     */
    final private function __construct()
    {

    }
}
