<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Adapter\PDO;

use \PDOStatement;

use Fridge\DBAL\Adapter\StatementInterface;

/**
 * {@inheritdoc}
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Statement extends PDOStatement implements StatementInterface
{
    /**
     * Disabeld constructor.
     */
    final private function __construct()
    {

    }
}
