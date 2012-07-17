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

/**
 * Abstract driver test.
 *
 * All driver tests must extend this class.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractDriverTest extends \PHPUnit_Framework_TestCase
{
    /** @var array */
    static protected $settings;

    /** @var \Fridge\DBAL\Driver\DriverInterface */
    protected $driver;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (($this->driver === null) || (self::$settings === null)) {
            $this->markTestSkipped();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->driver);
    }

    public function testConnect()
    {
        $this->assertInstanceOf(
            'Fridge\DBAL\Adapter\PDO\Connection',
            $this->driver->connect(self::$settings, self::$settings['username'], self::$settings['password'])
        );
    }

    public function testPlatform()
    {
        $this->assertInstanceOf('Fridge\DBAL\Platform\PlatformInterface', $this->driver->getPlatform());
    }

    public function testSchemaManager()
    {
        $connectionMock = $this->getMock('Fridge\DBAL\Connection\ConnectionInterface');

        $this->assertInstanceOf(
            'Fridge\DBAL\SchemaManager\SchemaManagerInterface',
            $this->driver->getSchemaManager($connectionMock)
        );
    }
}
