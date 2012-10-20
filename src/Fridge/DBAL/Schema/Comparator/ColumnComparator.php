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
        $differences = array();

        if ($oldColumn->getType() !== $newColumn->getType()) {
            $differences[] = 'type';
        }

        if ($oldColumn->getLength() !== $newColumn->getLength()) {
            $differences[] = 'length';
        }

        if ($oldColumn->getPrecision() !== $newColumn->getPrecision()) {
            $differences[] = 'precision';
        }

        if ($oldColumn->getScale() !== $newColumn->getScale()) {
            $differences[] = 'scale';
        }

        if ($oldColumn->isUnsigned() !== $newColumn->isUnsigned()) {
            $differences[] = 'unsigned';
        }

        if ($oldColumn->isFixed() !== $newColumn->isFixed()) {
            $differences[] = 'fixed';
        }

        if ($oldColumn->isNotNull() !== $newColumn->isNotNull()) {
            $differences[] = 'not_null';
        }

        if ($oldColumn->getDefault() !== $newColumn->getDefault()) {
            $differences[] = 'default';
        }

        if ($oldColumn->isAutoIncrement() !== $newColumn->isAutoIncrement()) {
            $differences[] = 'auto_increment';
        }

        if ($oldColumn->getComment() !== $newColumn->getComment()) {
            $differences[] = 'comment';
        }

        return new ColumnDiff($oldColumn, $newColumn, $differences);
    }
}
