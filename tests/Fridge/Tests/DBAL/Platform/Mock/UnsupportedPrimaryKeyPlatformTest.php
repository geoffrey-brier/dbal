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
 * Unsupported primary key platform test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class UnsupportedPrimaryKeyPlatformTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\Platform\PlatformInterface */
    protected $platform;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->platform = new UnsupportedPrimaryKeyPlatformMock();
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
    public function testCreatePrimaryKeySQLQuery()
    {
        $primaryKeyMock = $this->getMock('Fridge\DBAL\Schema\PrimaryKey', array(), array(), '', false);

        $this->platform->getCreatePrimaryKeySQLQuery($primaryKeyMock, 'foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testDropPrimaryKeySQLQuery()
    {
        $primaryKeyMock = $this->getMock('Fridge\DBAL\Schema\PrimaryKey', array(), array(), '', false);

        $this->platform->getDropPrimaryKeySQLQuery($primaryKeyMock, 'foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testCreateConsraintSQLQueryWithPrimaryKey()
    {
        $primaryKeyMock = $this->getMock('Fridge\DBAL\Schema\PrimaryKey', array(), array(), '', false);

        $this->platform->getCreateConstraintSQLQuery($primaryKeyMock, 'foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testDropConstraintSQLQueryWithPrimaryKey()
    {
        $primaryKeyMock = $this->getMock('Fridge\DBAL\Schema\PrimaryKey', array(), array(), '', false);

        $this->platform->getDropConstraintSQLQuery($primaryKeyMock, 'foo');
    }
}

/**
 * Unsupported primary key platform mock.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class UnsupportedPrimaryKeyPlatformMock extends AbstractPlatform
{
    /**
     * {@inheritdoc}
     */
    public function supportPrimaryKey()
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
