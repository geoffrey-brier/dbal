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

use Fridge\DBAL\Exception\TypeException,
    Fridge\DBAL\Platform\PlatformInterface;

/**
 * Object type.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ObjectType implements TypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(PlatformInterface $platform, array $options = array())
    {
        return $platform->getClobSQLDeclaration($options);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, PlatformInterface $platform)
    {
        return ($value === null) ? null : serialize($value);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, PlatformInterface $platform)
    {
        if ($value === null) {
            return null;
        }

        $errorReporting = error_reporting(~E_ALL);
        $val = unserialize($value);
        error_reporting($errorReporting);

        if ($val === false) {
            throw TypeException::conversionToPHPFailed($value, $this->getName());
        }

        return $val;
    }

    /**
     * {@inheritdoc}
     */
    public function getBindingType()
    {
        return PDO::PARAM_STR;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return Type::OBJECT;
    }
}
