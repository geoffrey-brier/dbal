<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\Fixture;

use \PDO;

/**
 * Fixture that can use PDO for building it.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractPDOFixture extends AbstractFixture
{
    /**
     * {@inheritdoc}
     */
    protected function getConnection()
    {
        $dsnOptions = array();

        foreach ($this->settings as $dsnKey => $dsnSetting) {
            if (in_array($dsnKey, array('dbname', 'host', 'port'))) {
                $dsnOptions[] = $dsnKey.'='.$dsnSetting;
            }
        }

        $dsnPrefix = substr($this->settings['driver'], 4);
        $username = $this->settings['username'];
        $password = $this->settings['password'];

        return new PDO($dsnPrefix.':'.implode(';', $dsnOptions), $username, $password);
    }
}
