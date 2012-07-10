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

use Fridge\DBAL\Schema;

/**
 * Foreign key test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ForeignKeyTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\Schema\ForeignKey */
    protected $foreignKey;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->foreignKey = new Schema\ForeignKey('foo', array(), 'bar', array());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->foreignKey);
    }

    public function testInitialState()
    {
        $this->assertEquals('foo', $this->foreignKey->getName());
        $this->assertEmpty($this->foreignKey->getColumnNames());
        $this->assertEmpty($this->foreignKey->getLocalColumnNames());
        $this->assertEquals('bar', $this->foreignKey->getForeignTableName());
        $this->assertEmpty($this->foreignKey->getForeignColumnNames());
    }

    public function testGeneratedName()
    {
        $foreignKey = new Schema\ForeignKey(null, array(), 'bar', array());
        $this->assertRegExp('/^fk_[a-z0-9]{17}$/', $foreignKey->getName());
    }

    public function testLocalColumnNamesWithValidValues()
    {
        $columnNames = array('foo', 'bar');
        $this->foreignKey->setLocalColumnNames($columnNames);

        $this->assertEquals($columnNames, $this->foreignKey->getLocalColumnNames());
        $this->assertEquals($columnNames, $this->foreignKey->getColumnNames());
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The local column name of the foreign key "foo" must be a string.
     */
    public function testLocalColumnNamesWithInvalidValues()
    {
        $this->foreignKey->setLocalColumnNames(array(true));
    }

    public function testForeignTableNameWithValidValue()
    {
        $this->foreignKey->setForeignTableName('foo');
        $this->assertEquals('foo', $this->foreignKey->getForeignTableName());
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The foreign table name of the foreign key "foo" must be a string.
     */
    public function testForeignTableNameWithInvalidValue()
    {
        $this->foreignKey->setForeignTableName(true);
    }

    public function testForeignColumnNamesWithValidValues()
    {
        $columnNames = array('foo', 'bar');
        $this->foreignKey->setForeignColumnNames($columnNames);

        $this->assertEquals($columnNames, $this->foreignKey->getForeignColumnNames());
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The foreign column name of the foreign key "foo" must be a string.
     */
    public function testForeignColumnNamesWithInvalidValues()
    {
        $this->foreignKey->setForeignColumnNames(array(true));
    }
}
