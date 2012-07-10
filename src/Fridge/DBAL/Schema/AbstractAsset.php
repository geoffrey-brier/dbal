<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Schema;

use Fridge\DBAL\Exception\SchemaException;

/**
 * An asset offers the ability to manage name and allows you to easily generate a prefixed identifier.
 *
 * All schema classes must extend this class.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractAsset
{
    /** @var string */
    protected $name;

    /**
     * Creates an asset.
     *
     * @param string $name The asset name.
     */
    public function __construct($name)
    {
        $this->setName($name);
    }

    /**
     * Gets the asset name.
     *
     * @return string The asset name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the asset name.
     *
     * @param string $name The asset name.
     */
    public function setName($name)
    {
        if (!is_string($name) || (strlen($name) <= 0)) {
            $explodedNamespace = explode('\\', get_class($this));
            $class = $explodedNamespace[count($explodedNamespace) - 1];
            $asset = strtolower(preg_replace('/(.)([A-Z])/', '\\1 \\2', $class));

            throw SchemaException::invalidAssetName($asset);
        }

        $this->name = $name;
    }

    /**
     * Generates a prefixed identifier.
     *
     * @param string  $prefix    The identifier prefix.
     * @param integer $maxLength The identifier max length.
     *
     * @return string The prefixed identifier
     */
    protected function generateIdentifier($prefix, $maxLength)
    {
        $hash = null;
        $dictionary = 'abcdefghijklmnopqrstuvwxyz0123456789';

        for ($i = 0 ; $i < ($maxLength - strlen($prefix)) ; $i++) {
            $hash .= $dictionary[mt_rand(0, strlen($dictionary) - 1)];
        }

        return $prefix.$hash;
    }
}
