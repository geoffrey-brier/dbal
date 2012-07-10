<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\SchemaManager;

/**
 * Executes the functional schema manager test suite on a specific database.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractSchemaManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\Tests\Fixture\FixtureInterface */
    static protected $fixture;

    /** @var \Fridge\DBAL\SchemaManager\SchemaManagerInterface */
    protected $schemaManager;

    /**
     * {@inheritdoc}
     */
    static public function setUpBeforeClass()
    {
        if (self::$fixture !== null) {
            self::$fixture->createSchema();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (($this->schemaManager === null) || (self::$fixture === null)) {
            $this->markTestSkipped();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->schemaManager->getConnection()->close();

        unset($this->schemaManager);
    }

    public function testConnection()
    {
        $this->assertInstanceOf('Fridge\DBAL\Connection\ConnectionInterface', $this->schemaManager->getConnection());
    }

    public function testGetDatabaseWithConfiguredDatabase()
    {
        $this->assertEquals(self::$fixture->getDatabase(), $this->schemaManager->getDatabase());
    }

    public function testGetDatabaseWithoutConfiguredDatabase()
    {
        $this->schemaManager->getConnection()->setDatabase(null);

        $this->assertNull($this->schemaManager->getDatabase());
    }

    public function testGetDatabases()
    {
        $this->assertTrue(in_array(self::$fixture->getDatabase(), $this->schemaManager->getDatabases()));
    }

    public function testGetSequences()
    {
        $this->assertEquals(self::$fixture->getSequences(), $this->schemaManager->getSequences());
    }

    public function testGetViews()
    {
        $this->assertEquals(self::$fixture->getViews(), $this->schemaManager->getViews());
    }

    public function testGetTableColumns()
    {
        foreach (self::$fixture->getTableNames() as $tableName) {
            $this->assertEquals(
                self::$fixture->getTableColumns($tableName),
                $this->schemaManager->getTableColumns($tableName)
            );
        }
    }

    public function testGetTablePrimaryKey()
    {
        foreach (self::$fixture->getTableNames() as $tableName) {
            $this->assertEquals(
                self::$fixture->getTablePrimaryKey($tableName),
                $this->schemaManager->getTablePrimaryKey($tableName)
            );
        }
    }

    public function testGetTableForeignKeys()
    {
        foreach (self::$fixture->getTableNames() as $tableName) {
            $this->assertEquals(
                self::$fixture->getTableForeignKeys($tableName),
                $this->schemaManager->getTableForeignKeys($tableName)
            );
        }
    }

    public function testGetTableIndexes()
    {
        foreach (self::$fixture->getTableNames() as $tableName) {
            $this->assertEquals(
                self::$fixture->getTableIndexes($tableName),
                $this->schemaManager->getTableIndexes($tableName)
            );
        }
    }

    public function testGetTable()
    {
        foreach (self::$fixture->getTableNames() as $table) {
            $this->assertEquals(self::$fixture->getTable($table), $this->schemaManager->getTable($table));
        }
    }

    public function testGetTableNames()
    {
        $tableNames = $this->schemaManager->getTableNames();

        foreach (self::$fixture->getTableNames() as $tableName) {
            $this->assertTrue(in_array($tableName, $tableNames));
        }

        $this->assertEquals(count(self::$fixture->getTableNames()), count($tableNames));
    }

    public function testGetTables()
    {
        $tables = $this->schemaManager->getTables();

        $tableNames = self::$fixture->getTableNames();
        sort($tableNames);

        foreach ($tableNames as $index => $tableName) {
            $this->assertEquals(self::$fixture->getTable($tableName), $tables[$index]);
        }

        $this->assertEquals(count($tableNames), count($tables));
    }

    public function testGetSchema()
    {
        $this->assertEquals(self::$fixture->getSchema(), $this->schemaManager->getSchema());
    }

    public function testDropSequence()
    {
        foreach (self::$fixture->getSequences() as $sequence) {
            $this->schemaManager->dropSequence($sequence->getName());
        }

        $this->assertFalse($this->schemaManager->getSchema()->hasSequences());
    }

    /**
     * @depends testDropSequence
     */
    public function testCreateSequence()
    {
        foreach (self::$fixture->getSequences() as $sequence) {
            $this->schemaManager->createSequence($sequence);
        }

        $this->assertEquals(self::$fixture->getSequences(), $this->schemaManager->getSequences());
    }

    public function testDropAndCreateSequence()
    {
        foreach (self::$fixture->getSequences() as $sequence) {
            $this->schemaManager->dropAndCreateSequence($sequence);
        }

        $this->assertEquals(self::$fixture->getSequences(), $this->schemaManager->getSequences());
    }

    public function testDropView()
    {
        foreach (self::$fixture->getViews() as $view) {
            $this->schemaManager->dropView($view->getName());
        }

        $this->assertFalse($this->schemaManager->getSchema()->hasViews());
    }

    /**
     * @depends testDropView
     */
    public function testCreateView()
    {
        foreach (self::$fixture->getViews() as $view) {
            $this->schemaManager->createView($view);
        }

        $this->assertEquals(self::$fixture->getViews(), $this->schemaManager->getViews());
    }

    public function testDropAndCreateView()
    {
        foreach (self::$fixture->getViews() as $view) {
            $this->schemaManager->dropAndCreateView($view);
        }

        $this->assertEquals(self::$fixture->getViews(), $this->schemaManager->getViews());
    }

    public function testDropPrimaryKey()
    {
        $table = 'tprimarykeyunlock';

        $primaryKey = self::$fixture->getTablePrimaryKey($table);
        $this->schemaManager->dropPrimaryKey($primaryKey->getName(), $table);

        $this->assertFalse($this->schemaManager->getTable($table)->hasPrimaryKey());
    }

    /**
     * @depends testDropPrimaryKey
     */
    public function testCreatePrimaryKey()
    {
        $table = 'tprimarykeyunlock';

        $primaryKey = self::$fixture->getTablePrimaryKey($table);
        $this->schemaManager->createPrimaryKey($primaryKey, $table);

        $this->assertEquals($primaryKey, $this->schemaManager->getTablePrimaryKey($table));
    }

    public function testDropAndCreatePrimaryKey()
    {
        $primaryKeyTable = 'tprimarykeyunlock';

        $primaryKey = self::$fixture->getTablePrimaryKey($primaryKeyTable);
        $this->schemaManager->dropAndCreatePrimaryKey($primaryKey, $primaryKeyTable);

        $this->assertEquals(
            self::$fixture->getTablePrimaryKey($primaryKeyTable),
            $this->schemaManager->getTablePrimaryKey($primaryKeyTable)
        );
    }

    public function testDropForeignKey()
    {
        $table = 'tforeignkey';

        foreach (self::$fixture->getTableForeignKeys($table) as $foreignKey) {
            $this->schemaManager->dropForeignKey($foreignKey->getName(), $table);
        }

        $this->assertFalse($this->schemaManager->getTable($table)->hasForeignKeys());
    }

    /**
     * @depends testDropForeignKey
     */
    public function testCreateForeignKey()
    {
        $table = 'tforeignkey';

        $foreignKeys = self::$fixture->getTableForeignKeys($table);

        foreach ($foreignKeys as $foreignKey) {
            $this->schemaManager->createForeignKey($foreignKey, $table);
        }

        $this->assertEquals($foreignKeys, $this->schemaManager->getTableForeignKeys($table));
    }

    public function testDropAndCreateForeignKey()
    {
        $table = 'tforeignkey';

        foreach (self::$fixture->getTableForeignKeys($table) as $foreignKey) {
            $this->schemaManager->dropAndCreateForeignKey($foreignKey, $table);
        }

        $this->assertEquals(
            self::$fixture->getTableForeignKeys($table),
            $this->schemaManager->getTableForeignKeys($table)
        );
    }

    public function testDropIndex()
    {
        $table = 'tindex';

        foreach (self::$fixture->getTableIndexes($table) as $index) {
            $this->schemaManager->dropIndex($index->getName(), $table);
        }

        $this->assertFalse($this->schemaManager->getTable($table)->hasIndexes());
    }

    /**
     * @depends testDropIndex
     */
    public function testCreateIndex()
    {
        $table = 'tindex';

        $indexes = self::$fixture->getTableIndexes($table);

        foreach ($indexes as $index) {
            $this->schemaManager->createIndex($index, $table);
        }

        $this->assertEquals($indexes, $this->schemaManager->getTableIndexes($table));
    }

    public function testDropAndCreateIndex()
    {
        $table = 'tindex';

        $indexes = self::$fixture->getTableIndexes($table);

        foreach ($indexes as $index) {
            $this->schemaManager->dropAndCreateIndex($index, $table);
        }

        $this->assertEquals($indexes, $this->schemaManager->getTableIndexes($table));
    }

    public function testDropConstraintWithPrimaryKey()
    {
        $table = 'tprimarykeyunlock';

        $this->schemaManager->dropConstraint(self::$fixture->getTablePrimaryKey($table)->getName(), $table);

        $this->assertFalse($this->schemaManager->getTable($table)->hasPrimaryKey());
    }

    /**
     * @depends testDropConstraintWithPrimaryKey
     */
    public function testCreateConstraintWithPrimaryKey()
    {
        $table = 'tprimarykeyunlock';
        $primaryKey = self::$fixture->getTablePrimaryKey($table);

        $this->schemaManager->createConstraint($primaryKey, $table);

        $this->assertEquals(
            $primaryKey,
            $this->schemaManager->getTablePrimaryKey($table)
        );
    }

    public function testDropAndCreateConstraintWithPrimaryKey()
    {
        $tableName = 'tprimarykeyunlock';
        $primaryKey = self::$fixture->getTablePrimaryKey($tableName);

        $this->schemaManager->dropAndCreateConstraint($primaryKey, $tableName);

        $this->assertEquals($primaryKey, $this->schemaManager->getTable($tableName)->getPrimaryKey());
    }

    public function testDropConstraintWithForeignKey()
    {
        $table = 'tforeignkey';

        foreach (self::$fixture->getTableForeignKeys($table) as $foreignKey) {
            $this->schemaManager->dropConstraint($foreignKey->getName(), $table);
        }

        $this->assertFalse($this->schemaManager->getTable($table)->hasForeignKeys());
    }

    /**
     * @depends testDropConstraintWithForeignKey
     */
    public function testCreateConstraintWithForeignKey()
    {
        $table = 'tforeignkey';
        $foreignKeys = self::$fixture->getTableForeignKeys($table);

        foreach ($foreignKeys as $foreignKey) {
            $this->schemaManager->createConstraint($foreignKey, $table);
        }

        $this->assertEquals(
            $foreignKeys,
            $this->schemaManager->getTableForeignKeys($table)
        );
    }

    public function testDropAndCreateConstraintWithForeignKey()
    {
        $tableName = 'tforeignkey';
        $foreignKeys = self::$fixture->getTableForeignKeys($tableName);

        foreach ($foreignKeys as $foreignKey) {
            $this->schemaManager->dropAndCreateConstraint($foreignKey, $tableName);
        }

        $table = $this->schemaManager->getTable($tableName);

        foreach ($foreignKeys as $foreignKey) {
            $this->assertEquals($foreignKey, $table->getForeignKey($foreignKey->getName()));
        }
    }

    public function testDropConstraintWithIndex()
    {
        $table = 'tindex';

        foreach (self::$fixture->getTableIndexes($table) as $index) {
            $this->schemaManager->dropConstraint($index->getName(), $table);
        }

        $this->assertFalse($this->schemaManager->getTable($table)->hasIndexes());
    }

    /**
     * @depends testDropConstraintWithIndex
     */
    public function testCreateConstraintWithIndex()
    {
        $table = 'tindex';
        $indexes = self::$fixture->getTableIndexes($table);

        foreach ($indexes as $index) {
            $this->schemaManager->createConstraint($index, $table);
        }

        $this->assertEquals(
            $indexes,
            $this->schemaManager->getTableIndexes($table)
        );
    }

    public function testDropAndCreateConstraintWithIndex()
    {
        $tableName = 'tindex';
        $indexes = self::$fixture->getTableIndexes($tableName);

        foreach ($indexes as $index) {
            $this->schemaManager->dropAndCreateConstraint($index, $tableName);
        }

        $table = $this->schemaManager->getTable($tableName);

        foreach ($indexes as $index) {
            $this->assertEquals($index, $table->getIndex($index->getName()));
        }
    }

    public function testDropTable()
    {
        $tableNames = self::$fixture->getTableNames();
        sort($tableNames);

        foreach (self::$fixture->getViews() as $view) {
            $this->schemaManager->dropView($view->getName());
        }

        foreach ($tableNames as $tableName) {
            $this->schemaManager->dropTable($tableName);
        }

        $this->assertFalse($this->schemaManager->getSchema()->hasTables());
    }

    /**
     * @depends testDropTable
     */
    public function testCreateTable()
    {
        foreach (self::$fixture->getTableNames() as $tableName) {
            $this->schemaManager->createTable(self::$fixture->getTable($tableName));
        }

        $schema = $this->schemaManager->getSchema();

        foreach (self::$fixture->getTableNames() as $tableName) {
            $table = self::$fixture->getTable($tableName);
            $table->setSchema($schema);

            $this->assertEquals($table, $schema->getTable($tableName));
        }
    }

    public function testDropAndCreateTable()
    {
        $tableNames = self::$fixture->getTableNames();
        sort($tableNames);

        foreach ($tableNames as $tableName) {
            $this->schemaManager->dropTable($tableName);
        }

        foreach (self::$fixture->getTableNames() as $tableName) {
            $this->schemaManager->dropAndCreateTable(self::$fixture->getTable($tableName));
        }

        $tables = $this->schemaManager->getTables();

        foreach ($tableNames as $index => $tableName) {
            $this->assertEquals(self::$fixture->getTable($tableName), $tables[$index]);
        }
    }

    public function testCreateDatabase()
    {
        $this->schemaManager->createDatabase('foo');
        $this->assertTrue(in_array('foo', $this->schemaManager->getDatabases()));
    }

    /**
     * @depends testCreateDatabase
     */
    public function testDropDatabase()
    {
        $this->schemaManager->dropDatabase('foo');
        $this->assertFalse(in_array('foo', $this->schemaManager->getDatabases()));
    }

    public function testDropAndCreateDatabase()
    {
        $this->schemaManager->dropAndCreateDatabase(self::$fixture->getDatabase());

        $this->assertTrue(in_array(self::$fixture->getDatabase(), $this->schemaManager->getDatabases()));
    }
}
