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

use \DateTime,
    \PDO;

use Fridge\DBAL\Type;

/**
 * Time type test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class TimeTypeTest extends AbstractTypeTest
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->type = new Type\TimeType();

        $this->platformMock
            ->expects($this->any())
            ->method('getTimeFormat')
            ->will($this->returnValue('H:i:s'));
    }

    public function testSQLDeclaration()
    {
        $this->platformMock
            ->expects($this->once())
            ->method('getTimeSQLDeclaration');

        $this->type->getSQLDeclaration($this->platformMock);
    }

    public function testConvertToDatabaseValueWithValidValue()
    {
        $this->assertSame('01:23:45', $this->type->convertToDatabaseValue(new DateTime('01:23:45'), $this->platformMock));
    }

    public function testConvertToDatabaseValueWithNullValue()
    {
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platformMock));
    }

    public function testConvertToPHPValueWithValidValue()
    {
        $this->assertEquals(new DateTime('01:23:45'), $this->type->convertToPHPValue('01:23:45', $this->platformMock));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\TypeException
     * @expectedExceptionMessage The value "foo" can not be converted to the type "time".
     */
    public function testConvertToPHPValueWithInvalidValue()
    {
        $this->type->convertToPHPValue('foo', $this->platformMock);
    }

    public function testConvertToPHPValueWithNullValue()
    {
        $this->assertNull($this->type->convertToPHPValue(null, $this->platformMock));
    }

    public function testBindingType()
    {
        $this->assertSame(PDO::PARAM_STR, $this->type->getBindingType());
    }

    public function testName()
    {
        $this->assertSame(Type\Type::TIME, $this->type->getName());
    }
}
