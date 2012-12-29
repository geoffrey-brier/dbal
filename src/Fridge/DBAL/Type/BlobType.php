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

use Fridge\DBAL\Platform\PlatformInterface,
    Fridge\DBAL\Exception\TypeException;

/**
 * Blob type.
 *
 * @author Loic Chardonnet <loic.chardonnet@gmail.com>
 */
class BlobType implements TypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(PlatformInterface $platform, array $options = array())
    {
        return $platform->getBlobSQLDeclaration($options);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, PlatformInterface $platform)
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     *
     * @throw
     */
    public function convertToPHPValue($value, PlatformInterface $platform)
    {
        if (!is_string($value) && !is_resource($value) && ($value !== null)) {
            throw TypeException::conversionToPHPFailed($value, Type::BLOB);
        }

        $filePointerResource = tmpfile();
        fwrite($filePointerResource, $value);
        fseek($filePointerResource, 0);

        $value = $filePointerResource;

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getBindingType()
    {
        return PDO::PARAM_LOB;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return Type::BLOB;
    }
}
