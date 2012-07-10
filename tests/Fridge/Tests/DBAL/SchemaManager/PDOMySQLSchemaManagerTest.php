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

use Fridge\DBAL\SchemaManager\MySQLSchemaManager,
    Fridge\Tests\ConnectionUtility,
    Fridge\Tests\Fixture\MySQLFixture;

/**
 * PDO MySQL schema manager test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PDOMySQLSchemaManagerTest extends AbstractSchemaManagerTest
{
    /**
     * {@inheritdoc}
     */
    static public function setUpBeforeClass()
    {
        if (ConnectionUtility::hasConnection(ConnectionUtility::PDO_MYSQL)) {
            self::$fixture = new MySQLFixture();
        }

        parent::setUpBeforeClass();
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (ConnectionUtility::hasConnection(ConnectionUtility::PDO_MYSQL)) {
            $this->schemaManager = new MySQLSchemaManager(ConnectionUtility::getConnection(ConnectionUtility::PDO_MYSQL));
        }

        parent::setUp();
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testGetSequences()
    {
        $this->schemaManager->getSequences();
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testCreateSequence()
    {
        $sequenceMock = $this->getMock('Fridge\DBAL\Schema\Sequence', array(), array('foo'));

        $this->schemaManager->createSequence($sequenceMock);
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testDropSequence()
    {
        $this->schemaManager->dropSequence('foo');
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testDropAndCreateSequence()
    {
        $sequenceMock = $this->getMock('Fridge\DBAL\Schema\Sequence', array(), array('foo'));

        $this->schemaManager->dropAndCreateSequence($sequenceMock);
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testDropConstraintWithPrimaryKey()
    {
        $this->schemaManager->dropConstraint('foo', 'bar');
    }

    public function testCreateConstraintWithPrimaryKey()
    {
        $tableName = 'tprimarykeyunlock';

        $this->schemaManager->dropPrimaryKey(self::$fixture->getTablePrimaryKey($tableName)->getName(), $tableName);

        parent::testCreateConstraintWithPrimaryKey();
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testDropConstraintWithForeignKey()
    {
        $this->schemaManager->dropConstraint('foo', 'bar');
    }

    public function testCreateConstraintWithForeignKey()
    {
        $table = 'tforeignkey';

        foreach (self::$fixture->getTableForeignKeys($table) as $foreignKey) {
            $this->schemaManager->dropForeignKey($foreignKey->getName(), $table);
        }

        parent::testCreateConstraintWithForeignKey();
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testDropConstraintWithIndex()
    {
        $this->schemaManager->dropConstraint('foo', 'bar');
    }

    public function testCreateConstraintWithIndex()
    {
        $table = 'tindex';

        foreach (self::$fixture->getTableIndexes($table) as $index) {
            $this->schemaManager->dropIndex($index->getName(), $table);
        }

        parent::testCreateConstraintWithIndex();
    }
}
