<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Exception;

/**
 * Connection exception.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ConnectionException extends Exception
{
    /**
     * Gets the "NO ACTIVE TRANSACTION" exception.
     *
     * @return \Fridge\DBAL\Exception\ConnectionException The "NO ACTIVE TRANSACTION" exception.
     */
    public static function noActiveTransaction()
    {
        return new static('The connection does not have an active transaction.');
    }

    /**
     * Gets the "TRANSACTION ISOLATION NOT SUPPORTED" exception.
     *
     * @return \Fridge\DBAL\Exception\ConnectionException The "TRANSACTION ISOLATION NOT SUPPORTED" exception.
     */
    public static function transactionIsolationNotSupported()
    {
        return new static('The connection does not support transaction isolation.');
    }
}
