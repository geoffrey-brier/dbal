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
    Fridge\DBAL\Schema,
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
        $this->assertEquals('BIGINT', $this->platform->getBigIntegerSQLDeclaration());
    }

    public function testBigIntegerSQLDeclarationWithAutoIncrementFlag()
    {
        $this->assertEquals('BIGSERIAL', $this->platform->getBigIntegerSQLDeclaration(array('auto_increment' => true)));
    }

    public function testDateTimeSQLDeclaration()
    {
        $this->assertEquals('TIMESTAMP(0) WITHOUT TIME ZONE', $this->platform->getDateTimeSQLDeclaration());
    }

    public function testIntegerSQLDeclarationWithoutOptions()
    {
        $this->assertEquals('INT', $this->platform->getIntegerSQLDeclaration());
    }

    public function testIntegerSQLDeclarationWithAutoIncrementFlag()
    {
        $this->assertEquals('SERIAL', $this->platform->getIntegerSQLDeclaration(array('auto_increment' => true)));
    }

    public function testTimeSQLDeclaration()
    {
        $this->assertEquals('TIME(0) WITHOUT TIME ZONE', $this->platform->getTimeSQLDeclaration());
    }

    public function testSupportInlineTableColumnComment()
    {
        $this->assertFalse($this->platform->supportInlineTableColumnComment());
    }

    public function testSetTransactionIsolationSQLQuery()
    {
        $this->assertEquals(
            'SET SESSION CHARACTERISTICS AS TRANSACTION ISOLATION LEVEL '.Connection::TRANSACTION_READ_COMMITTED,
            $this->platform->getSetTransactionIsolationSQLQuery(Connection::TRANSACTION_READ_COMMITTED)
        );
    }

    public function testCreateTableSQLQueries()
    {
        $table = new Schema\Table(
            'foo',
            array(
                new Schema\Column('foo', Type::getType(Type::INTEGER), array('comment' => 'foo')),
                new Schema\Column('bar', Type::getType(Type::INTEGER)),
            )
        );

        $this->assertEquals(
            array(
                'CREATE TABLE foo (foo INT, bar INT)',
                'COMMENT ON COLUMN foo.foo IS \'foo\'',
            ),
            $this->platform->getCreateTableSQLQueries($table)
        );
    }
}
