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

use Fridge\DBAL\Type\Type;

/**
 * Type test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class TypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Gets a list of valid types.
     *
     * @return array A list of valid types.
     */
    static public function validTypeProvider()
    {
        return array(
            array(Type::TARRAY),
            array(Type::BIGINTEGER),
            array(Type::BLOB),
            array(Type::BOOLEAN),
            array(Type::DATE),
            array(Type::DATETIME),
            array(Type::DECIMAL),
            array(Type::FLOAT),
            array(Type::INTEGER),
            array(Type::OBJECT),
            array(Type::SMALLINTEGER),
            array(Type::STRING),
            array(Type::TEXT),
            array(Type::TIME)
        );
    }

    /**
     * @param string $type A valid type.
     *
     * @dataProvider validTypeProvider
     */
    public function testHasTypeWithValidType($type)
    {
        $this->assertTrue(Type::hasType($type));
    }

    public function testHasTypeWithInvalidType()
    {
        $this->assertFalse(type::hasType('foo'));
    }

    /**
     * @param string $type A valid type.
     *
     * @dataProvider validTypeProvider
     */
    public function testGetTypeWithValidType($type)
    {
        $this->assertInstanceOf('Fridge\DBAL\Type\TypeInterface', Type::getType($type));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\TypeException
     * @expectedExceptionMessage The type "foo" does not exist.
     */
    public function testGetTypeWithInvalidType()
    {
        Type::getType('foo');
    }

    public function testAddTypeWithValidTypeAndClass()
    {
        $this->assertFalse(Type::hasType('type'));

        Type::addType('type', $this->getMockClass('Fridge\DBAL\Type\TypeInterface'));

        $this->assertTrue(Type::hasType('type'));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\TypeException
     * @expectedExceptionMessage The type "boolean" already exists.
     */
    public function testAddTypeWithInvalidType()
    {
        Type::addType(Type::BOOLEAN, $this->getMockClass('Fridge\DBAL\Type\TypeInterface'));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\TypeException
     * @expectedExceptionMessage The class "bar" can not be found.
     */
    public function testAddTypeWithInvalidClass()
    {
        Type::addType('foo', 'bar');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\TypeException
     * @expectedExceptionMessage The type "\stdClass" must implement the Fridge\DBAL\Type\TypeInterface.
     */
    public function testAddTypeWithInvalidClassImplementation()
    {
        Type::addType('foo', '\stdClass');
    }

    public function testOverrideTypeWithValidTypeAndClass()
    {
        $this->assertInstanceOf('Fridge\DBAL\Type\BooleanType', Type::getType(Type::BOOLEAN));

        $typeMockClass = $this->getMockClass('Fridge\DBAL\Type\TypeInterface');
        Type::overrideType(Type::BOOLEAN, $typeMockClass);

        $this->assertInstanceOf($typeMockClass, Type::getType(Type::BOOLEAN));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\TypeException
     */
    public function testOverrideTypeWithInvalidType()
    {
        Type::overrideType('foo', $this->getMockClass('Fridge\DBAL\Type\TypeInterface'));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\TypeException
     */
    public function testOverrideTypeWithInvalidClass()
    {
        Type::overrideType(Type::BOOLEAN, 'foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\TypeException
     */
    public function testOverrideTypeWithInvalidClassImplementation()
    {
        Type::overrideType(Type::BOOLEAN, '\stdClass');
    }

    public function testRemoveTypeWithValidType()
    {
        $this->assertTrue(Type::hasType(Type::BOOLEAN));

        Type::removeType(Type::BOOLEAN);
        $this->assertFalse(Type::hasType(Type::BOOLEAN));

        Type::addType(Type::BOOLEAN, 'Fridge\DBAL\Type\BooleanType');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\TypeException
     */
    public function testRemoveTypeWithInvalidType()
    {
        Type::removeType('foo');
    }
}
