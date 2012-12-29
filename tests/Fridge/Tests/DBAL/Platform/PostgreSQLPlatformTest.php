<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\Platform;

use Fridge\DBAL\Connection\Connection,
    Fridge\DBAL\Platform\PostgreSQLPlatform,
    Fridge\DBAL\Schema\Column,
    Fridge\DBAL\Schema\Diff\ColumnDiff,
    Fridge\DBAL\Schema\ForeignKey,
    Fridge\DBAL\Schema\Index,
    Fridge\DBAL\Schema\PrimaryKey,
    Fridge\DBAL\Schema\Table,
    Fridge\DBAL\Type\Type;

/**
 * Postgre SQL platform test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PostgreSQLPlatformTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\Platform\PlatformInterface */
    protected $platform;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->platform = new PostgreSQLPlatform();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->platform);
    }

    public function testMappedTypes()
    {
        $this->assertTrue($this->platform->hasMappedType('int'));
    }

    public function testBigIntegerSQLDeclarationWithoutOptions()
    {
        $this->assertSame('BIGINT', $this->platform->getBigIntegerSQLDeclaration());
    }

    public function testBigIntegerSQLDeclarationWithAutoIncrementFlag()
    {
        $this->assertSame('BIGSERIAL', $this->platform->getBigIntegerSQLDeclaration(array('auto_increment' => true)));
    }

    public function testBlobSQLDeclaration()
    {
        $this->assertSame('BYTEA', $this->platform->getBlobSQLDeclaration());
    }

    public function testDateTimeSQLDeclaration()
    {
        $this->assertSame('TIMESTAMP(0) WITHOUT TIME ZONE', $this->platform->getDateTimeSQLDeclaration());
    }

    public function testIntegerSQLDeclarationWithoutOptions()
    {
        $this->assertSame('INT', $this->platform->getIntegerSQLDeclaration());
    }

    public function testIntegerSQLDeclarationWithAutoIncrementFlag()
    {
        $this->assertSame('SERIAL', $this->platform->getIntegerSQLDeclaration(array('auto_increment' => true)));
    }

    public function testTimeSQLDeclaration()
    {
        $this->assertSame('TIME(0) WITHOUT TIME ZONE', $this->platform->getTimeSQLDeclaration());
    }

    public function testSupportInlineTableColumnComment()
    {
        $this->assertFalse($this->platform->supportInlineTableColumnComment());
    }

    public function testSetTransactionIsolationSQLQuery()
    {
        $this->assertSame(
            'SET SESSION CHARACTERISTICS AS TRANSACTION ISOLATION LEVEL '.Connection::TRANSACTION_READ_COMMITTED,
            $this->platform->getSetTransactionIsolationSQLQuery(Connection::TRANSACTION_READ_COMMITTED)
        );
    }

    public function testCreateTableSQLQueries()
    {
        $table = new Table(
            'foo',
            array(
                new Column('foo', Type::getType(Type::INTEGER), array('comment' => 'foo')),
                new Column('bar', Type::getType(Type::INTEGER)),
                new Column('foo_bar', Type::getType(Type::INTEGER)),
            ),
            new PrimaryKey('pk1', array('foo')),
            array(
                new ForeignKey(
                    'fk1',
                    array('bar'),
                    'bar',
                    array('bar'),
                    ForeignKey::CASCADE,
                    ForeignKey::CASCADE
                )
            ),
            array(
                new Index('idx1', array('foo_bar')),
            )
        );

        $this->assertSame(
            array(
                'CREATE TABLE foo ('.
                'foo INT NOT NULL,'.
                ' bar INT,'.
                ' foo_bar INT,'.
                ' CONSTRAINT pk1 PRIMARY KEY (foo),'.
                ' CONSTRAINT fk1 FOREIGN KEY (bar) REFERENCES bar (bar) ON DELETE CASCADE ON UPDATE CASCADE'.
                ')',
                'COMMENT ON COLUMN foo.foo IS \'foo\'',
                'CREATE INDEX idx1 ON foo (foo_bar)',
            ),
            $this->platform->getCreateTableSQLQueries($table)
        );
    }

    public function testCreateTableSQLQueriesWithIndexDisabled()
    {
        $table = new Table(
            'foo',
            array(new Column('foo', Type::getType(Type::INTEGER))),
            null,
            array(),
            array(new Index('idx1', array('foo')))
        );

        $this->assertSame(
            array('CREATE TABLE foo (foo INT)'),
            $this->platform->getCreateTableSQLQueries($table, array('index' => false))
        );
    }

    public function testCreateColumnSQLQueries()
    {
        $column = new Column('foo', Type::getType(Type::INTEGER), array('comment' => 'foo'));

        $this->assertSame(
            array(
                'ALTER TABLE foo ADD COLUMN foo INT',
                'COMMENT ON COLUMN foo.foo IS \'foo\'',
            ),
            $this->platform->getCreateColumnSQLQueries($column, 'foo')
        );
    }

    public function testAlterColumnSQLQueriesWithNameDifference()
    {
        $columnDiff = new ColumnDiff(
            new Column('foo', Type::getType(Type::INTEGER)),
            new Column('bar', Type::getType(Type::INTEGER)),
            array()
        );

        $this->assertSame(
            array('ALTER TABLE foo RENAME COLUMN foo TO bar'),
            $this->platform->getAlterColumnSQLQueries($columnDiff, 'foo')
        );
    }

    public function testAlterColumnSQLQueriesWithTypeDifference()
    {
        $columnDiff = new ColumnDiff(
            new Column('foo', Type::getType(Type::INTEGER)),
            new Column('foo', Type::getType(Type::INTEGER)),
            array('type')
        );

        $this->assertSame(
            array('ALTER TABLE foo ALTER COLUMN foo TYPE INT'),
            $this->platform->getAlterColumnSQLQueries($columnDiff, 'foo')
        );
    }

    public function testAlterColumnSQLQueriesWithAddedNotNullDifference()
    {
        $columnDiff = new ColumnDiff(
            new Column('foo', Type::getType(Type::INTEGER)),
            new Column('foo', Type::getType(Type::INTEGER), array('not_null' => true)),
            array('not_null')
        );

        $this->assertSame(
            array('ALTER TABLE foo ALTER COLUMN foo SET NOT NULL'),
            $this->platform->getAlterColumnSQLQueries($columnDiff, 'foo')
        );
    }

    public function testAlterColumnSQLQueriesWithDroppedNotNullDifference()
    {
        $columnDiff = new ColumnDiff(
            new Column('foo', Type::getType(Type::INTEGER)),
            new Column('foo', Type::getType(Type::INTEGER)),
            array('not_null')
        );

        $this->assertSame(
            array('ALTER TABLE foo ALTER COLUMN foo DROP NOT NULL'),
            $this->platform->getAlterColumnSQLQueries($columnDiff, 'foo')
        );
    }

    public function testAlterColumnSQLQueriesWithAddedDefaultDifference()
    {
        $columnDiff = new ColumnDiff(
            new Column('foo', Type::getType(Type::INTEGER)),
            new Column('foo', Type::getType(Type::INTEGER), array('default' => 'foo')),
            array('default')
        );

        $this->assertSame(
            array('ALTER TABLE foo ALTER COLUMN foo SET DEFAULT \'foo\''),
            $this->platform->getAlterColumnSQLQueries($columnDiff, 'foo')
        );
    }

    public function testAlterColumnSQLQueriesWithDroppedDefaultDifference()
    {
        $columnDiff = new ColumnDiff(
            new Column('foo', Type::getType(Type::INTEGER)),
            new Column('foo', Type::getType(Type::INTEGER)),
            array('default')
        );

        $this->assertSame(
            array('ALTER TABLE foo ALTER COLUMN foo DROP DEFAULT'),
            $this->platform->getAlterColumnSQLQueries($columnDiff, 'foo')
        );
    }

    public function testAlterColumnSQLQueriesWithCommentDifference()
    {
        $columnDiff = new ColumnDiff(
            new Column('foo', Type::getType(Type::INTEGER)),
            new Column('foo', Type::getType(Type::INTEGER), array('comment' => 'foo')),
            array('comment')
        );

        $this->assertSame(
            array('COMMENT ON COLUMN foo.foo IS \'foo\''),
            $this->platform->getAlterColumnSQLQueries($columnDiff, 'foo')
        );
    }
}
