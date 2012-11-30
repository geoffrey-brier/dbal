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

use Fridge\DBAL\Type\BooleanType,
    Fridge\DBAL\Type\Type;

/**
 * Boolean type test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class BooleanTypeTest extends AbstractTypeTest
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->type = new BooleanType();
    }

    public function testSQLDeclaration()
    {
        $this->platformMock
            ->expects($this->once())
            ->method('getBooleanSQLDeclaration');

        $this->type->getSQLDeclaration($this->platformMock);
    }

    public function testConvertToDatabaseValueWithValidValue()
    {
        $this->assertSame(1, $this->type->convertToDatabaseValue(true, $this->platformMock));
        $this->assertSame(0, $this->type->convertToDatabaseValue(false, $this->platformMock));
    }

    public function testConvertToDatabaseValueWithNullValue()
    {
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platformMock));
    }

    public function testConvertToPHPValueWithValidValue()
    {
        $this->assertTrue($this->type->convertToPHPValue(1, $this->platformMock));
        $this->assertFalse($this->type->convertToPHPValue(0, $this->platformMock));
    }

    public function testConvertToPHPValueWithNullValue()
    {
        $this->assertNull($this->type->convertToPHPValue(null, $this->platformMock));
    }

    public function testBindingType()
    {
        $this->assertSame(PDO::PARAM_BOOL, $this->type->getBindingType());
    }

    public function testName()
    {
        $this->assertSame(Type::BOOLEAN, $this->type->getName());
    }
}
