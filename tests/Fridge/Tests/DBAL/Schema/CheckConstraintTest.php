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

use Fridge\DBAL\Schema\CheckConstraint;

/**
 * Check constraint test.
 *
 * @author Benjamin Lazarecki <benjamin.lazarecki@gmail.com>
 */
class CheckConstraintTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\Schema\CheckConstraint */
    protected $checkConstraint;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->checkConstraint = new CheckConstraint('foo', 'bar');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->checkConstraint);
    }

    public function testInitialState()
    {
        $this->assertEquals('foo', $this->checkConstraint->getName());
        $this->assertEquals('bar', $this->checkConstraint->getConstraint());
        $this->assertEmpty($this->checkConstraint->getColumnNames());
    }

    public function testGenerateName()
    {
        $checkConstraint = new CheckConstraint(null, 'bar');
        $this->assertRegExp('/^constraint_[a-z0-9]{19}$/', $checkConstraint->getName());
    }

    public function testColumnNamesWithValidValues()
    {
        $columnNames = array('foo', 'bar');
        $this->checkConstraint->setColumnNames($columnNames);

        $this->assertEquals($columnNames, $this->checkConstraint->getColumnNames());
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The column name of the check constraint "foo" must be a string.
     */
    public function testColumnNamesWithInvalidValues()
    {
        $this->checkConstraint->setColumnNames(array(true));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The constraint of the check constraint "foo" must be a string.
     */
    public function testConstraintWithInvalidValues()
    {
        $this->checkConstraint->setConstraint(null);
    }
}
