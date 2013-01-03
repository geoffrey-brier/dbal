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

use \ReflectionMethod;

use Fridge\DBAL\Connection\Connection,
    Fridge\DBAL\Platform\MySQLPlatform,
    Fridge\DBAL\Schema\Check,
    Fridge\DBAL\Schema\Column,
    Fridge\DBAL\Schema\Diff\ColumnDiff,
    Fridge\DBAL\Schema\Diff\SchemaDiff,
    Fridge\DBAL\Schema\ForeignKey,
    Fridge\DBAL\Schema\Index,
    Fridge\DBAL\Schema\PrimaryKey,
    Fridge\DBAL\Schema\Schema,
    Fridge\DBAL\Schema\Sequence,
    Fridge\DBAL\Schema\Table,
    Fridge\DBAL\Type\Type;

/**
 * MySQL Platform test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class MySQLPlatformTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\Platform\PlatformInterface */
    protected $platform;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->platform = new MySQLPlatform();
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

    /**
     * Gets the MySQLPlatform::getIntegerSQLDeclarationSnippet method.
     *
     * @return \ReflectionMethod The MySQLPlatform::getIntegerSQLDeclarationSnippet method.
     */
    protected function getIntegerSQLDeclarationSnippetMethod()
    {
        $method = new ReflectionMethod('Fridge\DBAL\Platform\MySQLPlatform', 'getIntegerSQLDeclarationSnippet');
        $method->setAccessible(true);

        return $method;
    }

    public function testIntegerSQLDeclarationSnippetWithoutOptions()
    {
        $this->assertSame('', $this->getIntegerSQLDeclarationSnippetMethod()->invoke($this->platform, array()));
    }

    public function testIntegerSQLDeclarationSnippetWithLength()
    {
        $this->assertSame(
            '(100)',
            $this->getIntegerSQLDeclarationSnippetMethod()->invoke($this->platform, array('length' => 100))
        );
    }

    public function testIntegerSQLDeclarationSnippetWithUnsignedFlag()
    {
        $this->assertSame(
            ' UNSIGNED',
            $this->getIntegerSQLDeclarationSnippetMethod()->invoke($this->platform, array('unsigned' => true))
        );
    }

    public function testIntegerSQLDeclarationSnippetWithAutoIncrementFlag()
    {
        $this->assertSame(
            ' AUTO_INCREMENT',
            $this->getIntegerSQLDeclarationSnippetMethod()->invoke($this->platform, array('auto_increment' => true))
        );
    }

    public function testBigIntegerSQLDeclaration()
    {
        $this->assertSame('BIGINT(100)', $this->platform->getBigIntegerSQLDeclaration(array('length' => 100)));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     * @expectedExceptionMessage The string type prefix length must be a strict positive integer.
     */
    public function testBlobSQLDeclarationWithInvalidNegativeValue()
    {
        $this->platform->getBlobSQLDeclaration(array('length' => -42));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testBlobSQLDeclarationWithInvalidZeroValue()
    {
        $this->platform->getBlobSQLDeclaration(array('length' => 0));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testBlobSQLDeclarationWithInvalidStringValue()
    {
        $this->platform->getBlobSQLDeclaration(array('length' => 'foo'));
    }

    public function testBlobSQLDeclarationWithoutLength()
    {
        $this->assertSame('LONGBLOB', $this->platform->getBlobSQLDeclaration());
    }

    public function testBlobSQLDeclarationForTinyBlob()
    {
        $this->assertSame('TINYBLOB', $this->platform->getBlobSQLDeclaration(array('length' => 255)));
    }

    public function testBlobSQLDeclarationForBlob()
    {
        $this->assertSame('BLOB', $this->platform->getBlobSQLDeclaration(array('length' => 65535)));
    }

    public function testBlobSQLDeclarationForMediumBlob()
    {
        $this->assertSame('MEDIUMBLOB', $this->platform->getBlobSQLDeclaration(array('length' => 16777215)));
    }

    public function testBlobSQLDeclarationForLongBlob()
    {
        $this->assertSame('LONGBLOB', $this->platform->getBlobSQLDeclaration(array('length' => 16777216)));
    }

    public function testBooleanSQLDeclaration()
    {
        $this->assertSame('TINYINT(1)', $this->platform->getBooleanSQLDeclaration());
    }

    public function testClobSQLDeclarationWithoutLength()
    {
        $this->assertSame('LONGTEXT', $this->platform->getClobSQLDeclaration());
    }

    public function testClobSQLDeclarationWithLengthLowerThan266()
    {
        $this->assertSame('TINYTEXT', $this->platform->getClobSQLDeclaration(array('length' => 255)));
    }

    public function testClobSQLDeclarationWithLengthLowerThan65536()
    {
        $this->assertSame('TEXT', $this->platform->getClobSQLDeclaration(array('length' => 65535)));
    }

    public function testClobSQLDeclarationWithLengthLowerThan16777216()
    {
        $this->assertSame('MEDIUMTEXT', $this->platform->getClobSQLDeclaration(array('length' => 16777215)));
    }

    public function testClobSQLDeclarationWithLengthGreaterThan16777215()
    {
        $this->assertSame('LONGTEXT', $this->platform->getClobSQLDeclaration(array('length' => 16777216)));
    }

    public function testDateTimeSQLDeclarationWithoutOptions()
    {
        $this->assertSame('DATETIME', $this->platform->getDateTimeSQLDeclaration());
    }

    public function testDateTimeSQLDeclarationWithVersionFlag()
    {
        $this->assertSame('TIMESTAMP', $this->platform->getDateTimeSQLDeclaration(array('version' => true)));
    }

    public function testIntegerSQLDeclaration()
    {
        $this->assertSame('INT(100)', $this->platform->getIntegerSQLDeclaration(array('length' => 100)));
    }

    public function testSmallIntegerSQLDeclaration()
    {
        $this->assertSame('SMALLINT(100)', $this->platform->getSmallIntegerSQLDeclaration(array('length' => 100)));
    }

    public function testSupportSequence()
    {
        $this->assertFalse($this->platform->supportSequence());
    }

    public function testSetTransactionIsolationSQLQuery()
    {
        $this->assertSame(
            'SET SESSION TRANSACTION ISOLATION LEVEL '.Connection::TRANSACTION_READ_COMMITTED,
            $this->platform->getSetTransactionIsolationSQLQuery(Connection::TRANSACTION_READ_COMMITTED)
        );
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     * @expectExceptionMessage The method "Fridge\DBAL\Platform\MySQLPlatform::getSelectSequencesSQLQuery" is not supported.
     */
    public function testSelectSequenceSQLQuery()
    {
        $this->platform->getSelectSequencesSQLQuery('foo');
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testSelectTableCheckSQLQuery()
    {
        $this->platform->getSelectTableCheckSQLQuery('foo', 'bar');
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     * @expectExceptionMessage The method "Fridge\DBAL\Platform\MySQLPlatform::getCreateSequenceSQLQueries" is not supported.
     */
    public function testCreateSequenceSQLQueries()
    {
        $sequence = new Sequence('foo');

        $this->platform->getCreateSequenceSQLQueries($sequence);
    }

    public function testCreatePrimaryKeySQLQueries()
    {
        $primaryKey = new PrimaryKey('foo', array('foo'));

        $this->assertSame(
            array('ALTER TABLE foo ADD CONSTRAINT PRIMARY KEY (foo)'),
            $this->platform->getCreatePrimaryKeySQLQueries($primaryKey, 'foo')
        );
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testCreateCheckSQLQueries()
    {
        $check = new Check('foo', 'bar');

        $this->platform->getCreateCheckSQLQueries($check, 'zaz');
    }

    public function testCreateTableSQLQueries()
    {
        $table = new Table('foo', array(new Column('foo', Type::getType(Type::INTEGER))));

        $sqls = $this->platform->getCreateTableSQLQueries($table);

        $this->assertSame('ENGINE = InnoDB', substr($sqls[0], -15));
    }

    public function testRenameDatabaseSQLQueries()
    {
        $oldTable = new Schema('foo', array(new Table('foo')));
        $newTable = new Schema('bar', array(new Table('foo')));

        $schemaDiff = new SchemaDiff($oldTable, $newTable);

        $this->assertSame(
            array(
                'CREATE DATABASE bar',
                'RENAME TABLE foo.foo TO bar.foo',
                'DROP DATABASE foo',
            ),
            $this->platform->getRenameDatabaseSQLQueries($schemaDiff)
        );
    }

    public function testAlterColumnSQLQueries()
    {
        $columnDiff = new ColumnDiff(
            new Column('foo', Type::getType(Type::INTEGER)),
            new Column('bar', Type::getType(Type::INTEGER)),
            array()
        );

        $this->assertSame(
            array('ALTER TABLE foo CHANGE COLUMN foo bar INT'),
            $this->platform->getAlterColumnSQLQueries($columnDiff, 'foo')
        );
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     * @expectExceptionMessage The method "Fridge\DBAL\Platform\MySQLPlatform::getSelectDropSQLQuery" is not supported.
     */
    public function testDropSequenceSQLQueries()
    {
        $sequence = new Sequence('foo');

        $this->platform->getDropSequenceSQLQueries($sequence);
    }

    public function testDropPrimaryKeySQLQueries()
    {
        $primaryKey = new PrimaryKey('foo');

        $this->assertSame(
            array('ALTER TABLE bar DROP PRIMARY KEY'),
            $this->platform->getDropPrimaryKeySQLQueries($primaryKey, 'bar')
        );
    }

    public function testDropIndexSQLQueries()
    {
        $index = new Index('foo', array(), true);

        $this->assertSame(
            array('ALTER TABLE bar DROP INDEX foo'),
            $this->platform->getDropIndexSQLQueries($index, 'bar')
        );
    }

    public function testDropForeignKeySQLQueries()
    {
        $foreignKey = new ForeignKey('foo', array(), 'bar', array());

        $this->assertSame(
            array('ALTER TABLE bar DROP FOREIGN KEY foo'),
            $this->platform->getDropForeignKeySQLQueries($foreignKey, 'bar')
        );
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testDropCheckSQLQueries()
    {
        $check = new Check('foo', 'bar');

        $this->platform->getDropCheckSQLQueries($check, 'zaz');
    }

    public function testQuoteIdentifier()
    {
        $this->assertSame('`', $this->platform->getQuoteIdentifier());
    }
}
