<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\Type;

/**
 * Base class for all abstract type tests.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractTypeTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\Type\ArrayType */
    protected $type;

    /** @var \Fridge\DBAL\Platform\PlatformInterface */
    protected $platformMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->platformMock = $this->getMock('Fridge\DBAL\Platform\PlatformInterface');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->type);
        unset($this->platformMock);
    }
}
