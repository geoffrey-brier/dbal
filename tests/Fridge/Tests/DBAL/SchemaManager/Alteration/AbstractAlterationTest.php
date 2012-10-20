<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\SchemaManager\Alteration;

/**
 * Base alteration test case.
 *
 * All alteration tests must extend this class.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractAlterationTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\Tests\Fixture\FixtureInterface */
    static protected $fixture;

    /** @var \Fridge\DBAL\Connection\ConnectionInterface */
    protected $connection;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (self::$fixture === null) {
            $this->markTestSkipped();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        if ($this->connection !== null) {
            $this->connection->close();
        }

        unset($this->connection);
    }
}
