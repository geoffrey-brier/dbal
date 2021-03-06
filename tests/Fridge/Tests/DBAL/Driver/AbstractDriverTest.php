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
    /** @var \Fridge\Tests\Fixture\FixtureInterface */
    static protected $fixtures;

    /** @var \Fridge\DBAL\Driver\DriverInterface */
    protected $driver;

    /**
     * {@inheritdoc}
     */
    static public function setUpBeforeClass()
    {
        if (self::$fixtures !== null) {
            self::$fixtures->createSchema();
        }
    }

    /**
     * {@inheritdoc}
     */
    static public function tearDownAfterClass()
    {
        if (self::$fixtures !== null) {
            self::$fixtures->drop();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (self::$fixtures === null) {
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
        $settings = self::$fixtures->getSettings();

        $this->assertInstanceOf(
            'Fridge\DBAL\Adapter\ConnectionInterface',
            $this->driver->connect($settings, $settings['username'], $settings['password'])
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
