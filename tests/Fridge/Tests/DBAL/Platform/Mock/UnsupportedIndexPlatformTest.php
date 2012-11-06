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
 * Unsupported index platform test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class UnsupportedIndexPlatformTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\Platform\PlatformInterface */
    protected $platform;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->platform = new UnsupportedIndexPlatformMock();
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
    public function testCreateIndexSQLQueriesWithUniqueIndex()
    {
        $indexMock = $this->getMock('Fridge\DBAL\Schema\Index', array(), array(), '', false);
        $indexMock
            ->expects($this->any())
            ->method('isUnique')
            ->will($this->returnValue(true));

        $this->platform->getCreateIndexSQLQueries($indexMock, 'foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testCreateIndexSQLQueriesWithNonUniqueIndex()
    {
        $indexMock = $this->getMock('Fridge\DBAL\Schema\Index', array(), array(), '', false);

        $this->platform->getCreateIndexSQLQueries($indexMock, 'foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testDropIndexSQLQueries()
    {
        $indexMock = $this->getMock('Fridge\DBAL\Schema\Index', array(), array(), '', false);

        $this->platform->getDropIndexSQLQueries($indexMock, 'foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testCreateConstraintSQLQueriesWithUniqueIndex()
    {
        $indexMock = $this->getMock('Fridge\DBAL\Schema\Index', array(), array(), '', false);
        $indexMock
            ->expects($this->any())
            ->method('isUnique')
            ->will($this->returnValue(true));

        $this->platform->getCreateConstraintSQLQueries($indexMock, 'foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testCreateConstraintSQLQueriesWithNonUniqueIndex()
    {
        $indexMock = $this->getMock('Fridge\DBAL\Schema\Index', array(), array(), '', false);

        $this->platform->getCreateConstraintSQLQueries($indexMock, 'foo');
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testDropConstraintSQLQueriesWithIndex()
    {
        $indexMock = $this->getMock('Fridge\DBAL\Schema\Index', array(), array(), '', false);

        $this->platform->getDropConstraintSQLQueries($indexMock, 'foo');
    }
}

/**
 * Unsupported index platform mock.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class UnsupportedIndexPlatformMock extends AbstractPlatform
{
    /**
     * {@inheritdoc}
     */
    public function supportIndex()
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
