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

use Fridge\DBAL\Schema\View;

/**
 * View test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ViewTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\Schema\View */
    protected $view;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->view = new View('foo', 'bar');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->view);
    }

    public function testInitialState()
    {
        $this->assertEquals('foo', $this->view->getName());
        $this->assertEquals('bar', $this->view->getSQL());
    }

    public function testSQLWithValidValue()
    {
        $this->view->setSQL('foo');
        $this->assertEquals('foo', $this->view->getSQL());
    }

    /**
     * @expectedException Fridge\DBAL\Exception\SchemaException
     * @expectedExceptionMessage The SQL query of the view "foo" must be a string.
     */
    public function testSQLWithInvalidValue()
    {
        $this->view->setSQL(true);
    }

    public function testSQLWithNullValue()
    {
        $this->view->setSQL(null);
        $this->assertNull($this->view->getSQL());
    }
}
