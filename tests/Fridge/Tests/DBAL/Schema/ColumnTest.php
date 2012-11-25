<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\Schema;

use Fridge\DBAL\Schema\Column,
    Fridge\DBAL\Type\Type;

/**
 * Column test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ColumnTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\Schema\Column */
    protected $column;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->column = new Column('foo', Type::getType(Type::BOOLEAN), array());
    }

    /**
     * {@inhertdoc}
     */
    protected function tearDown()
    {
        unset($this->column);
    }

    public function testInitialState()
    {
        $this->assertSame('foo', $this->column->getName());
        $this->assertSame(Type::BOOLEAN, $this->column->getType()->getName());
        $this->assertNull($this->column->getLength());
        $this->assertNull($this->column->getPrecision());
        $this->assertNull($this->column->getScale());
        $this->assertNull($this->column->isUnsigned());
        $this->assertNull($this->column->isFixed());
        $this->assertFalse($this->column->isNotNull());
        $this->assertNull($this->column->getDefault());
        $this->assertNull($this->column->isAutoIncrement());
        $this->assertNull($this->column->getComment());
    }

    public function testType()
    {
        $this->column->setType(Type::getType(Type::STRING));
        $this->assertSame(Type::STRING, $this->column->getType()->getName());
    }

    public function testLengthWithValidValue()
    {
        $this->column->setLength(50);
        $this->assertSame(50, $this->column->getLength());
    }

    public function testLengthWithNullValue()
    {
        $this->column->setLength(null);
        $this->assertNull($this->column->getLength());
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The length of the column "foo" must be a positive integer.
     */
    public function testLengthWithInvalidValue()
    {
        $this->column->setLength(-1);
    }

    public function testPrecisionWithValidValue()
    {
        $this->column->setPrecision(9);
        $this->assertSame(9, $this->column->getPrecision());
    }

    public function testPrecisionWithNullValue()
    {
        $this->column->setPrecision(null);
        $this->assertNull($this->column->getPrecision());
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The precision of the column "foo" must be a positive integer.
     */
    public function testPrecisionWithInvalidValue()
    {
        $this->column->setPrecision(-1);
    }

    public function testScaleWithValidValue()
    {
        $this->column->setScale(5);
        $this->assertSame(5, $this->column->getScale());
    }

    public function testScaleWithNullValue()
    {
        $this->column->setScale(null);
        $this->assertNull($this->column->getScale());
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The scale of the column "foo" must be a positive integer.
     */
    public function testScaleWithInvalidValue()
    {
        $this->column->setScale(-1);
    }

    public function testUnsignedWithValidValue()
    {
        $this->column->setUnsigned(false);
        $this->assertFalse($this->column->isUnsigned());
    }

    public function testUnsignedWithNullValue()
    {
        $this->column->setUnsigned(null);
        $this->assertNull($this->column->isUnsigned());
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The unsigned flag of the column "foo" must be a boolean.
     */
    public function testUnsignedWithInvalidValue()
    {
        $this->column->setUnsigned('foo');
    }

    public function testFixedWithValidValue()
    {
        $this->column->setFixed(false);
        $this->assertFalse($this->column->isFixed());
    }

    public function testFixedWithNullValue()
    {
        $this->column->setFixed(null);
        $this->assertNull($this->column->isFixed());
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The fixed flag of the column "foo" must be a boolean.
     */
    public function testFixedWithInvalidValue()
    {
        $this->column->setFixed('foo');
    }

    public function testNotNullWithValidValue()
    {
        $this->column->setNotNull(false);
        $this->assertFalse($this->column->isNotNull());
    }

    public function testNotNullWithNullValue()
    {
        $this->column->setNotNull(null);
        $this->assertNull($this->column->isNotNull());
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The not null flag of the column "foo" must be a boolean.
     */
    public function testNotNullWithInvalidValue()
    {
        $this->column->setNotNull('foo');
    }

    public function testDefault()
    {
        $this->column->setDefault('foo');
        $this->assertSame('foo', $this->column->getDefault());
    }

    public function testAutoIncrementWithValidValue()
    {
        $this->column->setAutoIncrement(false);
        $this->assertFalse($this->column->isAutoIncrement());
    }

    public function testAutoIncrementWithNullValue()
    {
        $this->column->setAutoIncrement(null);
        $this->assertNull($this->column->isAutoIncrement());
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The auto increment flag of the column "foo" must be a boolean.
     */
    public function testAutoIncrementWithInvalidValue()
    {
        $this->column->setAutoIncrement('foo');
    }

    public function testCommentWithValidValue()
    {
        $this->column->setComment('foo');
        $this->assertSame('foo', $this->column->getComment());
    }

    public function testCommentWithNullValue()
    {
        $this->column->setComment(null);
        $this->assertNull($this->column->getComment());
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The comment of the column "foo" must be a string.
     */
    public function testCommentWithInvalidValue()
    {
        $this->column->setComment(true);
    }

    public function testPropertiesWithValidProperties()
    {
        $properties = array(
            'name'           => 'foo',
            'type'           => Type::getType(Type::BOOLEAN),
            'length'         => 100,
            'precision'      => 5,
            'scale'          => 2,
            'unsigned'       => true,
            'fixed'          => true,
            'not_null'       => true,
            'default'        => 'foo',
            'auto_increment' => true,
            'comment'        => 'foo',
        );

        $this->column->setProperties($properties);

        $this->assertSame('foo', $this->column->getName());
        $this->assertSame(Type::BOOLEAN, $this->column->getType()->getName());
        $this->assertSame(100, $this->column->getLength());
        $this->assertSame(5, $this->column->getPrecision());
        $this->assertSame(2, $this->column->getScale());
        $this->assertTrue($this->column->isUnsigned());
        $this->assertTrue($this->column->isFixed());
        $this->assertTrue($this->column->isNotNull());
        $this->assertSame('foo', $this->column->getDefault());
        $this->assertTrue($this->column->isAutoIncrement());
        $this->assertSame('foo', $this->column->getComment());
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The property "foo" of the column "foo" does not exist.
     */
    public function testPropertiesWithInvalidProperties()
    {
        $this->column->setProperties(array('foo' => 'bar'));
    }

    public function testToArray()
    {
        $properties = array(
            'name'           => 'foo',
            'type'           => Type::getType(Type::BOOLEAN),
            'length'         => 100,
            'precision'      => 5,
            'scale'          => 2,
            'unsigned'       => true,
            'fixed'          => true,
            'not_null'       => true,
            'default'        => 'foo',
            'auto_increment' => true,
            'comment'        => 'foo',
        );

        $expected = $properties;
        $expected['type'] = $expected['type']->getName();

        $this->column->setProperties($properties);
        $this->assertSame($expected, $this->column->toArray());
    }
}
