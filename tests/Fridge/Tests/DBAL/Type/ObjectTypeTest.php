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

use Fridge\DBAL\Base\PDO,
    Fridge\DBAL\Type;

/**
 * Object type test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ObjectTypeTest extends AbstractTypeTest
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->type = new Type\ObjectType();
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
        $this->assertEquals('O:8:"stdClass":1:{s:3:"foo";s:3:"bar";}', $this->type->convertToDatabaseValue((object) array('foo' => 'bar'), $this->platformMock));
    }

    public function testConvertToDatabaseValueWithNullValue()
    {
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platformMock));
    }

    public function testConvertToPHPValueWithValidValue()
    {
        $this->assertEquals((object) array('foo' => 'bar'), $this->type->convertToPHPValue('O:8:"stdClass":1:{s:3:"foo";s:3:"bar";}', $this->platformMock));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\TypeException
     */
    public function testConvertToPHPValueWithInvalidValue()
    {
        error_reporting((E_ALL | E_STRICT) - E_NOTICE);

        $this->type->convertToPHPValue('foo', $this->platformMock);

        error_reporting(-1);
    }

    public function testConvertToPHPValueWithNullValue()
    {
        $this->assertNull($this->type->convertToPHPValue(null, $this->platformMock));
    }

    public function testBindingType()
    {
        $this->assertEquals(PDO::PARAM_STR, $this->type->getBindingType());
    }

    public function testName()
    {
        $this->assertEquals(Type\Type::OBJECT, $this->type->getName());
    }
}
