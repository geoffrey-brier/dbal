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

use \DateTime,
    \ReflectionMethod;

use Fridge\DBAL\Connection\Connection,
    Fridge\DBAL\Schema,
    Fridge\DBAL\Type\Type;

/**
 * Platform test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PlatformTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\Platform\AbstractPlatform */
    protected $platform;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->platform = $this->getMockForAbstractClass('Fridge\DBAL\Platform\AbstractPlatform');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->platform);
    }

    /**
     * Gets a list of transaction isolations.
     *
     * @return array A list of transaction isolations.
     */
    static public function transactionIsolationProvider()
    {
        return array(
            array(Connection::TRANSACTION_READ_COMMITTED),
            array(Connection::TRANSACTION_READ_UNCOMMITTED),
            array(Connection::TRANSACTION_REPEATABLE_READ),
            array(Connection::TRANSACTION_SERIALIZABLE),
        );
    }

    /**
     * Initializes the platform mapped types.
     */
    protected function initializeMappedTypes()
    {
        $property = new \ReflectionProperty('Fridge\DBAL\Platform\AbstractPlatform', 'mappedTypes');
        $property->setAccessible(true);

        $property->setValue($this->platform, array('foo' => 'bar'));
    }

    /**
     * Initializes the platform mandatory types.
     */
    protected function initializeMandatoryTypes()
    {
        $property = new \ReflectionProperty('Fridge\DBAL\Platform\AbstractPlatform', 'mandatoryTypes');
        $property->setAccessible(true);

        $property->setValue($this->platform, array('foo'));
    }

    public function testInitialState()
    {
        $this->assertTrue($this-> platform->useStrictMappedType());
        $this->assertEquals(Type::TEXT, $this->platform->getFallbackMappedType());
    }

    public function testHasMappedType()
    {
        $this->initializeMappedTypes();

        $this->assertTrue($this->platform->hasMappedType('foo'));
        $this->assertFalse($this->platform->hasMappedType('bar'));
    }

    public function testGetMappedTypeWithValidValue()
    {
        $this->initializeMappedTypes();

        $this->assertEquals('bar', $this->platform->getMappedType('foo'));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     * @expectedExceptionMessage The mapped type "bar" does not exist.
     */
    public function testGetMappedTypeWithInvalidValueAndStrictMappedTypeEnable()
    {
        $this->initializeMappedTypes();

        $this->platform->getMappedType('bar');
    }

    public function testGetMappedTypeWithInvalidValueAndStrictMappedTypeDisable()
    {
        $this->initializeMappedTypes();

        $this->platform->useStrictMappedType(false);
        $this->assertEquals($this->platform->getFallbackMappedType(), $this->platform->getMappedType('bar'));
    }

    public function testAddMappedTypeWithValidValue()
    {
        $this->initializeMappedTypes();

        $this->platform->addMappedType('bar', 'foo');

        $this->assertTrue($this->platform->hasMappedType('bar'));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     * @expectedExceptionMessage The mapped type "foo" already exists.
     */
    public function testAddMappedTypeWithInvalidValue()
    {
        $this->initializeMappedTypes();

        $this->platform->addMappedType('foo', 'bar');
    }

    public function testOverrideMappedTypeWithValidValue()
    {
        $this->initializeMappedTypes();

        $this->platform->overrideMappedType('foo', 'foo');

        $this->assertEquals('foo', $this->platform->getMappedType('foo'));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testOverrideMappedTypeWithInvalidValue()
    {
        $this->initializeMappedTypes();

        $this->platform->overrideMappedType('bar', 'foo');
    }

    public function testRemoveMappedTypeWithValidValue()
    {
        $this->initializeMappedTypes();

        $this->platform->removeMappedType('foo');

        $this->assertFalse($this->platform->hasMappedType('foo'));
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testRemoveMappedTypeWithInvalidValue()
    {
        $this->initializeMappedTypes();

        $this->platform->removeMappedType('bar');
    }

    public function testStrictMappedType()
    {
        $this->platform->useStrictMappedType(false);
        $this->assertFalse($this->platform->useStrictMappedType());
    }

    public function testFallbackMappedTypeWithValidValue()
    {
        $this->platform->setFallbackMappedType(Type::INTEGER);
        $this->assertEquals(Type::INTEGER, $this->platform->getFallbackMappedType());
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\TypeException
     */
    public function testFallbackMappedTypeWithInvalidValue()
    {
        $this->platform->setFallbackMappedType('foo');
    }

    public function testHasMandatoryType()
    {
        $this->initializeMandatoryTypes();

        $this->assertTrue($this->platform->hasMandatoryType('foo'));
        $this->assertFalse($this->platform->hasMandatoryType('bar'));
    }

    public function testAddMandatoryTypeWithValidValue()
    {
        $this->initializeMandatoryTypes();

        $this->platform->addMandatoryType('bar');

        $this->assertTrue($this->platform->hasMandatoryType('bar'));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     * @expectedExceptionMessage The mandatory type "foo" already exists.
     */
    public function testAddMandatoryTypeWithInvalidValue()
    {
        $this->initializeMandatoryTypes();

        $this->platform->addMandatoryType('foo');
    }

    public function testRemoveMandatoryTypeWithValidValue()
    {
        $this->initializeMandatoryTypes();

        $this->platform->removeMandatoryType('foo');

        $this->assertFalse($this->platform->hasMandatoryType('foo'));
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     * @expectedExceptionMessage The mandatory type "bar" does not exist.
     */
    public function testRemoveMandatoryTypeWithInvalidValue()
    {
        $this->initializeMandatoryTypes();

        $this->platform->removeMandatoryType('bar');
    }

    public function testBigIntegerSQLDeclaration()
    {
        $this->assertEquals('BIGINT', $this->platform->getBigIntegerSQLDeclaration());
    }

    public function testBooleanSQLDeclaration()
    {
        $this->assertEquals('BOOLEAN', $this->platform->getBooleanSQLDeclaration());
    }

    public function testClobSQLDeclaration()
    {
        $this->assertEquals('TEXT', $this->platform->getClobSQLDeclaration());
    }

    public function testDateSQLDeclaration()
    {
        $this->assertEquals('DATE', $this->platform->getDateSQLDeclaration());
    }

    public function testDateTimeSQLDeclaration()
    {
        $this->assertEquals('DATETIME', $this->platform->getDateTimeSQLDeclaration());
    }

    public function testDecimalSQLDeclarationWithoutOptions()
    {
        $this->assertEquals('NUMERIC(5, 2)', $this->platform->getDecimalSQLDeclaration());
    }

    public function testDecimalSQLDeclarationWithPrecision()
    {
        $this->assertEquals('NUMERIC(3, 2)', $this->platform->getDecimalSQLDeclaration(array('precision' => 3)));
    }

    public function testDecimalSQLDeclarationWithScale()
    {
        $this->assertEquals('NUMERIC(5, 1)', $this->platform->getDecimalSQLDeclaration(array('scale' => 1)));
    }

    public function testFloatSQLDeclaration()
    {
        $this->assertEquals('DOUBLE PRECISION', $this->platform->getFloatSQLDeclaration());
    }

    public function testIntegerSQLDeclaration()
    {
        $this->assertEquals('INT', $this->platform->getIntegerSQLDeclaration());
    }

    public function testSmallIntegerSQLDeclaration()
    {
        $this->assertEquals('SMALLINT', $this->platform->getSmallIntegerSQLDeclaration());
    }

    public function testTimeSQLDeclaration()
    {
        $this->assertEquals('TIME', $this->platform->getTimeSQLDeclaration());
    }

    public function testVarcharSQLDeclarationWithoutOptions()
    {
        $this->assertEquals('VARCHAR(255)', $this->platform->getVarcharSQLDeclaration(array()));
    }

    public function testVarcharSQLDeclarationWithFixedFlag()
    {
        $this->assertEquals(
            'CHAR(20)',
            $this->platform->getVarcharSQLDeclaration(array('length' => 20, 'fixed' => true))
        );
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     * @expectedExceptionMessage The varchar length must be a positive integer.
     */
    public function testVarcharSQLDeclarationWithInvalidLength()
    {
        $this->platform->getVarcharSQLDeclaration(array('length' => -3));
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     * @expectedExceptionMessage The varchar fixed flag must be a boolean.
     */
    public function testVarcharSQLDeclarationWithInvalidFixedFlag()
    {
        $this->platform->getVarcharSQLDeclaration(array('fixed' => 'foo'));
    }

    public function testVarcharSQLDeclarationWithLenngthGreaterThanMaxVarcharLength()
    {
        $this->assertEquals('TEXT', $this->platform->getVarcharSQLDeclaration(array('length' => 65536)));
    }

    public function testDefaultTransactionIsolation()
    {
        $this->assertEquals(Connection::TRANSACTION_READ_COMMITTED, $this->platform->getDefaultTransactionIsolation());
    }

    public function testDateFormat()
    {
        $this->assertEquals('Y-m-d', $this->platform->getDateFormat());
    }

    public function testTimeFormat()
    {
        $this->assertEquals('H:i:s', $this->platform->getTimeFormat());
    }

    public function testDateTimeFormat()
    {
        $this->assertEquals('Y-m-d H:i:s', $this->platform->getDateTimeFormat());
    }

    public function testSupportSavepoint()
    {
        $this->assertTrue($this->platform->supportSavepoint());
    }

    public function testSupportTransactionIsolation()
    {
        $this->assertTrue($this->platform->supportTransactionIsolation());
    }

    public function testSupportSequence()
    {
        $this->assertTrue($this->platform->supportSequence());
    }

    public function testSupportInlineTableComment()
    {
        $this->assertTrue($this->platform->supportInlineTableColumnComment());
    }

    public function testCreateSavepointSQLQuery()
    {
        $this->assertEquals('SAVEPOINT foo', $this->platform->getCreateSavepointSQLQuery('foo'));
    }

    public function testReleaseSavepointSQLQuery()
    {
        $this->assertEquals('RELEASE SAVEPOINT foo', $this->platform->getReleaseSavepointSQLQuery('foo'));
    }

    public function testRollbackSavepointSQLQuery()
    {
        $this->assertEquals('ROLLBACK TO SAVEPOINT foo', $this->platform->getRollbackSavepointSQLQuery('foo'));
    }

    public function testSetCharsetSQLQuery()
    {
        $this->assertEquals('SET NAMES \'foo\'', $this->platform->getSetCharsetSQLQuery('foo'));
    }

    public function testCreateDatabaseSQLQuery()
    {
        $this->assertEquals('CREATE DATABASE foo', $this->platform->getCreateDatabaseSQLQuery('foo'));
    }

    public function testCreateSequenceSQLQuery()
    {
        $sequence = new Schema\Sequence('foo', 1, 1);

        $this->assertEquals(
            'CREATE SEQUENCE foo INCREMENT BY 1 MINVALUE 1 START WITH 1',
            $this->platform->getCreateSequenceSQLQuery($sequence)
        );
    }

    public function testCreateViewSQLQuery()
    {
        $view = new Schema\View('foo', 'bar');

        $this->assertEquals('CREATE VIEW foo AS bar', $this->platform->getCreateViewSQLQuery($view));
    }

    public function testCreateConstraintSQLQueryWithPrimaryKey()
    {
        $primaryKey = new Schema\PrimaryKey('foo', array('bar'));

        $this->assertEquals(
            'ALTER TABLE foo ADD CONSTRAINT foo PRIMARY KEY (bar)',
            $this->platform->getCreateConstraintSQLQuery($primaryKey, 'foo')
        );
    }

    public function testCreateConstraintSQLQueryWithForeignKey()
    {
        $foreignKey = new Schema\ForeignKey('foo', array('foo'), 'bar', array('bar'));

        $this->assertEquals(
            'ALTER TABLE foo ADD CONSTRAINT foo FOREIGN KEY (foo) REFERENCES bar (bar)',
            $this->platform->getCreateConstraintSQLQuery($foreignKey, 'foo')
        );
    }

    public function testCreateConstraintSQLQueryWithUniqueIndex()
    {
        $index = new Schema\Index('foo', array('foo'), true);

        $this->assertEquals(
            'ALTER TABLE foo ADD CONSTRAINT foo UNIQUE (foo)',
            $this->platform->getCreateConstraintSQLQuery($index, 'foo')
        );
    }

    public function testCreateConstraintSQLQueryWithNonUniqueIndex()
    {
        $index = new Schema\Index('foo', array('foo'));

        $this->assertEquals(
            'CREATE INDEX foo ON foo (foo)',
            $this->platform->getCreateConstraintSQLQuery($index, 'foo')
        );
    }

    public function testCreatePrimaryKeySQLQuery()
    {
        $primaryKey = new Schema\PrimaryKey('foo', array('foo'));

        $this->assertEquals(
            'ALTER TABLE foo ADD CONSTRAINT foo PRIMARY KEY (foo)',
            $this->platform->getCreatePrimaryKeySQLQuery($primaryKey, 'foo')
        );
    }

    public function testCreateForeignKeySQLQuery()
    {
        $foreignKey = new Schema\ForeignKey('foo', array('foo'), 'bar', array('bar'));

        $this->assertEquals(
            'ALTER TABLE foo ADD CONSTRAINT foo FOREIGN KEY (foo) REFERENCES bar (bar)',
            $this->platform->getCreateForeignKeySQLQuery($foreignKey, 'foo')
        );
    }

    public function testCreateIndexSQLQueryWithUniqueIndex()
    {
        $index = new Schema\Index('foo', array('foo'), true);

        $this->assertEquals(
            'ALTER TABLE foo ADD CONSTRAINT foo UNIQUE (foo)',
            $this->platform->getCreateIndexSQLQuery($index, 'foo')
        );
    }

    public function testCreateIndexSQLQueriesWithNonUniqueIndex()
    {
        $index = new Schema\Index('foo', array('foo'));

        $this->assertEquals(
            'CREATE INDEX foo ON foo (foo)',
            $this->platform->getCreateIndexSQLQuery($index, 'foo')
        );
    }

    public function testCreateColumnCommentSQLQuery()
    {
        $column = new Schema\Column('foo', Type::getType(Type::STRING), array('comment' => 'bar'));

        $this->assertEquals(
            'COMMENT ON COLUMN foo.foo IS \'bar\'',
            $this->platform->getCreateColumnCommentSQLQuery($column, 'foo')
        );
    }

    public function testCreateColumnCommentsSQLQueriesWithColumnComments()
    {
        $columns = array(
            new Schema\Column('foo', Type::getType(Type::STRING), array('comment' => 'foo')),
            new Schema\Column('bar', Type::getType(Type::STRING), array('comment' => 'bar')),
        );

        $this->assertEquals(
            array('COMMENT ON COLUMN foo.foo IS \'foo\'', 'COMMENT ON COLUMN foo.bar IS \'bar\''),
            $this->platform->getCreateColumnCommentsSQLQueries($columns, 'foo')
        );
    }

    public function testCreateColumnCommentsSQLQueriesWithoutTableColumnsComments()
    {
        $columns = array(
            new Schema\Column('foo', Type::getType(Type::STRING)),
            new Schema\Column('bar', Type::getType(Type::STRING)),
        );

        $this->assertEmpty($this->platform->getCreateColumnCommentsSQLQueries($columns, 'foo'));
    }

    public function testDropDatabaseSQLQuery()
    {
        $this->assertEquals('DROP DATABASE foo', $this->platform->getDropDatabaseSQLQuery('foo'));
    }

    public function testDropSequenceSQLQuery()
    {
        $sequence = new Schema\Sequence('foo');

        $this->assertEquals('DROP SEQUENCE foo', $this->platform->getDropSequenceSQLQuery($sequence));
    }

    public function testDropViewSQLQuery()
    {
        $view = new Schema\View('foo');

        $this->assertEquals('DROP VIEW foo', $this->platform->getDropViewSQLQuery($view));
    }

    public function testDropTableSQLQuery()
    {
        $table = new Schema\Table('foo');

        $this->assertEquals('DROP TABLE foo', $this->platform->getDropTableSQLQuery($table));
    }

    public function testDropConstraintSQLQueryWithPrimaryKey()
    {
        $constraint = new Schema\PrimaryKey('foo');

        $this->assertEquals(
            'ALTER TABLE bar DROP CONSTRAINT foo',
            $this->platform->getDropConstraintSQLQuery($constraint, 'bar')
        );
    }

    public function testDropConstraintSQLQueryWithForeignKey()
    {
        $constraint = new Schema\ForeignKey('foo', array(), 'bar', array());

        $this->assertEquals(
            'ALTER TABLE bar DROP CONSTRAINT foo',
            $this->platform->getDropConstraintSQLQuery($constraint, 'bar')
        );
    }

    public function testDropConstraintSQLQueryWithIndex()
    {
        $constraint = new Schema\Index('foo');

        $this->assertEquals(
            'DROP INDEX foo',
            $this->platform->getDropConstraintSQLQuery($constraint, 'bar')
        );
    }

    public function testDropPrimaryKeySQLQuery()
    {
        $primaryKey = new Schema\PrimaryKey('foo');

        $this->assertEquals(
            'ALTER TABLE bar DROP CONSTRAINT foo',
            $this->platform->getDropPrimaryKeySQLQuery($primaryKey, 'bar')
        );
    }

    public function testDropForeignKeySQLQuery()
    {
        $foreignKey = new Schema\ForeignKey('foo', array(), 'bar', array());

        $this->assertEquals(
            'ALTER TABLE bar DROP CONSTRAINT foo',
            $this->platform->getDropForeignKeySQLQuery($foreignKey, 'bar')
        );
    }

    public function testDropIndexSQLQueryWithUniqueIndex()
    {
        $index = new Schema\Index('foo', array(), true);

        $this->assertEquals(
            'ALTER TABLE bar DROP CONSTRAINT foo',
            $this->platform->getDropIndexSQLQuery($index, 'bar')
        );
    }

    public function testDropIndexSQLQueryWithNonUniqueIndex()
    {
        $index = new Schema\Index('foo');

        $this->assertEquals(
            'DROP INDEX foo',
            $this->platform->getDropIndexSQLQuery($index, 'bar')
        );
    }

    /**
     * @param string $isolation A valid transaction isolation.
     *
     * @dataProvider transactionIsolationProvider
     */
    public function testTransactionIsolationSQLDeclarationWithValidTransactionIsolation($isolation)
    {
        $method = new ReflectionMethod($this->platform, 'getTransactionIsolationSQLDeclaration');
        $method->setAccessible(true);

        $this->assertEquals($isolation, $method->invoke($this->platform, $isolation));
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     * @expectedExceptionMessage The transaction isolation "foo" does not exist.
     */
    public function testTransactionIsolationSQLDeclarationWithInvalidTransactionIsolation()
    {
        $method = new ReflectionMethod($this->platform, 'getTransactionIsolationSQLDeclaration');
        $method->setAccessible(true);

        $method->invoke($this->platform, 'foo');
    }

    public function testColumnSQLDeclarationWithAllOptions()
    {
        $column = new Schema\Column(
            'foo',
            Type::getType(Type::STRING),
            array(
                'length'   => 255,
                'not_null' => true,
                'default'  => 'foo',
                'comment'  => 'bar',
            )
        );

        $method = new ReflectionMethod($this->platform, 'getColumnSQLDeclaration');
        $method->setAccessible(true);

        $this->assertEquals(
            'foo VARCHAR(255) NOT NULL DEFAULT \'foo\' COMMENT \'bar\'',
            $method->invoke($this->platform, $column)
        );
    }

    public function testColumnSQLDeclarationWithTypedDefaultValue()
    {
        $column = new Schema\Column(
            'foo',
            Type::getType(Type::DATETIME),
            array('default' => new DateTime('2012-01-01 12:12:12'))
        );

        $method = new ReflectionMethod($this->platform, 'getColumnSQLDeclaration');
        $method->setAccessible(true);

        $this->assertEquals('\'2012-01-01 12:12:12\'', substr($method->invoke($this->platform, $column), -21));
    }

    public function testColumnSQLDeclarationWithMandatoryType()
    {
        $column = new Schema\Column('foo', Type::getType(Type::TARRAY));

        $method = new ReflectionMethod($this->platform, 'getColumnSQLDeclaration');
        $method->setAccessible(true);

        $this->assertEquals('\'(FridgeType::ARRAY)\'', substr($method->invoke($this->platform, $column), -21));
    }

    public function testColumnsSQLDeclaration()
    {
        $columns = array(
            new Schema\Column('foo', Type::getType(Type::INTEGER)),
            new Schema\Column('bar', Type::getType(Type::INTEGER)),
        );

        $method = new ReflectionMethod($this->platform, 'getColumnsSQLDeclaration');
        $method->setAccessible(true);

        $this->assertEquals('foo INT, bar INT', $method->invoke($this->platform, $columns));
    }

    public function testCreateTableSQLQueries()
    {
        $table = new Schema\Table(
            'foo',
            array(
                new Schema\Column('foo', Type::getType(Type::INTEGER)),
                new Schema\Column('bar', Type::getType(Type::INTEGER)),
                new Schema\Column('foo_bar', Type::getType(Type::INTEGER)),
                new Schema\Column('bar_foo', Type::getType(Type::INTEGER)),
            ),
            new Schema\PrimaryKey('pk1', array('foo')),
            array(new Schema\ForeignKey('fk1', array('bar'), 'bar', array('bar'))),
            array(
                new Schema\Index('idx1', array('foo_bar')),
                new Schema\Index('uniq1', array('bar_foo'), true),
            )
        );

        $this->assertEquals(
            array(
                'CREATE TABLE foo ('.
                'foo INT NOT NULL,'.
                ' bar INT,'.
                ' foo_bar INT,'.
                ' bar_foo INT,'.
                ' CONSTRAINT pk1 PRIMARY KEY (foo),'.
                ' INDEX idx1 (foo_bar),'.
                ' CONSTRAINT uniq1 UNIQUE (bar_foo),'.
                ' CONSTRAINT fk1 FOREIGN KEY (bar) REFERENCES bar (bar)'.
                ')',
            ),
            $this->platform->getCreateTableSQLQueries($table)
        );
    }

    public function testCreateTableSQLQueriesWithIndexOnly()
    {
        $table = new Schema\Table(
            'foo',
            array(new Schema\Column('foo', Type::getType(Type::INTEGER))),
            null,
            array(),
            array(new Schema\Index('idx1', array('foo')))
        );

        $this->assertEquals(
            array('CREATE TABLE foo (foo INT, INDEX idx1 (foo))'),
            $this->platform->getCreateTableSQLQueries($table)
        );
    }

    public function testCreateTableSQLQueriesWithPrimaryKeyDisabled()
    {
        $table = new Schema\Table(
            'foo',
            array(new Schema\Column('foo', Type::getType(Type::INTEGER))),
            new Schema\PrimaryKey('pk1', array('foo'))
        );

        $this->assertEquals(
            array('CREATE TABLE foo (foo INT NOT NULL)'),
            $this->platform->getCreateTableSQLQueries($table, array('primary_key' => false))
        );
    }

    public function testCreateTableSQLQueriesWithForeignKeyDisabled()
    {
        $table = new Schema\Table(
            'foo',
            array(new Schema\Column('foo', Type::getType(Type::INTEGER))),
            null,
            array(new Schema\ForeignKey('fk1', array('foo'), 'bar', array('bar')))
        );

        $this->assertEquals(
            array('CREATE TABLE foo (foo INT)'),
            $this->platform->getCreateTableSQLQueries($table, array('foreign_key' => false))
        );
    }

    public function testCreateTableSQLQueriesWithIndexDisabled()
    {
        $table = new Schema\Table(
            'foo',
            array(new Schema\Column('foo', Type::getType(Type::INTEGER))),
            null,
            array(),
            array(new Schema\Index('idx1', array('foo')))
        );

        $this->assertEquals(
            array('CREATE TABLE foo (foo INT)'),
            $this->platform->getCreateTableSQLQueries($table, array('index' => false))
        );
    }

    public function testQuoteIdentifier()
    {
        $this->assertEquals('"foo"', $this->platform->quoteIdentifier('foo'));
    }

    public function testQuoteIdentifiers()
    {
        $this->assertEquals(array('"foo"', '"bar"'), $this->platform->quoteIdentifiers(array('foo', 'bar')));
    }
}
