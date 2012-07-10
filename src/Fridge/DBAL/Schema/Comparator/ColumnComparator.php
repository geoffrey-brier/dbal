<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Schema\Comparator;

use Fridge\DBAL\Schema\Column,
    Fridge\DBAL\Schema\Diff\ColumnDiff;

/**
 * Column comparator.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ColumnComparator
{
    /**
     * Compares two columns.
     *
     * @param \Fridge\DBAL\Schema\Column $oldColumn The old column.
     * @param \Fridge\DBAL\Schema\Column $newColumn The new column.
     *
     * @return \Fridge\DBAL\Schema\Diff\ColumnDiff The difference between the two columns.
     */
    public function compare(Column $oldColumn, Column $newColumn)
    {
        $column = null;

        if (($oldColumn->getType() !== $newColumn->getType())
            || ($oldColumn->getLength() !== $newColumn->getLength())
            || ($oldColumn->getPrecision() !== $newColumn->getPrecision())
            || ($oldColumn->getScale() !== $newColumn->getScale())
            || ($oldColumn->isUnsigned() !== $newColumn->isUnsigned())
            || ($oldColumn->isFixed() !== $newColumn->isFixed())
            || ($oldColumn->isNotNull() !== $newColumn->isNotNull())
            || ($oldColumn->getDefault() !== $newColumn->getDefault())
            || ($oldColumn->isAutoIncrement() !== $newColumn->isAutoIncrement())
            || ($oldColumn->getComment() !== $newColumn->getComment())) {
            $column = $newColumn;
        }

        return new ColumnDiff($oldColumn->getName(), $newColumn->getName(), $column);
    }
}
