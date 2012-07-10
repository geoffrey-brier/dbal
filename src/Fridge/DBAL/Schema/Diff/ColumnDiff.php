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

use Fridge\DBAL\Schema\Column;

/**
 * Describes the difference between two columns.
 *
 * @author GeLo <geloen.eric@gmail.com>
 * @author Benjamin Lazarecki <benjamin.lazarecki@gmail.com>
 */
class ColumnDiff extends AbstractAssetDiff
{
    /** @var array */
    protected $differences;

    /**
     * Column diff constructor.
     *
     * @param \Fridge\DBAL\Schema\Column $oldColumn   The old column.
     * @param \Fridge\DBAL\Schema\Column $newColumn   The new column.
     * @param array                      $differences The column differences.
     */
    public function __construct(Column $oldColumn, Column $newColumn, array $differences)
    {
        parent::__construct($oldColumn, $newColumn);

        $this->differences = $differences;
    }

    /**
     * Gets the column difference
     *
     * @return type
     */
    public function getDifferences()
    {
        return $this->differences;
    }

    /**
     * {@inheritdoc}
     */
    public function hasDifference()
    {
        return parent::hasDifference() || !empty($this->differences);
    }

    /**
     * {@inheritdoc}
     */
    public function hasNameDifferenceOnly()
    {
        return parent::hasNameDifferenceOnly() && empty($this->differences);
    }
}
