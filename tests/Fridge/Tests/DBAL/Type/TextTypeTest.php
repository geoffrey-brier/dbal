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
 * Text type test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class TextTypeTest extends AbstractTypeTest
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->type = new Type\TextType();
    }

    public function testSQLDeclaration()
    {
        $this->platformMock
            ->expects($this->once())
            ->method('getClobSQLDeclaration');

        $this->type->getSQLDeclaration($this->platformMock);
    }

    public function testConvertToDatabaseValueWithValidValue()
    {
        $this->assertSame('foo', $this->type->convertToDatabaseValue('foo', $this->platformMock));
    }

    public function testConvertToDatabaseValueWithNullValue()
    {
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platformMock));
    }

    public function testConvertToPHPValueWithValidValue()
    {
        $this->assertSame('foo', $this->type->convertToPHPValue('foo', $this->platformMock));
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
        $this->assertSame(Type\Type::TEXT, $this->type->getName());
    }
}
