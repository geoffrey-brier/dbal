<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\SchemaManager;

/**
 * Schema manager test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class SchemaManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\SchemaManager\AbstractSchemaManager */
    protected $schemaManager;

    /** @var \Fridge\DBAL\Platform\PlatformInterface */
    protected $platformMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->platformMock = $this->getMock('Fridge\DBAL\Platform\PlatformInterface');

        $connection = $this->getMock('Fridge\DBAL\Connection\ConnectionInterface', array(), array(), '', false);
        $connection
            ->expects($this->any())
            ->method('getPlatform')
            ->will($this->returnValue($this->platformMock));

        $this->schemaManager = $this->getMockForAbstractClass(
            'Fridge\DBAL\SchemaManager\AbstractSchemaManager',
            array($connection)
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->schemaManager);
        unset($this->platformMock);
    }

    public function testGetViewsWhenNotSupported()
    {
        $this->platformMock
            ->expects($this->once())
            ->method('supportView')
            ->will($this->returnValue(false));

        $this->assertEmpty($this->schemaManager->getViews());
    }

    public function testGetTablePrimaryKeyWhenNotSupported()
    {
        $this->platformMock
            ->expects($this->once())
            ->method('supportPrimaryKey')
            ->will($this->returnValue(false));

        $this->assertNull($this->schemaManager->getTablePrimaryKey('foo'));
    }

    public function testGetTableForeignKeysWhenNotSupported()
    {
        $this->platformMock
            ->expects($this->once())
            ->method('supportForeignKey')
            ->will($this->returnValue(false));

        $this->assertEmpty($this->schemaManager->getTableForeignKeys('foo'));
    }

    public function testGetTableIndexesWhenNotSupported()
    {
        $this->platformMock
            ->expects($this->once())
            ->method('supportIndex')
            ->will($this->returnValue(false));

        $this->assertEmpty($this->schemaManager->getTableIndexes('foo'));
    }
}
