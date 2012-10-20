<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\SchemaManager\Alteration\Schema;

use Fridge\DBAL\Schema,
    Fridge\DBAL\Type\Type,
    Fridge\Tests\DBAL\SchemaManager\Alteration\AbstractAlterationTest as BaseAlteration;

/**
 * Base schema alteration test case.
 *
 * All schema alteration tests must extend this class.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractAlterationTest extends BaseAlteration
{
    /** @var \Fridge\DBAL\Schema\Comparator\SchemaComparator */
    protected $schemaComparator;

    /** @var \Fridge\DBAL\Schema\Schema */
    protected $oldSchema;

    /** @var \Fridge\DBAL\Schema\Schema */
    protected $newSchema;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->schemaComparator = new Schema\Comparator\SchemaComparator();

        $settings = self::$fixture->getSettings();
        $this->oldSchema = new Schema\Schema($settings['dbname']);
    }

    /**
     * Set up a schema.
     */
    protected function setUpSchema()
    {
        $this->newSchema = clone $this->oldSchema;

        $this->connection->getSchemaManager()->createSchema($this->oldSchema);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        if ($this->connection !== null) {
            $this->connection->getSchemaManager()->dropSchema($this->newSchema);
        }

        parent::tearDown();

        unset($this->schemaComparator);
        unset($this->oldSchema);
        unset($this->newSchema);
    }

    public function testCreateSequence()
    {
        $this->setUpSchema();
        $this->newSchema->createSequence('foo');

        $this->assertAlteration();
    }

    public function testRenameSequence()
    {
        $this->oldSchema->createSequence('foo');
        $this->setUpSchema();

        $this->newSchema->renameSequence('foo', 'bar');

        $this->assertAlteration();
    }

    public function testAlterSequence()
    {
        $sequence = $this->oldSchema->createSequence('foo');
        $this->setUpSchema();

        $this->newSchema->getSequence($sequence->getName())->setInitialValue(10);

        $this->assertAlteration();
    }

    public function testDropSequence()
    {
        $sequence = $this->oldSchema->createSequence('foo');
        $this->setUpSchema();

        $this->newSchema->dropSequence($sequence->getName());

        $this->assertAlteration();
    }

    public function testCreateView()
    {
        $table = $this->oldSchema->createTable('foo');
        $table->createColumn('foo', Type::STRING, array('length' => 50));
        $this->setUpSchema();

        $this->newSchema->createView('vfoo', 'SELECT foo.foo FROM foo;');

        $this->assertAlteration();
    }

    public function testRenameView()
    {
        $table = $this->oldSchema->createTable('foo');
        $table->createColumn('foo', Type::STRING, array('length' => 50));
        $this->oldSchema->createView('vfoo', 'SELECT foo.foo FROM foo;');
        $this->setUpSchema();

        $this->newSchema->renameView('vfoo', 'vbar');

        $this->assertAlteration();
    }

    public function testAlterView()
    {
        $table = $this->oldSchema->createTable('foo');
        $table->createColumn('foo', Type::STRING, array('length' => 50));
        $table->createColumn('bar', Type::STRING, array('length' => 50));
        $view = $this->oldSchema->createView('vfoo', 'SELECT foo.foo FROM foo;');
        $this->setUpSchema();

        $this->newSchema->getView($view->getName())->setSQL('SELECT foo.bar FROM foo;');

        $this->assertAlteration();
    }

    public function testDropView()
    {
        $table = $this->oldSchema->createTable('foo');
        $table->createColumn('foo', Type::STRING, array('length' => 50));
        $this->oldSchema->createView('vfoo', 'SELECT foo FROM foo');
        $this->setUpSchema();

        $this->newSchema->dropView('vfoo');

        $this->assertAlteration();
    }

    public function testCreateTable()
    {
        $this->setUpSchema();

        $table = $this->newSchema->createTable('foo');
        $table->createColumn('foo', Type::STRING, array('length' => 50));

        $this->assertAlteration();
    }

    public function testRenameTable()
    {
        $table = $this->oldSchema->createTable('foo');
        $table->createColumn('foo', Type::STRING, array('length' => 50));
        $this->setUpSchema();

        $this->newSchema->renameTable('foo', 'bar');

        $this->assertAlteration();
    }

    public function testAlterTable()
    {
        $table = $this->oldSchema->createTable('foo');
        $table->createColumn('foo', Type::STRING, array('length' => 50));
        $this->setUpSchema();

        $this->newSchema->getTable('foo')->getColumn('foo')->setNotNull(true);

        $this->assertAlteration();
    }

    public function testDropTable()
    {
        $table = $this->oldSchema->createTable('foo');
        $table->createColumn('foo', Type::STRING, array('length' => 50));
        $this->setUpSchema();

        $this->newSchema->dropTable($table->getName());

        $this->assertAlteration();
    }

    public function testCreateColumn()
    {
        $table = $this->oldSchema->createTable('foo');
        $table->createColumn('foo', Type::STRING, array('length' => 50));
        $this->setUpSchema();

        $this->newSchema->getTable($table->getName())->createColumn('bar', Type::STRING, array('length' => 50));

        $this->assertAlteration();
    }

    public function testRenameColumn()
    {
        $table = $this->oldSchema->createTable('foo');
        $table->createColumn('foo', Type::STRING, array('length' => 50));
        $this->setUpSchema();

        $this->newSchema->getTable($table->getName())->renameColumn('foo', 'bar');

        $this->assertAlteration();
    }

    public function testAlterColumn()
    {
        $table = $this->oldSchema->createTable('foo');
        $column = $table->createColumn('foo', Type::STRING, array('length' => 50));
        $this->setUpSchema();

        $this->newSchema->getTable($table->getName())->getColumn($column->getName())->setNotNull(true);

        $this->assertAlteration();
    }

    public function testDropColumn()
    {
        $table = $this->oldSchema->createTable('foo');
        $table->createColumn('foo', Type::STRING, array('length' => 50));
        $table->createColumn('bar', Type::STRING, array('length' => 50));
        $this->setUpSchema();

        $this->newSchema->getTable($table->getName())->dropColumn('bar');

        $this->assertAlteration();
    }

    public function testCreatePrimaryKey()
    {
        $table = $this->oldSchema->createTable('foo');
        $table->createColumn('foo', Type::STRING, array('length' => 50));
        $this->setUpSchema();

        $this->newSchema->getTable($table->getName())->createPrimaryKey(array('foo'), 'pk_foo');

        $this->assertAlteration();
    }

    public function testAlterPrimaryKey()
    {
        $table = $this->oldSchema->createTable('foo');
        $table->createColumn('foo', Type::STRING, array('length' => 50));
        $table->createColumn('bar', Type::STRING, array('length' => 50));
        $table->createPrimaryKey(array('foo'), 'pk_foo');
        $this->setUpSchema();

        $this->newSchema->getTable($table->getName())->dropPrimaryKey();
        $this->newSchema->getTable($table->getName())->createPrimaryKey(array('bar'), 'pk_foo');

        $this->assertAlteration();
    }

    public function testDropPrimaryKey()
    {
        $table = $this->oldSchema->createTable('foo');
        $table->createColumn('foo', Type::STRING, array('length' => 50));
        $table->createPrimaryKey(array('foo'), 'pk_foo');
        $this->setUpSchema();

        $this->newSchema->getTable($table->getName())->dropPrimaryKey();

        $this->assertAlteration();
    }

    public function testCreateIndex()
    {
        $table = $this->oldSchema->createTable('foo');
        $table->createColumn('foo', Type::STRING, array('length' => 50));
        $this->setUpSchema();

        $this->newSchema->getTable($table->getName())->createIndex(array('foo'), false, 'idx_foo');

        $this->assertAlteration();
    }

    public function testRenameIndex()
    {
        $table = $this->oldSchema->createTable('foo');
        $table->createColumn('foo', Type::STRING, array('length' => 50));
        $table->createIndex(array('foo'), false, 'idx_foo');
        $this->setUpSchema();

        $this->newSchema->getTable($table->getName())->renameIndex('idx_foo', 'idx_bar');

        $this->assertAlteration();
    }

    public function testAlterIndex()
    {
        $table = $this->oldSchema->createTable('foo');
        $table->createColumn('foo', Type::STRING, array('length' => 50));
        $index = $table->createIndex(array('foo'), false, 'idx_foo');
        $this->setUpSchema();

        $this->newSchema->getTable($table->getName())->getIndex($index->getName())->setUnique(true);

        $this->assertAlteration();
    }

    public function testDropIndex()
    {
        $table = $this->oldSchema->createTable('foo');
        $table->createColumn('foo', Type::STRING, array('length' => 50));
        $index = $table->createIndex(array('foo'), false, 'idx_foo');
        $this->setUpSchema();

        $this->newSchema->getTable($table->getName())->dropIndex($index->getName());

        $this->assertAlteration();
    }

    public function testCreateForeignKey()
    {
        $table1 = $this->oldSchema->createTable('foo');
        $table1->createColumn('foo', Type::STRING, array('length' => 50));
        $table1->createPrimaryKey(array('foo'), 'pk_foo');

        $table2 = $this->oldSchema->createTable('bar');
        $table2->createColumn('foo', Type::STRING, array('length' => 50));

        $this->setUpSchema();

        $this->newSchema->getTable($table2->getName())->createForeignKey(
            array('foo'),
            'foo',
            array('foo'),
            Schema\ForeignKey::RESTRICT,
            Schema\ForeignKey::RESTRICT,
            'fk_foo'
        );

        $this->assertAlteration();
    }

    public function testDropForeignKey()
    {
        $table1 = $this->oldSchema->createTable('foo');
        $table1->createColumn('foo', Type::STRING, array('length' => 50));
        $table1->createPrimaryKey(array('foo'), 'pk_foo');

        $table2 = $this->oldSchema->createTable('bar');
        $table2->createColumn('foo', Type::STRING, array('length' => 50));
        $foreignKey = $table2->createForeignKey(
            array('foo'),
            'foo',
            array('foo'),
            Schema\ForeignKey::RESTRICT,
            Schema\ForeignKey::RESTRICT,
            'fk_foo'
        );

        $this->setUpSchema();

        $this->newSchema->getTable($table2->getName())->dropForeignKey($foreignKey->getName());

        $this->assertAlteration();
    }

    public function testCreateCheck()
    {
        $table = $this->oldSchema->createTable('foo');
        $table->createColumn('foo', Type::INTEGER);
        $this->setUpSchema();

        $this->newSchema->getTable($table->getName())->createCheck('foo > 0', 'ck_foo');

        $this->assertAlteration();
    }

    public function testRenameCheck()
    {
        $table = $this->oldSchema->createTable('foo');
        $table->createColumn('foo', Type::INTEGER);
        $table->createCheck('foo > 0', 'ck_foo');
        $this->setUpSchema();

        $this->newSchema->getTable($table->getName())->renameCheck('ck_foo', 'ck_bar');

        $this->assertAlteration();
    }

    public function testAlterCheck()
    {
        $table = $this->oldSchema->createTable('foo');
        $table->createColumn('foo', Type::INTEGER);
        $table->createColumn('bar', Type::INTEGER);
        $check = $table->createCheck('foo > 0', 'ck_foo');
        $this->setUpSchema();

        $this->newSchema->getTable($table->getName())->getCheck($check->getName())->setDefinition('bar > 0');

        $this->assertAlteration();
    }

    public function testDropCheck()
    {
        $table = $this->oldSchema->createTable('foo');
        $table->createColumn('foo', Type::INTEGER);
        $check = $table->createCheck('foo > 0', 'ck_foo');
        $this->setUpSchema();

        $this->newSchema->getTable($table->getName())->dropCheck($check->getName());

        $this->assertAlteration();
    }

    public function testRenameDatabase()
    {
        $this->setUpSchema();
        $this->newSchema->setName('bar');

        $this->assertAlterationWithoutDatabase();
    }

    public function testRenameCurrentDatabase()
    {
        $this->connection->setDatabase('foo');

        $this->oldSchema->setName('foo');
        $this->setUpSchema();
        $this->newSchema->setName('bar');

        $this->assertAlterationWithoutDatabase();
    }

    /**
     * Asserts the old schema is altered.
     */
    protected function assertAlteration()
    {
        $schemaDiff = $this->schemaComparator->compare($this->oldSchema, $this->newSchema);
        $this->connection->getSchemaManager()->alterSchema($schemaDiff);

        $this->assertEquals(
            $this->newSchema,
            $this->connection->getSchemaManager()->getSchema($this->newSchema->getName())
        );
    }

    /**
     * Asserts the old schema is altered without database.
     */
    protected function assertAlterationWithoutDatabase()
    {
        $schemaDiff = $this->schemaComparator->compare($this->oldSchema, $this->newSchema);
        $this->connection->getSchemaManager()->alterSchema($schemaDiff);

        $this->connection->setDatabase(null);

        $this->assertEquals(
            $this->newSchema,
            $this->connection->getSchemaManager()->getSchema($this->newSchema->getName())
        );
    }
}
