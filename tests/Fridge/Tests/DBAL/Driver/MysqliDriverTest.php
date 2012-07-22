<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\Driver;

use Fridge\DBAL\Driver\MysqliDriver,
    Fridge\Tests\PHPUnitUtility;

/**
 * Mysqli driver tests.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class MysqliDriverTest extends AbstractDriverTest
{
    /**
     * {@inheritdoc}
     */
    static public function setUpBeforeClass()
    {
        if (PHPUnitUtility::hasSettings(PHPUnitUtility::MYSQLI)) {
            self::$settings = PHPUnitUtility::getSettings(PHPUnitUtility::MYSQLI);
        }

        parent::setUpBeforeClass();
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (PHPUnitUtility::hasSettings(PHPUnitUtility::MYSQLI)) {
            $this->driver = new MysqliDriver();
        }

        parent::setUp();
    }
}
