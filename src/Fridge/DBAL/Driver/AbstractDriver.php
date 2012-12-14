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

/**
 * The abstract driver wraps the platform and the schema manager.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractDriver implements DriverInterface
{
    /** @var \Fridge\DBAL\Platform\PlatformInterface */
    protected $platform;

    /** @var \Fridge\DBAL\SchemaManager\SchemaManagerInterface */
    protected $schemaManager;
}
