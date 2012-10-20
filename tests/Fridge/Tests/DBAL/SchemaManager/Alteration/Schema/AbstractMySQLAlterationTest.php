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

use Fridge\DBAL\Type\Type;

/**
 * Base MySQL schema alteration test case
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractMySQLAlterationTest extends AbstractAlterationTest
{
    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testCreateSequence()
    {
        parent::testCreateSequence();
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testRenameSequence()
    {
        parent::testRenameSequence();
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testAlterSequence()
    {
        parent::testAlterSequence();
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testDropSequence()
    {
        parent::testDropSequence();
    }

    public function testCreateView()
    {
        $table = $this->oldSchema->createTable('foo');
        $table->createColumn('foo', Type::STRING, array('length' => 50));
        $this->setUpSchema();

        $this->newSchema->createView('vfoo', 'select `dbal_test`.`foo`.`foo` AS `foo` from `dbal_test`.`foo`');

        $this->assertAlteration();
    }

    public function testRenameView()
    {
        $table = $this->oldSchema->createTable('foo');
        $table->createColumn('foo', Type::STRING, array('length' => 50));
        $this->oldSchema->createView('vfoo', 'select `dbal_test`.`foo`.`foo` AS `foo` from `dbal_test`.`foo`');
        $this->setUpSchema();

        $this->newSchema->renameView('vfoo', 'vbar');

        $this->assertAlteration();
    }

    public function testAlterView()
    {
        $table = $this->oldSchema->createTable('foo');
        $table->createColumn('foo', Type::STRING, array('length' => 50));
        $table->createColumn('bar', Type::STRING, array('length' => 50));
        $view = $this->oldSchema->createView('vfoo', 'select `dbal_test`.`foo`.`foo` AS `foo` from `dbal_test`.`foo`');
        $this->setUpSchema();

        $this->newSchema
            ->getView($view->getName())
            ->setSQL('select `dbal_test`.`foo`.`bar` AS `bar` from `dbal_test`.`foo`');

        $this->assertAlteration();
    }

    public function testCreatePrimaryKey()
    {
        $table = $this->oldSchema->createTable('foo');
        $table->createColumn('foo', Type::STRING, array('length' => 50));
        $this->setUpSchema();

        $this->newSchema->getTable($table->getName())->createPrimaryKey(array('foo'), 'PRIMARY');

        $this->assertAlteration();
    }

    public function testAlterPrimaryKey()
    {
        $table = $this->oldSchema->createTable('foo');
        $table->createColumn('foo', Type::STRING, array('length' => 50));
        $table->createColumn('bar', Type::STRING, array('length' => 50));
        $table->createPrimaryKey(array('foo'), 'PRIMARY');
        $this->setUpSchema();

        $this->newSchema->getTable($table->getName())->dropPrimaryKey();
        $this->newSchema->getTable($table->getName())->createPrimaryKey(array('bar'), 'PRIMARY');

        $this->assertAlteration();
    }

    public function testCreateForeignKey()
    {
        $table1 = $this->oldSchema->createTable('foo');
        $table1->createColumn('foo', Type::STRING, array('length' => 50));
        $table1->createPrimaryKey(array('foo'), 'PRIMARY');

        $table2 = $this->oldSchema->createTable('bar');
        $table2->createColumn('foo', Type::STRING, array('length' => 50));

        $this->setUpSchema();

        $this->newSchema->getTable($table2->getName())->createForeignKey(array('foo'), 'foo', array('foo'), 'fk_foo');

        $this->assertAlteration();
    }

    public function testDropForeignKey()
    {
        $table1 = $this->oldSchema->createTable('foo');
        $table1->createColumn('foo', Type::STRING, array('length' => 50));
        $table1->createPrimaryKey(array('foo'), 'PRIMARY');

        $table2 = $this->oldSchema->createTable('bar');
        $table2->createColumn('foo', Type::STRING, array('length' => 50));
        $foreignKey = $table2->createForeignKey(array('foo'), 'foo', array('foo'), 'fk_foo');

        $this->setUpSchema();

        $this->newSchema->getTable($table2->getName())->dropForeignKey($foreignKey->getName());

        $this->assertAlteration();
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testCreateCheck()
    {
        parent::testCreateCheck();
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testRenameCheck()
    {
        parent::testRenameCheck();
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testAlterCheck()
    {
        parent::testAlterCheck();
    }

    /**
     * @expectedException \Fridge\DBAL\Exception\PlatformException
     */
    public function testDropCheck()
    {
        parent::testDropCheck();
    }
}
