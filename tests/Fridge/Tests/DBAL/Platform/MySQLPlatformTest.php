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
    Fridge\DBAL\Schema,
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
     * @expectExceptionMessage The method "Fridge\DBAL\Platform\MySQLPlatform::getCreateSequenceSQLQuery" is not supported.
     */
    public function testCreateSequenceSQLQuery()
    {
        $sequence = new Schema\Sequence('foo');

        $this->platform->getCreateSequenceSQLQuery($sequence);
    }

    public function testCreatePrimaryKeySQLQuery()
    {
        $primaryKey = new Schema\PrimaryKey('foo', array('foo'));

        $this->assertSame(
            'ALTER TABLE foo ADD CONSTRAINT PRIMARY KEY (foo)',
            $this->platform->getCreatePrimaryKeySQLQuery($primaryKey, 'foo')
        );
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testCreateCheckSQLQuery()
    {
        $check = new Schema\Check('foo', 'bar');

        $this->platform->getCreateCheckSQLQuery($check, 'zaz');
    }

    public function testCreateTableSQLQueries()
    {
        $table = new Schema\Table(
            'foo',
            array(
                new Schema\Column('foo', Type::getType(Type::INTEGER)),
            )
        );

        $sqls = $this->platform->getCreateTableSQLQueries($table);

        $this->assertSame('ENGINE = InnoDB', substr($sqls[0], -15));
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     * @expectExceptionMessage The method "Fridge\DBAL\Platform\MySQLPlatform::getSelectDropSQLQuery" is not supported.
     */
    public function testDropSequenceSQLQuery()
    {
        $sequence = new Schema\Sequence('foo');

        $this->platform->getDropSequenceSQLQuery($sequence);
    }

    public function testDropPrimaryKeySQLQuery()
    {
        $primaryKey = new Schema\PrimaryKey('foo');

        $this->assertSame(
            'ALTER TABLE bar DROP PRIMARY KEY',
            $this->platform->getDropPrimaryKeySQLQuery($primaryKey, 'bar')
        );
    }

    public function testDropIndexSQLQuery()
    {
        $index = new Schema\Index('foo', array(), true);

        $this->assertSame(
            'DROP INDEX foo ON bar',
            $this->platform->getDropIndexSQLQuery($index, 'bar')
        );
    }

    public function testDropForeignKeySQLQuery()
    {
        $foreignKey = new Schema\ForeignKey('foo', array(), 'bar', array());

        $this->assertSame(
            'ALTER TABLE bar DROP FOREIGN KEY foo',
            $this->platform->getDropForeignKeySQLQuery($foreignKey, 'bar')
        );
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testDropCheckSQLQuery()
    {
        $check = new Schema\Check('foo', 'bar');

        $this->platform->getDropCheckSQLQuery($check, 'zaz');
    }

    public function testQuoteIdentifier()
    {
        $this->assertSame('`', $this->platform->getQuoteIdentifier());
    }
}
