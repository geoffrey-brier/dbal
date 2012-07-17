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

use \PDO;

use Fridge\DBAL\Platform\PlatformInterface;

/**
 * Integer type.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class IntegerType implements TypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(PlatformInterface $platform, array $options = array())
    {
        return $platform->getIntegerSQLDeclaration($options);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, PlatformInterface $platform)
    {
        return ($value === null) ? null : (int) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, PlatformInterface $platform)
    {
        return ($value === null) ? null : (int) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getBindingType()
    {
        return PDO::PARAM_INT;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return Type::INTEGER;
    }
}
