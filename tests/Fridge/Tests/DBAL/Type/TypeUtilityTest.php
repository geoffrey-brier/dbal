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

use \PDO;

use Fridge\DBAL\Type;

/**
 * Type utility test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class TypeUtilityTest extends \PHPUnit_Framework_TestCase
{
    public function testBindTypedValueWithDBALType()
    {
        $type = Type\Type::BOOLEAN;
        $value = true;

        $platformMock = $this->getMock('Fridge\DBAL\Platform\PlatformInterface');
        Type\TypeUtility::bindTypedValue($value, $type, $platformMock);

        $this->assertSame(1, $value);
        $this->assertSame(PDO::PARAM_BOOL, $type);
    }

    public function testBindTypedValueWithPDOType()
    {
        $type = PDO::PARAM_BOOL;
        $value = true;

        $platformMock = $this->getMock('Fridge\DBAL\Platform\PlatformInterface');
        Type\TypeUtility::bindTypedValue($value, $type, $platformMock);

        $this->assertSame(true, $value);
        $this->assertSame(PDO::PARAM_BOOL, $type);
    }
}
