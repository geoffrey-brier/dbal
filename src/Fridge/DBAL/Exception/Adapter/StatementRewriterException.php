<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Exception\Adapter;

use Fridge\DBAL\Exception\Exception as BaseException;

/**
 * Statement rewriter exception.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class StatementRewriterException extends BaseException
{
    /**
     * Gets the "PARAMETER DOES NOT EXIST" exception.
     *
     * @param string $parameter The parameter.
     *
     * @return \Fridge\DBAL\Exception\Adapter\StatementRewriterException The "PARAMETER DOES NOT EXIST" exception.
     */
    static public function parameterDoesNotExist($parameter)
    {
        return new static(sprintf('The parameter "%s" does not exist.', $parameter));
    }
}
