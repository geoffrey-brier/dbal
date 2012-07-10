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
    /** @var \Fridge\DBAL\Schema\Column */
    protected $column;

    /**
     * Column diff constructor.
     *
     * @param string                     $oldName The old column name.
     * @param string                     $newName The new column name.
     * @param \Fridge\DBAL\Schema\Column $column  The new column.
     */
    public function __construct($oldName, $newName, Column $column = null)
    {
        parent::__construct($oldName, $newName);

        $this->column = $column;
    }

    /**
     * Gets the column.
     *
     * @return \Fridge\DBAL\Schema\Column The column.
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * {@inheritdoc}
     */
    public function hasDifference()
    {
        return parent::hasDifference() || ($this->column !== null);
    }
}
