<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Driver\PDO;

use Fridge\DBAL\Adapter\PDO\PDOConnection,
    Fridge\DBAL\Driver\AbstractDriver as BaseDriver;

/**
 * The abstract PDO driver allows to easily support low-level PDO connections by adding the DSN notion.
 *
 * All drivers using a low-level PDO connection must extend this class.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractDriver extends BaseDriver
{
    /**
     * {@inheritdoc}
     */
    public function connect(array $parameters, $username = null, $password = null, array $driverOptions = array())
    {
        return new PDOConnection($this->generateDSN($parameters), $username, $password, $driverOptions);
    }

    /**
     * Generates the PDO DSN.
     *
     * @param array $parameters The PDO DSN parameters
     *
     * @return string The PDO DSN
     */
    abstract protected function generateDSN(array $parameters);
}
