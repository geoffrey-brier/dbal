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
    Fridge\DBAL\Schema\Check,
    Fridge\DBAL\Schema\Column,
    Fridge\DBAL\Schema\Diff\ColumnDiff,
    Fridge\DBAL\Schema\Diff\SchemaDiff,
    Fridge\DBAL\Schema\Diff\TableDiff,
    Fridge\DBAL\Schema\ForeignKey,
    Fridge\DBAL\Schema\Index,
    Fridge\DBAL\Schema\PrimaryKey,
    Fridge\DBAL\Schema\Schema,
    Fridge\DBAL\Schema\Sequence,
    Fridge\DBAL\Schema\Table,
    Fridge\DBAL\Schema\View,
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

        $property->setValue($this->platform, array('foo' => Type::INTEGER));
    }

    /**
     * Initializes the platform mandatory types.
     */
    protected function initializeMandatoryTypes()
    {
        $property = new \ReflectionProperty('Fridge\DBAL\Platform\AbstractPlatform', 'mandatoryTypes');
        $property->setAccessible(true);

        $property->setValue($this->platform, array(Type::INTEGER));
    }

    public function testInitialState()
    {
        $this->assertTrue($this-> platform->useStrictMappedType());
        $this->assertSame(Type::TEXT, $this->platform->getFallbackMappedType());
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

        $this->assertSame(Type::INTEGER, $this->platform->getMappedType('foo'));
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
        $this->assertSame($this->platform->getFallbackMappedType(), $this->platform->getMappedType('bar'));
    }

    public function testAddMappedTypeWithValidValue()
    {
        $this->initializeMappedTypes();

        $this->platform->addMappedType('bar', Type::INTEGER);

        $this->assertTrue($this->platform->hasMappedType('bar'));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     * @expectedExceptionMessage The mapped type "foo" already exists.
     */
    public function testAddMappedTypeWithInvalidValue()
    {
        $this->initializeMappedTypes();

        $this->platform->addMappedType('foo', Type::INTEGER);
    }

    /**
     * @expectedException Fridge\DBAL\Exception\TypeException
     */
    public function testAddMappedTypeWithInvalidType()
    {
        $this->platform->addMappedType('foo', 'bar');
    }

    public function testOverrideMappedTypeWithValidValue()
    {
        $this->initializeMappedTypes();

        $this->platform->overrideMappedType('foo', Type::SMALLINTEGER);

        $this->assertSame(Type::SMALLINTEGER, $this->platform->getMappedType('foo'));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testOverrideMappedTypeWithInvalidValue()
    {
        $this->initializeMappedTypes();

        $this->platform->overrideMappedType('bar', Type::INTEGER);
    }

    /**
     * @expectedException Fridge\DBAL\Exception\TypeException
     */
    public function testOverrideMappedTypeWithInvalidType()
    {
        $this->initializeMappedTypes();

        $this->platform->overrideMappedType('foo', 'bar');
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
        $this->assertSame(Type::INTEGER, $this->platform->getFallbackMappedType());
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

        $this->assertTrue($this->platform->hasMandatoryType(Type::INTEGER));
        $this->assertFalse($this->platform->hasMandatoryType('foo'));
    }

    public function testAddMandatoryTypeWithValidValue()
    {
        $this->initializeMandatoryTypes();

        $this->platform->addMandatoryType(Type::SMALLINTEGER);

        $this->assertTrue($this->platform->hasMandatoryType(Type::SMALLINTEGER));
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     * @expectedExceptionMessage The mandatory type "integer" already exists.
     */
    public function testAddMandatoryTypeWithInvalidValue()
    {
        $this->initializeMandatoryTypes();

        $this->platform->addMandatoryType(Type::INTEGER);
    }

    /**
     * @expectedException Fridge\DBAL\Exception\TypeException
     */
    public function testAddMandatoryTypeWithInvalidType()
    {
        $this->platform->addMandatoryType('foo');
    }

    public function testRemoveMandatoryTypeWithValidValue()
    {
        $this->initializeMandatoryTypes();

        $this->platform->removeMandatoryType(Type::INTEGER);

        $this->assertFalse($this->platform->hasMandatoryType(Type::INTEGER));
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     * @expectedExceptionMessage The mandatory type "foo" does not exist.
     */
    public function testRemoveMandatoryTypeWithInvalidValue()
    {
        $this->initializeMandatoryTypes();

        $this->platform->removeMandatoryType('foo');
    }

    public function testBigIntegerSQLDeclaration()
    {
        $this->assertSame('BIGINT', $this->platform->getBigIntegerSQLDeclaration());
    }

    public function testBlobSQLDeclaration()
    {
        $this->assertSame('BLOB', $this->platform->getBlobSQLDeclaration());
    }

    public function testBooleanSQLDeclaration()
    {
        $this->assertSame('BOOLEAN', $this->platform->getBooleanSQLDeclaration());
    }

    public function testClobSQLDeclaration()
    {
        $this->assertSame('TEXT', $this->platform->getClobSQLDeclaration());
    }

    public function testDateSQLDeclaration()
    {
        $this->assertSame('DATE', $this->platform->getDateSQLDeclaration());
    }

    public function testDateTimeSQLDeclaration()
    {
        $this->assertSame('DATETIME', $this->platform->getDateTimeSQLDeclaration());
    }

    public function testDecimalSQLDeclarationWithoutOptions()
    {
        $this->assertSame('NUMERIC(5, 2)', $this->platform->getDecimalSQLDeclaration());
    }

    public function testDecimalSQLDeclarationWithPrecision()
    {
        $this->assertSame('NUMERIC(3, 2)', $this->platform->getDecimalSQLDeclaration(array('precision' => 3)));
    }

    public function testDecimalSQLDeclarationWithScale()
    {
        $this->assertSame('NUMERIC(5, 1)', $this->platform->getDecimalSQLDeclaration(array('scale' => 1)));
    }

    public function testFloatSQLDeclaration()
    {
        $this->assertSame('DOUBLE PRECISION', $this->platform->getFloatSQLDeclaration());
    }

    public function testIntegerSQLDeclaration()
    {
        $this->assertSame('INT', $this->platform->getIntegerSQLDeclaration());
    }

    public function testSmallIntegerSQLDeclaration()
    {
        $this->assertSame('SMALLINT', $this->platform->getSmallIntegerSQLDeclaration());
    }

    public function testTimeSQLDeclaration()
    {
        $this->assertSame('TIME', $this->platform->getTimeSQLDeclaration());
    }

    public function testVarcharSQLDeclarationWithoutOptions()
    {
        $this->assertSame('VARCHAR(255)', $this->platform->getVarcharSQLDeclaration(array()));
    }

    public function testVarcharSQLDeclarationWithFixedFlag()
    {
        $this->assertSame(
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
        $this->assertSame('TEXT', $this->platform->getVarcharSQLDeclaration(array('length' => 65536)));
    }

    public function testDefaultTransactionIsolation()
    {
        $this->assertSame(Connection::TRANSACTION_READ_COMMITTED, $this->platform->getDefaultTransactionIsolation());
    }

    public function testDateFormat()
    {
        $this->assertSame('Y-m-d', $this->platform->getDateFormat());
    }

    public function testTimeFormat()
    {
        $this->assertSame('H:i:s', $this->platform->getTimeFormat());
    }

    public function testDateTimeFormat()
    {
        $this->assertSame('Y-m-d H:i:s', $this->platform->getDateTimeFormat());
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
        $this->assertSame('SAVEPOINT foo', $this->platform->getCreateSavepointSQLQuery('foo'));
    }

    public function testReleaseSavepointSQLQuery()
    {
        $this->assertSame('RELEASE SAVEPOINT foo', $this->platform->getReleaseSavepointSQLQuery('foo'));
    }

    public function testRollbackSavepointSQLQuery()
    {
        $this->assertSame('ROLLBACK TO SAVEPOINT foo', $this->platform->getRollbackSavepointSQLQuery('foo'));
    }

    public function testSetCharsetSQLQuery()
    {
        $this->assertSame('SET NAMES \'foo\'', $this->platform->getSetCharsetSQLQuery('foo'));
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testSelectDatabaseSQLQuery()
    {
        $this->platform->getSelectDatabaseSQLQuery();
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testSelectDatabasesSQLQuery()
    {
        $this->platform->getSelectDatabasesSQLQuery();
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testSelectSequencesSQLQuery()
    {
        $this->platform->getSelectSequencesSQLQuery('foo');
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testSelectViewsSQLQuery()
    {
        $this->platform->getSelectViewsSQLQuery('foo');
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testSelectTableNamesSQLQuery()
    {
        $this->platform->getSelectTableNamesSQLQuery('foo');
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testSelectTableColumnsSQLQuery()
    {
        $this->platform->getSelectTableColumnsSQLQuery('foo', 'bar');
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testSelectTablePrimaryKeySQLQuery()
    {
        $this->platform->getSelectTablePrimaryKeySQLQuery('foo', 'bar');
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testSelectTableForeignKeysSQLQuery()
    {
        $this->platform->getSelectTableForeignKeysSQLQuery('foo', 'bar');
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testSelectTableIndexesSQLQuery()
    {
        $this->platform->getSelectTableIndexesSQLQuery('foo', 'bar');
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testSelectTableChecksSQLQuery()
    {
        $this->platform->getSelectTableCheckSQLQuery('foo', 'bar');
    }

    public function testCreateDatabaseSQLQueries()
    {
        $this->assertSame(array('CREATE DATABASE foo'), $this->platform->getCreateDatabaseSQLQueries('foo'));
    }

    public function testCreateSequenceSQLQueries()
    {
        $sequence = new Sequence('foo', 1, 1);

        $this->assertSame(
            array('CREATE SEQUENCE foo INCREMENT BY 1 MINVALUE 1 START WITH 1'),
            $this->platform->getCreateSequenceSQLQueries($sequence)
        );
    }

    public function testCreateViewSQLQueries()
    {
        $view = new View('foo', 'bar');

        $this->assertSame(array('CREATE VIEW foo AS bar'), $this->platform->getCreateViewSQLQueries($view));
    }

    public function testCreateColumnSQLQueries()
    {
        $column = new Column('foo', Type::getType(Type::INTEGER), array('comment' => 'foo'));

        $this->assertSame(
            array('ALTER TABLE foo ADD COLUMN foo INT COMMENT \'foo\''),
            $this->platform->getCreateColumnSQLQueries($column, 'foo')
        );
    }

    public function testCreateConstraintSQLQueriesWithPrimaryKey()
    {
        $primaryKey = new PrimaryKey('foo', array('bar'));

        $this->assertSame(
            array('ALTER TABLE foo ADD CONSTRAINT foo PRIMARY KEY (bar)'),
            $this->platform->getCreateConstraintSQLQueries($primaryKey, 'foo')
        );
    }

    public function testCreateConstraintSQLQueriesWithForeignKey()
    {
        $foreignKey = new ForeignKey(
            'foo',
            array('foo'),
            'bar',
            array('bar'),
            ForeignKey::SET_NULL,
            ForeignKey::CASCADE
        );

        $this->assertSame(
            array(
                'ALTER TABLE foo'.
                ' ADD CONSTRAINT foo'.
                ' FOREIGN KEY (foo)'.
                ' REFERENCES bar (bar)'.
                ' ON DELETE SET NULL'.
                ' ON UPDATE CASCADE'
            ),
            $this->platform->getCreateConstraintSQLQueries($foreignKey, 'foo')
        );
    }

    public function testCreateConstraintSQLQueriesWithUniqueIndex()
    {
        $index = new Index('foo', array('foo'), true);

        $this->assertSame(
            array('ALTER TABLE foo ADD CONSTRAINT foo UNIQUE (foo)'),
            $this->platform->getCreateConstraintSQLQueries($index, 'foo')
        );
    }

    public function testCreateConstraintSQLQueriesWithNonUniqueIndex()
    {
        $index = new Index('foo', array('foo'));

        $this->assertSame(
            array('CREATE INDEX foo ON foo (foo)'),
            $this->platform->getCreateConstraintSQLQueries($index, 'foo')
        );
    }

    public function testCreateConstraintSQLQueriesWithCheck()
    {
        $check = new Check('foo', 'bar');

        $this->assertSame(
            array('ALTER TABLE foo ADD CONSTRAINT foo CHECK (bar)'),
            $this->platform->getCreateConstraintSQLQueries($check, 'foo')
        );
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     * @expectedExceptionMessage The constraint "FridgeConstaint" is not supported.
     */
    public function testCreateConstraintSQLQueriesWithInvalidConstraint()
    {
        $constraintMock = $this->getMockBuilder('Fridge\DBAL\Schema\ConstraintInterface')
            ->setMockClassName('FridgeConstaint')
            ->disableOriginalConstructor()
            ->getMock();

        $this->platform->getCreateConstraintSQLQueries($constraintMock, 'foo');
    }

    public function testCreatePrimaryKeySQLQueries()
    {
        $primaryKey = new PrimaryKey('foo', array('foo'));

        $this->assertSame(
            array('ALTER TABLE foo ADD CONSTRAINT foo PRIMARY KEY (foo)'),
            $this->platform->getCreatePrimaryKeySQLQueries($primaryKey, 'foo')
        );
    }

    public function testCreateForeignKeySQLQueries()
    {
        $foreignKey = new ForeignKey(
            'foo',
            array('foo'),
            'bar',
            array('bar'),
            ForeignKey::SET_NULL,
            ForeignKey::CASCADE
        );

        $this->assertSame(
            array(
                'ALTER TABLE foo'.
                ' ADD CONSTRAINT foo'.
                ' FOREIGN KEY (foo)'.
                ' REFERENCES bar (bar)'.
                ' ON DELETE SET NULL'.
                ' ON UPDATE CASCADE'
            ),
            $this->platform->getCreateForeignKeySQLQueries($foreignKey, 'foo')
        );
    }

    public function testCreateIndexSQLQueriesWithUniqueIndex()
    {
        $index = new Index('foo', array('foo'), true);

        $this->assertSame(
            array('ALTER TABLE foo ADD CONSTRAINT foo UNIQUE (foo)'),
            $this->platform->getCreateIndexSQLQueries($index, 'foo')
        );
    }

    public function testCreateIndexSQLQueriesWithNonUniqueIndex()
    {
        $index = new Index('foo', array('foo'));

        $this->assertSame(
            array('CREATE INDEX foo ON foo (foo)'),
            $this->platform->getCreateIndexSQLQueries($index, 'foo')
        );
    }

    public function testCreateCheckSQLQueries()
    {
        $check = new Check('foo', 'bar');

        $this->assertSame(
            array('ALTER TABLE zaz ADD CONSTRAINT foo CHECK (bar)'),
            $this->platform->getCreateCheckSQLQueries($check, 'zaz')
        );
    }

    public function testRenameDatabaseSQLQueries()
    {
        $oldSchema = new Schema('foo');
        $newSchema = new Schema('bar');

        $schemaDiff = new SchemaDiff($oldSchema, $newSchema);

        $this->assertSame(
            array('ALTER DATABASE foo RENAME TO bar'),
            $this->platform->getRenameDatabaseSQLQueries($schemaDiff)
        );
    }

    public function testRenameTableSQLQueries()
    {
        $oldTable = new Table('foo');
        $newTable = new Table('bar');

        $tableDiff = new TableDiff($oldTable, $newTable);

        $this->assertSame(
            array('ALTER TABLE foo RENAME TO bar'),
            $this->platform->getRenameTableSQLQueries($tableDiff)
        );
    }

    public function testAlterColumnSQLQueries()
    {
        $columnDiff = new ColumnDiff(
            new Column('foo', Type::getType(Type::INTEGER)),
            new Column('bar', Type::getType(Type::INTEGER), array('comment' => 'foo')),
            array()
        );

        $this->assertSame(
            array('ALTER TABLE foo ALTER COLUMN foo bar INT COMMENT \'foo\''),
            $this->platform->getAlterColumnSQLQueries($columnDiff, 'foo')
        );
    }

    public function testDropDatabaseSQLQueries()
    {
        $this->assertSame(array('DROP DATABASE foo'), $this->platform->getDropDatabaseSQLQueries('foo'));
    }

    public function testDropSequenceSQLQueries()
    {
        $sequence = new Sequence('foo');

        $this->assertSame(array('DROP SEQUENCE foo'), $this->platform->getDropSequenceSQLQueries($sequence));
    }

    public function testDropViewSQLQueries()
    {
        $view = new View('foo');

        $this->assertSame(array('DROP VIEW foo'), $this->platform->getDropViewSQLQueries($view));
    }

    public function testDropTableSQLQueries()
    {
        $table = new Table('foo');

        $this->assertSame(array('DROP TABLE foo'), $this->platform->getDropTableSQLQueries($table));
    }

    public function testDropColumnSQLQueries()
    {
        $column = new Column('foo', Type::getType(Type::INTEGER));

        $this->assertSame(
            array('ALTER TABLE foo DROP COLUMN foo'),
            $this->platform->getDropColumnSQLQueries($column, 'foo')
        );
    }

    public function testDropConstraintSQLQueriesWithPrimaryKey()
    {
        $constraint = new PrimaryKey('foo');

        $this->assertSame(
            array('ALTER TABLE bar DROP CONSTRAINT foo'),
            $this->platform->getDropConstraintSQLQueries($constraint, 'bar')
        );
    }

    public function testDropConstraintSQLQueriesWithForeignKey()
    {
        $constraint = new ForeignKey('foo', array(), 'bar', array());

        $this->assertSame(
            array('ALTER TABLE bar DROP CONSTRAINT foo'),
            $this->platform->getDropConstraintSQLQueries($constraint, 'bar')
        );
    }

    public function testDropConstraintSQLQueriesWithIndex()
    {
        $constraint = new Index('foo');

        $this->assertSame(
            array('DROP INDEX foo'),
            $this->platform->getDropConstraintSQLQueries($constraint, 'bar')
        );
    }

    public function testDropConstraintSQLQueriesWithCheck()
    {
        $check = new Check('foo', 'bar');

        $this->assertSame(
            array('ALTER TABLE foo DROP CONSTRAINT foo'),
            $this->platform->getDropConstraintSQLQueries($check, 'foo')
        );
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     */
    public function testDropConstraintSQLQueriesWithInvalidConstraint()
    {
        $constraintMock = $this->getMockBuilder('Fridge\DBAL\Schema\ConstraintInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->platform->getDropConstraintSQLQueries($constraintMock, 'foo');
    }

    public function testDropPrimaryKeySQLQueries()
    {
        $primaryKey = new PrimaryKey('foo');

        $this->assertSame(
            array('ALTER TABLE bar DROP CONSTRAINT foo'),
            $this->platform->getDropPrimaryKeySQLQueries($primaryKey, 'bar')
        );
    }

    public function testDropForeignKeySQLQueries()
    {
        $foreignKey = new ForeignKey('foo', array(), 'bar', array());

        $this->assertSame(
            array('ALTER TABLE bar DROP CONSTRAINT foo'),
            $this->platform->getDropForeignKeySQLQueries($foreignKey, 'bar')
        );
    }

    public function testDropIndexSQLQueriesWithUniqueIndex()
    {
        $index = new Index('foo', array(), true);

        $this->assertSame(
            array('ALTER TABLE bar DROP CONSTRAINT foo'),
            $this->platform->getDropIndexSQLQueries($index, 'bar')
        );
    }

    public function testDropIndexSQLQueriesWithNonUniqueIndex()
    {
        $index = new Index('foo');

        $this->assertSame(
            array('DROP INDEX foo'),
            $this->platform->getDropIndexSQLQueries($index, 'bar')
        );
    }

    public function testDropCheckSQLQueries()
    {
        $check = new Check('foo', 'bar');

        $this->assertSame(
            array('ALTER TABLE zaz DROP CONSTRAINT foo'),
            $this->platform->getDropCheckSQLQueries($check, 'zaz')
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

        $this->assertSame($isolation, $method->invoke($this->platform, $isolation));
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
        $column = new Column(
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

        $this->assertSame(
            'foo VARCHAR(255) NOT NULL DEFAULT \'foo\' COMMENT \'bar\'',
            $method->invoke($this->platform, $column)
        );
    }

    public function testColumnSQLDeclarationWithTypedDefaultValue()
    {
        $column = new Column(
            'foo',
            Type::getType(Type::DATETIME),
            array('default' => new DateTime('2012-01-01 12:12:12'))
        );

        $method = new ReflectionMethod($this->platform, 'getColumnSQLDeclaration');
        $method->setAccessible(true);

        $this->assertSame('\'2012-01-01 12:12:12\'', substr($method->invoke($this->platform, $column), -21));
    }

    public function testColumnSQLDeclarationWithMandatoryType()
    {
        $column = new Column('foo', Type::getType(Type::TARRAY));

        $method = new ReflectionMethod($this->platform, 'getColumnSQLDeclaration');
        $method->setAccessible(true);

        $this->assertSame('\'(FridgeType::ARRAY)\'', substr($method->invoke($this->platform, $column), -21));
    }

    public function testColumnsSQLDeclaration()
    {
        $columns = array(
            new Column('foo', Type::getType(Type::INTEGER)),
            new Column('bar', Type::getType(Type::INTEGER)),
        );

        $method = new ReflectionMethod($this->platform, 'getColumnsSQLDeclaration');
        $method->setAccessible(true);

        $this->assertSame('foo INT, bar INT', $method->invoke($this->platform, $columns));
    }

    public function testCreateTableSQLQueries()
    {
        $table = new Table(
            'foo',
            array(
                new Column('foo', Type::getType(Type::INTEGER)),
                new Column('bar', Type::getType(Type::INTEGER)),
                new Column('foo_bar', Type::getType(Type::INTEGER)),
                new Column('bar_foo', Type::getType(Type::INTEGER)),
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
                new Index('uniq1', array('bar_foo'), true),
            ),
            array(
                new Check('ck1', 'foo > 0'),
            )
        );

        $this->assertSame(
            array(
                'CREATE TABLE foo ('.
                'foo INT NOT NULL,'.
                ' bar INT,'.
                ' foo_bar INT,'.
                ' bar_foo INT,'.
                ' CONSTRAINT pk1 PRIMARY KEY (foo),'.
                ' INDEX idx1 (foo_bar),'.
                ' CONSTRAINT uniq1 UNIQUE (bar_foo),'.
                ' CONSTRAINT fk1 FOREIGN KEY (bar) REFERENCES bar (bar) ON DELETE CASCADE ON UPDATE CASCADE,'.
                ' CONSTRAINT ck1 CHECK (foo > 0)'.
                ')',
            ),
            $this->platform->getCreateTableSQLQueries($table)
        );
    }

    public function testCreateTableSQLQueriesWithIndexOnly()
    {
        $table = new Table(
            'foo',
            array(new Column('foo', Type::getType(Type::INTEGER))),
            null,
            array(),
            array(new Index('idx1', array('foo')))
        );

        $this->assertSame(
            array('CREATE TABLE foo (foo INT, INDEX idx1 (foo))'),
            $this->platform->getCreateTableSQLQueries($table)
        );
    }

    public function testCreateTableSQLQueriesWithPrimaryKeyDisabled()
    {
        $table = new Table(
            'foo',
            array(new Column('foo', Type::getType(Type::INTEGER))),
            new PrimaryKey('pk1', array('foo'))
        );

        $this->assertSame(
            array('CREATE TABLE foo (foo INT NOT NULL)'),
            $this->platform->getCreateTableSQLQueries($table, array('primary_key' => false))
        );
    }

    public function testCreateTableSQLQueriesWithForeignKeyDisabled()
    {
        $table = new Table(
            'foo',
            array(new Column('foo', Type::getType(Type::INTEGER))),
            null,
            array(new ForeignKey('fk1', array('foo'), 'bar', array('bar')))
        );

        $this->assertSame(
            array('CREATE TABLE foo (foo INT)'),
            $this->platform->getCreateTableSQLQueries($table, array('foreign_key' => false))
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

    public function testCreateTableSQLQueriesWithCheckDisabled()
    {
        $table = new Table(
            'foo',
            array(new Column('foo', Type::getType(Type::INTEGER))),
            null,
            array(),
            array(),
            array(new Check('ck1', array('foo > 0')))
        );

        $this->assertSame(
            array('CREATE TABLE foo (foo INT)'),
            $this->platform->getCreateTableSQLQueries($table, array('check' => false))
        );
    }

    public function testQuoteIdentifier()
    {
        $this->assertSame('"foo"', $this->platform->quoteIdentifier('foo'));
    }

    public function testQuoteIdentifiers()
    {
        $this->assertSame(array('"foo"', '"bar"'), $this->platform->quoteIdentifiers(array('foo', 'bar')));
    }
}
