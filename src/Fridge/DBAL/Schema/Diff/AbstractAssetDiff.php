<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Schema\Diff;

use Fridge\DBAL\Schema\AbstractAsset;

/**
 * An asset offers the ability to manage renaming.
 *
 * @author GeLo <geloen.eric@gmail.com>
 * @author Benjamin Lazarecki <benjamin.lazarecki@gmail.com>
 */
abstract class AbstractAssetDiff implements DiffInterface
{
    /** @var \Fridge\DBAL\Schema\AbstractAsset */
    protected $oldAsset;

    /** @var \Fridge\DBAL\Schema\AbstractAsset */
    protected $newAsset;

    /**
     * @param \Fridge\DBAL\Schema\AbstractAsset $oldAsset The old asset.
     * @param \Fridge\DBAL\Schema\AbstractAsset $newAsset The new asset.
     */
    public function __construct(AbstractAsset $oldAsset, AbstractAsset $newAsset)
    {
        $this->oldAsset = $oldAsset;
        $this->newAsset = $newAsset;
    }

    /**
     * Get the old asset.
     *
     * @return \Fridge\DBAL\Schema\AbstractAsset The old asset.
     */
    public function getOldAsset()
    {
        return $this->oldAsset;
    }

    /**
     * Get the new asset.
     *
     * @return \Fridge\DBAL\Schema\AbstractAsset The new asset.
     */
    public function getNewAsset()
    {
        return $this->newAsset;
    }

    /**
     * {@inheritdoc}
     */
    public function hasDifference()
    {
        return $this->getOldAsset()->getName() !== $this->getNewAsset()->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function hasNameDifference()
    {
        return $this->getOldAsset()->getName() !== $this->getNewAsset()->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function hasNameDifferenceOnly()
    {
        return $this->getOldAsset()->getName() !== $this->getNewAsset()->getName();
    }
}
