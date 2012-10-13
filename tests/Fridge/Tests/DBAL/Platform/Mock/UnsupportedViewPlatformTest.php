<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\Platform\Mock;

use Fridge\DBAL\Platform\AbstractPlatform;

/**
 * Unsupported view platform test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class UnsupportedViewPlatformTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\Platform\PlatformInterface */
    protected $platform;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->platform = new UnsupportedViewPlatformMock();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->platform);
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testCreateViewSQLQuery()
    {
        $viewMock = $this->getMock('Fridge\DBAL\Schema\View', array(), array(), '', false);

        $this->platform->getCreateViewSQLQuery($viewMock);
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testDropViewSQLQuery()
    {
        $viewMock = $this->getMock('Fridge\DBAL\Schema\View', array(), array(), '', false);

        $this->platform->getDropViewSQLQuery($viewMock);
    }
}

/**
 * Unsupported view platform mock.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class UnsupportedViewPlatformMock extends AbstractPlatform
{
    /**
     * {@inheritdoc}
     */
    public function supportView()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectDatabaseSQLQuery()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getSelectDatabasesSQLQuery()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getSelectSequencesSQLQuery($database)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getSelectTableCheckSQLQuery($table, $database)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getSelectTableColumnsSQLQuery($table, $database)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getSelectTableForeignKeysSQLQuery($table, $database)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getSelectTableIndexesSQLQuery($table, $database)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getSelectTableNamesSQLQuery($database)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getSelectTablePrimaryKeySQLQuery($table, $database)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getSelectViewsSQLQuery($database)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getSetTransactionIsolationSQLQuery($isolation)
    {

    }

    /**
     * {@inheritdoc}
     */
    protected function initializeMappedTypes()
    {

    }
}
