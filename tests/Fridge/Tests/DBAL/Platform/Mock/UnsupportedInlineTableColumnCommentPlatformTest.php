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

use Fridge\DBAL\Platform\AbstractPlatform,
    Fridge\DBAL\Schema\Column,
    Fridge\DBAL\Schema\Diff\ColumnDiff,
    Fridge\DBAL\Type\Type;

/**
 * Unsupported inline table column comment platform test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class UnsupportedInlineTableColumnCommentPlatformTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\Platform\PlatformInterface */
    protected $platform;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->platform = new UnsupportedInlineTableColumnCommentPlatformMock();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->platform);
    }

    public function testAlterColumnSQLQueries()
    {
        $columnDiff = new ColumnDiff(
            new Column('foo', Type::getType(Type::INTEGER)),
            new Column('bar', Type::getType(Type::INTEGER), array('comment' => 'foo')),
            array()
        );

        $this->assertSame(
            array(
                'ALTER TABLE foo ALTER COLUMN foo bar INT',
                'COMMENT ON COLUMN foo.bar IS \'foo\'',
            ),
            $this->platform->getAlterColumnSQLQueries($columnDiff, 'foo')
        );
    }
}

/**
 * Unsupported inline table column comment platform mock.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class UnsupportedInlineTableColumnCommentPlatformMock extends AbstractPlatform
{
    /**
     * {@inheritdoc}
     */
    public function supportInlineTableColumnComment()
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
