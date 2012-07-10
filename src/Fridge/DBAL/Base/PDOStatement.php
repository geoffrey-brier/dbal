<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Base;

use \PDOStatement as BasePDOStatement;

/**
 * {@inheritdoc}
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PDOStatement extends BasePDOStatement implements StatementInterface
{
    /**
     * Disabeld constructor.
     */
    final private function __construct()
    {

    }
}
