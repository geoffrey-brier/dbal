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

/**
 * An asset offers the ability to manage renaming.
 *
 * @author GeLo <geloen.eric@gmail.com>
 * @author Benjamin Lazarecki <benjamin.lazarecki@gmail.com>
 */
abstract class AbstractAssetDiff implements DiffInterface
{
    /** @var string */
    protected $oldName;

    /** @var string */
    protected $newName;

    /**
     * @param string $oldName The old name.
     * @param string $newName The new name.
     */
    public function __construct($oldName, $newName)
    {
        $this->oldName = $oldName;
        $this->newName = $newName;
    }

    /**
     * Get the old name.
     *
     * @return string The old name.
     */
    public function getOldName()
    {
        return $this->oldName;
    }

    /**
     * Get the new name.
     *
     * @return string The new name.
     */
    public function getNewName()
    {
        return $this->newName;
    }

    /**
     * {@inheritdoc}
     */
    public function hasDifference()
    {
        return $this->oldName !== $this->newName;
    }
}
