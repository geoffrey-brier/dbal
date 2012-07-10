<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Driver;

use Fridge\DBAL\Base\PDO;

/**
 * The PDO driver allows to easily support base PDO connections by adding the DSN notion.
 *
 * All drivers using a base PDO connection must extend this class.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractPDODriver extends AbstractDriver
{
    /**
     * {@inheritdoc}
     */
    public function connect(array $parameters, $username = null, $password = null, array $driverOptions = array())
    {
        return new PDO($this->generateDSN($parameters), $username, $password, $driverOptions);
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
