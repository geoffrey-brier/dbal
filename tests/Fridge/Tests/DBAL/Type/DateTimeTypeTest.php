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

use Fridge\DBAL\Type\DateTimeType,
    Fridge\DBAL\Type\Type;

/**
 * DateTime type test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class DateTimeTypeTest extends AbstractTypeTest
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->type = new DateTimeType();

        $this->platformMock
            ->expects($this->any())
            ->method('getDateTimeFormat')
            ->will($this->returnValue('Y-m-d H:i:s'));
    }

    public function testSQLDeclaration()
    {
        $this->platformMock
            ->expects($this->once())
            ->method('getDateTimeSQLDeclaration');

        $this->type->getSQLDeclaration($this->platformMock);
    }

    public function testConvertToDatabaseValueWithValidValue()
    {
        $this->assertSame('2012-01-01 01:23:45', $this->type->convertToDatabaseValue(new DateTime('2012-01-01 01:23:45'), $this->platformMock));
    }

    public function testConvertToDatabaseValueWithNullValue()
    {
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platformMock));
    }

    public function testConvertToPHPValueWithValidValue()
    {
        $this->assertEquals(new DateTime('2012-01-01 01:23:45'), $this->type->convertToPHPValue('2012-01-01 01:23:45', $this->platformMock));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\TypeException
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
        $this->assertSame(Type::DATETIME, $this->type->getName());
    }
}
