<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL;

use Fridge\DBAL\Configuration,
    Monolog\Logger,
    Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Configuration test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultLogger()
    {
        $configuration = new Configuration();

        $this->assertInstanceOf('Monolog\Logger', $configuration->getLogger());
        $this->assertSame('Fridge DBAL', $configuration->getLogger()->getName());
    }

    public function testDefaultEventDispatcher()
    {
        $configuration = new Configuration();

        $this->assertInstanceOf(
            'Symfony\Component\EventDispatcher\EventDispatcher',
            $configuration->getEventDispatcher()
        );
    }

    public function testCustomLogger()
    {
        $logger1 = new Logger('foo');
        $configuration = new Configuration($logger1);

        $this->assertSame($logger1, $configuration->getLogger());

        $logger2 = new Logger('bar');
        $configuration->setLogger($logger2);

        $this->assertSame($logger2, $configuration->getLogger());
    }

    public function testCustomEventDispatcher()
    {
        $eventDispatcher1 = new EventDispatcher();
        $configuration = new Configuration(null, $eventDispatcher1);

        $this->assertSame($eventDispatcher1, $configuration->getEventDispatcher());

        $eventDispatcher2 = new EventDispatcher();
        $configuration->setEventDispatcher($eventDispatcher2);

        $this->assertSame($eventDispatcher2, $configuration->getEventDispatcher());
    }
}
