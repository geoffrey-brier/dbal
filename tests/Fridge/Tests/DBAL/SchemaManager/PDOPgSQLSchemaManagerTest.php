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

use Fridge\DBAL\SchemaManager\PostgreSQLSchemaManager,
    Fridge\Tests\ConnectionUtility,
    Fridge\Tests\Fixture\PostgreSQLFixture;

/**
 * PDO PostgreSQL schema manager test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PDOPgSQLSchemaManagerTest extends AbstractSchemaManagerTest
{
    /**
     * {@inheritdoc}
     */
    static public function setUpBeforeClass()
    {
        if (ConnectionUtility::hasConnection(ConnectionUtility::PDO_PGSQL)) {
            self::$fixture = new PostgreSQLFixture();
        }

        parent::setUpBeforeClass();
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (ConnectionUtility::hasConnection(ConnectionUtility::PDO_PGSQL)) {
            $this->schemaManager = new PostgreSQLSchemaManager(ConnectionUtility::getConnection(ConnectionUtility::PDO_PGSQL));
        }

        parent::setUp();
    }

    public function testGetDatabaseWithoutConfiguredDatabase()
    {
        $this->schemaManager->getConnection()->setDatabase(null);

        $this->assertEquals('postgres', $this->schemaManager->getDatabase());
    }

    public function testGetTableIndexes()
    {
        parent::testGetTableIndexes();
    }

    public function testDropIndex()
    {
        $tableName = 'tindex';

        foreach (self::$fixture->getTableIndexes($tableName) as $index) {
            if (!$index->isUnique()) {
                $this->schemaManager->dropIndex($index->getName(), $tableName);
            }
        }

        $table = $this->schemaManager->getTable($tableName);

        foreach (self::$fixture->getTableIndexes($tableName) as $index) {
            if (!$index->isUnique()) {
                $this->assertFalse($table->hasIndex($index->getName()));
            }
        }
    }

    /**
     * @depends testDropIndex
     */
    public function testCreateIndex()
    {
        $table = 'tindex';
        $indexes = self::$fixture->getTableIndexes($table);

        foreach ($indexes as $index) {
            if (!$index->isUnique()) {
                $this->schemaManager->createIndex($index, $table);
            }
        }

        $this->assertEquals($indexes, $this->schemaManager->getTableIndexes($table));
    }

    public function testDropAndCreateIndex()
    {
        $table = 'tindex';

        $indexes = self::$fixture->getTableIndexes($table);

        foreach ($indexes as $index) {
            if (!$index->isUnique()) {
                $this->schemaManager->dropAndCreateIndex($index, $table);
            }
        }

        $this->assertEquals($indexes, $this->schemaManager->getTableIndexes($table));
    }

    public function testDropConstraintWithIndex()
    {
        $tableName = 'tindex';
        $indexes = self::$fixture->getTableIndexes($tableName);

        foreach ($indexes as $index) {
            if ($index->isUnique()) {
                $this->schemaManager->dropConstraint($index->getName(), $tableName);
            }
        }

        $table = $this->schemaManager->getTable($tableName);

        foreach ($indexes as $index) {
            if ($index->isUnique()) {
                $this->assertFalse($table->hasIndex($index->getName()));
            }
        }
    }

    /**
     * @depends testDropConstraintWithIndex
     */
    public function testCreateConstraintWithIndex()
    {
        $tableName = 'tindex';
        $indexes = self::$fixture->getTableIndexes($tableName);

        foreach ($indexes as $index) {
            if ($index->isUnique()) {
                $this->schemaManager->createConstraint($index, $tableName);
            }
        }

        $table = $this->schemaManager->getTable($tableName);

        foreach ($indexes as $index) {
            if ($index->isUnique()) {
                $this->assertEquals($table->getIndex($index->getName()), $index);
            }
        }
    }

    public function testDropAndCreateConstraintWithIndex()
    {
        $tableName = 'tindex';
        $indexes = self::$fixture->getTableForeignKeys($tableName);

        foreach ($indexes as $index) {
            if ($index->isUnique()) {
                $this->schemaManager->dropAndCreateConstraint($index, $tableName);
            }
        }

        $table = $this->schemaManager->getTable($tableName);

        foreach ($indexes as $index) {
            if ($index->isUnique()) {
                $this->assertEquals($index, $table->getIndex($index->getName()));
            }
        }
    }
}
