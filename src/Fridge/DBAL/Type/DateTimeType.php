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

use \DateTime;

use Fridge\DBAL\Base\PDO,
    Fridge\DBAL\Exception\TypeException,
    Fridge\DBAL\Platform\PlatformInterface;

/**
 * Date time type.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class DateTimeType implements TypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(PlatformInterface $platform, array $options = array())
    {
        return $platform->getDateTimeSQLDeclaration($options);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, PlatformInterface $platform)
    {
        return ($value === null) ? null : $value->format($platform->getDateTimeFormat());
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, PlatformInterface $platform)
    {
        if ($value === null) {
            return null;
        }

        $phpValue = DateTime::createFromFormat($platform->getDateTimeFormat(), $value);

        if ($phpValue === false) {
            throw TypeException::conversionToPHPFailed($value, $this->getName());
        }

        return $phpValue;
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
        return Type::DATETIME;
    }
}
