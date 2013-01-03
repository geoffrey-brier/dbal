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
     * @throws \Fridge\DBAL\Exception\TypeException If the database value can not be convert to this PHP value.
     */
    public function convertToPHPValue($value, PlatformInterface $platform)
    {
        if ($value === null) {
            return null;
        }

        if (!is_string($value) && !is_resource($value)) {
            throw TypeException::conversionToPHPFailed($value, $this->getName());
        }

        $resource = tmpfile();
        fwrite($resource, $value);
        fseek($resource, 0);

        return $resource;
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
