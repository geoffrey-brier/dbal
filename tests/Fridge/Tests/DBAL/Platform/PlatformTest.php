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

        $this->assertSame('bar', $this->platform->getMappedType('foo'));
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

        $this->assertSame('foo', $this->platform->getMappedType('foo'));
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
        $this->assertSame('BIGINT', $this->platform->getBigIntegerSQLDeclaration());
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

    public function testCreateDatabaseSQLQueries()
    {
        $this->assertSame(array('CREATE DATABASE foo'), $this->platform->getCreateDatabaseSQLQueries('foo'));
    }

    public function testCreateSequenceSQLQueries()
    {
        $sequence = new Schema\Sequence('foo', 1, 1);

        $this->assertSame(
            array('CREATE SEQUENCE foo INCREMENT BY 1 MINVALUE 1 START WITH 1'),
            $this->platform->getCreateSequenceSQLQueries($sequence)
        );
    }

    public function testCreateViewSQLQueries()
    {
        $view = new Schema\View('foo', 'bar');

        $this->assertSame(array('CREATE VIEW foo AS bar'), $this->platform->getCreateViewSQLQueries($view));
    }

    public function testCreateColumnSQLQueries()
    {
        $column = new Schema\Column('foo', Type::getType(Type::INTEGER), array('comment' => 'foo'));

        $this->assertSame(
            array('ALTER TABLE foo ADD COLUMN foo INT COMMENT \'foo\''),
            $this->platform->getCreateColumnSQLQueries($column, 'foo')
        );
    }

    public function testCreateConstraintSQLQueriesWithPrimaryKey()
    {
        $primaryKey = new Schema\PrimaryKey('foo', array('bar'));

        $this->assertSame(
            array('ALTER TABLE foo ADD CONSTRAINT foo PRIMARY KEY (bar)'),
            $this->platform->getCreateConstraintSQLQueries($primaryKey, 'foo')
        );
    }

    public function testCreateConstraintSQLQueriesWithForeignKey()
    {
        $foreignKey = new Schema\ForeignKey(
            'foo',
            array('foo'),
            'bar',
            array('bar'),
            Schema\ForeignKey::SET_NULL,
            Schema\ForeignKey::CASCADE
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
        $index = new Schema\Index('foo', array('foo'), true);

        $this->assertSame(
            array('ALTER TABLE foo ADD CONSTRAINT foo UNIQUE (foo)'),
            $this->platform->getCreateConstraintSQLQueries($index, 'foo')
        );
    }

    public function testCreateConstraintSQLQueriesWithNonUniqueIndex()
    {
        $index = new Schema\Index('foo', array('foo'));

        $this->assertSame(
            array('CREATE INDEX foo ON foo (foo)'),
            $this->platform->getCreateConstraintSQLQueries($index, 'foo')
        );
    }

    public function testCreateConstraintSQLQueriesWithCheck()
    {
        $check = new Schema\Check('foo', 'bar');

        $this->assertSame(
            array('ALTER TABLE foo ADD CONSTRAINT foo CHECK (bar)'),
            $this->platform->getCreateConstraintSQLQueries($check, 'foo')
        );
    }

    /**
     * @expectedException Fridge\DBAL\Exception\PlatformException
     * @expectedExceptionMessage The constraint "foo" is not supported.
     */
    public function testCreateConstraintSQLQueriesWithInvalidConstraint()
    {
        $check = $this->getMock('Fridge\DBAL\Schema\ConstraintInterface', array(), array(), 'foo', false);

        $this->platform->getCreateConstraintSQLQueries($check, 'foo');
    }

    public function testCreatePrimaryKeySQLQueries()
    {
        $primaryKey = new Schema\PrimaryKey('foo', array('foo'));

        $this->assertSame(
            array('ALTER TABLE foo ADD CONSTRAINT foo PRIMARY KEY (foo)'),
            $this->platform->getCreatePrimaryKeySQLQueries($primaryKey, 'foo')
        );
    }

    public function testCreateForeignKeySQLQueries()
    {
        $foreignKey = new Schema\ForeignKey(
            'foo',
            array('foo'),
            'bar',
            array('bar'),
            Schema\ForeignKey::SET_NULL,
            Schema\ForeignKey::CASCADE
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
        $index = new Schema\Index('foo', array('foo'), true);

        $this->assertSame(
            array('ALTER TABLE foo ADD CONSTRAINT foo UNIQUE (foo)'),
            $this->platform->getCreateIndexSQLQueries($index, 'foo')
        );
    }

    public function testCreateIndexSQLQueriesWithNonUniqueIndex()
    {
        $index = new Schema\Index('foo', array('foo'));

        $this->assertSame(
            array('CREATE INDEX foo ON foo (foo)'),
            $this->platform->getCreateIndexSQLQueries($index, 'foo')
        );
    }

    public function testCreateCheckSQLQueries()
    {
        $check = new Schema\Check('foo', 'bar');

        $this->assertSame(
            array('ALTER TABLE zaz ADD CONSTRAINT foo CHECK (bar)'),
            $this->platform->getCreateCheckSQLQueries($check, 'zaz')
        );
    }

    public function testRenameDatabaseSQLQueries()
    {
        $oldSchema = new Schema\Schema('foo');
        $newSchema = new Schema\Schema('bar');

        $schemaDiff = new Schema\Diff\SchemaDiff($oldSchema, $newSchema);

        $this->assertSame(
            array('ALTER DATABASE foo RENAME TO bar'),
            $this->platform->getRenameDatabaseSQLQueries($schemaDiff)
        );
    }

    public function testRenameTableSQLQueries()
    {
        $oldTable = new Schema\Table('foo');
        $newTable = new Schema\Table('bar');

        $tableDiff = new Schema\Diff\TableDiff($oldTable, $newTable);

        $this->assertSame(
            array('ALTER TABLE foo RENAME TO bar'),
            $this->platform->getRenameTableSQLQueries($tableDiff)
        );
    }

    public function testAlterColumnSQLQueries()
    {
        $columnDiff = new Schema\Diff\ColumnDiff(
            new Schema\Column('foo', Type::getType(Type::INTEGER)),
            new Schema\Column('bar', Type::getType(Type::INTEGER), array('comment' => 'foo')),
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
        $sequence = new Schema\Sequence('foo');

        $this->assertSame(array('DROP SEQUENCE foo'), $this->platform->getDropSequenceSQLQueries($sequence));
    }

    public function testDropViewSQLQueries()
    {
        $view = new Schema\View('foo');

        $this->assertSame(array('DROP VIEW foo'), $this->platform->getDropViewSQLQueries($view));
    }

    public function testDropTableSQLQueries()
    {
        $table = new Schema\Table('foo');

        $this->assertSame(array('DROP TABLE foo'), $this->platform->getDropTableSQLQueries($table));
    }

    public function testDropColumnSQLQueries()
    {
        $column = new Schema\Column('foo', Type::getType(Type::INTEGER));

        $this->assertSame(
            array('ALTER TABLE foo DROP COLUMN foo'),
            $this->platform->getDropColumnSQLQueries($column, 'foo')
        );
    }

    public function testDropConstraintSQLQueriesWithPrimaryKey()
    {
        $constraint = new Schema\PrimaryKey('foo');

        $this->assertSame(
            array('ALTER TABLE bar DROP CONSTRAINT foo'),
            $this->platform->getDropConstraintSQLQueries($constraint, 'bar')
        );
    }

    public function testDropConstraintSQLQueriesWithForeignKey()
    {
        $constraint = new Schema\ForeignKey('foo', array(), 'bar', array());

        $this->assertSame(
            array('ALTER TABLE bar DROP CONSTRAINT foo'),
            $this->platform->getDropConstraintSQLQueries($constraint, 'bar')
        );
    }

    public function testDropConstraintSQLQueriesWithIndex()
    {
        $constraint = new Schema\Index('foo');

        $this->assertSame(
            array('DROP INDEX foo'),
            $this->platform->getDropConstraintSQLQueries($constraint, 'bar')
        );
    }

    public function testDropConstraintSQLQueriesWithCheck()
    {
        $check = new Schema\Check('foo', 'bar');

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
        $check = $this->getMock('Fridge\DBAL\Schema\ConstraintInterface', array(), array(), '', false);

        $this->platform->getDropConstraintSQLQueries($check, 'foo');
    }

    public function testDropPrimaryKeySQLQueries()
    {
        $primaryKey = new Schema\PrimaryKey('foo');

        $this->assertSame(
            array('ALTER TABLE bar DROP CONSTRAINT foo'),
            $this->platform->getDropPrimaryKeySQLQueries($primaryKey, 'bar')
        );
    }

    public function testDropForeignKeySQLQuery()
    {
        $foreignKey = new Schema\ForeignKey('foo', array(), 'bar', array());

        $this->assertSame(
            'ALTER TABLE bar DROP CONSTRAINT foo',
            $this->platform->getDropForeignKeySQLQuery($foreignKey, 'bar')
        );
    }

    public function testDropIndexSQLQueryWithUniqueIndex()
    {
        $index = new Schema\Index('foo', array(), true);

        $this->assertSame(
            'ALTER TABLE bar DROP CONSTRAINT foo',
            $this->platform->getDropIndexSQLQuery($index, 'bar')
        );
    }

    public function testDropIndexSQLQueryWithNonUniqueIndex()
    {
        $index = new Schema\Index('foo');

        $this->assertSame(
            'DROP INDEX foo',
            $this->platform->getDropIndexSQLQuery($index, 'bar')
        );
    }

    public function testDropCheck()
    {
        $check = new Schema\Check('foo', 'bar');

        $this->assertSame(
            'ALTER TABLE zaz DROP CONSTRAINT foo',
            $this->platform->getDropCheckSQLQuery($check, 'zaz')
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

        $this->assertSame(
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

        $this->assertSame('\'2012-01-01 12:12:12\'', substr($method->invoke($this->platform, $column), -21));
    }

    public function testColumnSQLDeclarationWithMandatoryType()
    {
        $column = new Schema\Column('foo', Type::getType(Type::TARRAY));

        $method = new ReflectionMethod($this->platform, 'getColumnSQLDeclaration');
        $method->setAccessible(true);

        $this->assertSame('\'(FridgeType::ARRAY)\'', substr($method->invoke($this->platform, $column), -21));
    }

    public function testColumnsSQLDeclaration()
    {
        $columns = array(
            new Schema\Column('foo', Type::getType(Type::INTEGER)),
            new Schema\Column('bar', Type::getType(Type::INTEGER)),
        );

        $method = new ReflectionMethod($this->platform, 'getColumnsSQLDeclaration');
        $method->setAccessible(true);

        $this->assertSame('foo INT, bar INT', $method->invoke($this->platform, $columns));
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
            array(
                new Schema\ForeignKey(
                    'fk1',
                    array('bar'),
                    'bar',
                    array('bar'),
                    Schema\ForeignKey::CASCADE,
                    Schema\ForeignKey::CASCADE
                )
            ),
            array(
                new Schema\Index('idx1', array('foo_bar')),
                new Schema\Index('uniq1', array('bar_foo'), true),
            ),
            array(
                new Schema\Check('ck1', 'foo > 0'),
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
        $table = new Schema\Table(
            'foo',
            array(new Schema\Column('foo', Type::getType(Type::INTEGER))),
            null,
            array(),
            array(new Schema\Index('idx1', array('foo')))
        );

        $this->assertSame(
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

        $this->assertSame(
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

        $this->assertSame(
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

        $this->assertSame(
            array('CREATE TABLE foo (foo INT)'),
            $this->platform->getCreateTableSQLQueries($table, array('index' => false))
        );
    }

    public function testCreateTableSQLQueriesWithCheckDisabled()
    {
        $table = new Schema\Table(
            'foo',
            array(new Schema\Column('foo', Type::getType(Type::INTEGER))),
            null,
            array(),
            array(),
            array(new Schema\Check('ck1', array('foo > 0')))
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
