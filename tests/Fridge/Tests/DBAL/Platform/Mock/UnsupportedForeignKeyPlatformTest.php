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
 * Unsupported foreign key platform test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class UnsupportedForeignKeyPlatformTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\Platform\PlatformInterface */
    protected $platform;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->platform = new UnsupportedForeignKeyPlatformMock();
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
    public function testCreateForeignKeySQLQueries()
    {
        $foreignKeyMock = $this->getMock('Fridge\DBAL\Schema\ForeignKey', array(), array(), '', false);

        $this->platform->getCreateForeignKeySQLQueries($foreignKeyMock, 'foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testDropForeignKeySQLQuery()
    {
        $foreignKeyMock = $this->getMock('Fridge\DBAL\Schema\ForeignKey', array(), array(), '', false);

        $this->platform->getDropForeignKeySQLQuery($foreignKeyMock, 'foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testCreateConstraintSQLQueriesWithForeignKey()
    {
        $foreignKeyMock = $this->getMock('Fridge\DBAL\Schema\ForeignKey', array(), array(), '', false);

        $this->platform->getCreateConstraintSQLQueries($foreignKeyMock, 'foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testDropConstraintSQLQueriesWithForeignKey()
    {
        $foreignKeyMock = $this->getMock('Fridge\DBAL\Schema\ForeignKey', array(), array(), '', false);

        $this->platform->getDropConstraintSQLQueries($foreignKeyMock, 'foo');
    }
}

/**
 * Unsupported foreign key platform mock.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class UnsupportedForeignKeyPlatformMock extends AbstractPlatform
{
    /**
     * {@inheritdoc}
     */
    public function supportForeignKey()
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
