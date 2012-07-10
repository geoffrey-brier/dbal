<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\DBAL\Schema\Comparator;

use Fridge\DBAL\Schema,
    Fridge\DBAL\Type\Type;

/**
 * Schema comparator test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class SchemaComparatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Fridge\DBAL\Schema\Comparator\SchemaComparator */
    protected $schemaComparator;

    /** @var \Fridge\DBAL\Schema\Schema */
    protected $oldSchema;

    /** @var \Fridge\DBAL\Schema\Schema */
    protected $newSchema;

    /** @var \Fridge\DBAL\Schema\Table */
    protected $oldTable;

    /** @var \Fridge\DBAL\Schema\Table */
    protected $newTable;

    /** @var \Fridge\DBAL\Schema\Sequence */
    protected $oldSequence;

    /** @var \Fridge\DBAL\Schema\Sequence */
    protected $newSequence;

    /** @var \Fridge\DBAL\Schema\View */
    protected $oldView;

    /** @var \Fridge\DBAL\Schema\View */
    protected $newView;

    /** @var \Fridge\DBAL\Schema\Column */
    protected $column;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->schemaComparator = new Schema\Comparator\SchemaComparator();

        $this->oldSchema = new Schema\Schema('foo');
        $this->newSchema = new Schema\Schema('foo');

        $this->oldTable = new Schema\Table('foo');
        $this->newTable = new Schema\Table('foo');

        $this->oldSequence = new Schema\Sequence('foo');
        $this->newSequence = new Schema\Sequence('foo');

        $this->oldView = new Schema\View('foo');
        $this->newView = new Schema\View('foo');

        $this->column = new Schema\Column('foo', Type::getType(Type::INTEGER));
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->schemaComparator);

        unset($this->oldSchema);
        unset($this->newSchema);

        unset($this->oldTable);
        unset($this->newTable);

        unset($this->oldSequence);
        unset($this->newSequence);

        unset($this->oldView);
        unset($this->newView);
    }

    public function testCompareSequencesWithoutDifference()
    {
        $this->assertFalse($this->schemaComparator->compareSequences($this->oldSequence, $this->newSequence));
    }

    public function testCompareSequencesWithNameDifference()
    {
        $this->newSequence->setName('bar');

        $this->assertTrue($this->schemaComparator->compareSequences($this->oldSequence, $this->newSequence));
    }

    public function testCompareSequencesWithInitialValueDifference()
    {
        $this->newSequence->setInitialValue(2);

        $this->assertTrue($this->schemaComparator->compareSequences($this->oldSequence, $this->newSequence));
    }

    public function testCompareSequenceWithIncrementSizeDifference()
    {
        $this->newSequence->setIncrementSize(2);

        $this->assertTrue($this->schemaComparator->compareSequences($this->oldSequence, $this->newSequence));
    }

    public function testCompareViewsWithoutDifference()
    {
        $this->assertFalse($this->schemaComparator->compareViews($this->oldView, $this->newView));
    }

    public function testCompareViewWithNameDifference()
    {
        $this->newView->setName('bar');

        $this->assertTrue($this->schemaComparator->compareViews($this->oldView, $this->newView));
    }

    public function testCompareViewWithSQLDifference()
    {
        $this->newView->setSQL('foo');

        $this->assertTrue($this->schemaComparator->compareViews($this->oldView, $this->newView));
    }

    public function testCompareWithoutDifference()
    {
        $schemaDiff = $this->schemaComparator->compare($this->oldSchema, $this->newSchema);

        $this->assertEquals('foo', $schemaDiff->getOldName());
        $this->assertEquals('foo', $schemaDiff->getNewName());
        $this->assertFalse($schemaDiff->hasDifference());
    }

    public function testCompareWithNameDifference()
    {
        $this->newSchema->setName('bar');

        $schemaDiff = $this->schemaComparator->compare($this->oldSchema, $this->newSchema);

        $this->assertEquals('foo', $schemaDiff->getOldName());
        $this->assertEquals('bar', $schemaDiff->getNewName());
    }

    public function testCompareWithCreatedTablesDifference()
    {
        $this->newSchema->addTable($this->newTable);

        $schemaDiff = $this->schemaComparator->compare($this->oldSchema, $this->newSchema);

        $this->assertEquals(array($this->newTable), $schemaDiff->getCreatedTables());
    }

    public function testCompareWithAlteredTablesDifference()
    {
        $this->newTable->addColumn($this->column);

        $this->oldSchema->addTable($this->oldTable);
        $this->newSchema->addTable($this->newTable);

        $schemaDiff = $this->schemaComparator->compare($this->oldSchema, $this->newSchema);

        $alteredTables = $schemaDiff->getAlteredTables();

        $this->assertArrayHasKey(0, $alteredTables);
        $this->assertEquals($this->oldTable->getName(), $alteredTables[0]->getOldName());
        $this->assertEquals($this->newTable->getName(), $alteredTables[0]->getNewName());
        $this->assertEquals(array($this->column), $alteredTables[0]->getCreatedColumns());
    }

    public function testCompareWithDroppedTablesDifference()
    {
        $this->oldSchema->addTable($this->oldTable);

        $schemaDiff = $this->schemaComparator->compare($this->oldSchema, $this->newSchema);

        $this->assertEquals(array($this->oldTable), $schemaDiff->getDroppedTables());
    }

    public function testCompareWithCreatedSequencesDifference()
    {
        $this->newSchema->addSequence($this->newSequence);

        $schemaDiff = $this->schemaComparator->compare($this->oldSchema, $this->newSchema);

        $this->assertEquals(array($this->newSequence), $schemaDiff->getCreatedSequences());
    }

    public function testCompareWithAlteredSequecesDifference()
    {
        $this->newSequence->setIncrementSize(2);

        $this->oldSchema->addSequence($this->oldSequence);
        $this->newSchema->addSequence($this->newSequence);

        $schemaDiff = $this->schemaComparator->compare($this->oldSchema, $this->newSchema);

        $this->assertEquals(array($this->newSequence), $schemaDiff->getCreatedSequences());
        $this->assertEquals(array($this->oldSequence), $schemaDiff->getDroppedSequences());
    }

    public function testCompareWithDroppedSeqencesDifference()
    {
        $this->oldSchema->addSequence($this->oldSequence);

        $schemaDiff = $this->schemaComparator->compare($this->oldSchema, $this->newSchema);

        $this->assertEquals(array($this->oldSequence), $schemaDiff->getDroppedSequences());
    }

    public function testCompareWithCreatedViewsDifference()
    {
        $this->newSchema->addView($this->newView);

        $schemaDiff = $this->schemaComparator->compare($this->oldSchema, $this->newSchema);

        $this->assertEquals(array($this->newView), $schemaDiff->getCreatedViews());
    }

    public function testCompareWithAlteredViewsDifference()
    {
        $this->newView->setSQL('foo');

        $this->oldSchema->addView($this->oldView);
        $this->newSchema->addView($this->newView);

        $schemaDiff = $this->schemaComparator->compare($this->oldSchema, $this->newSchema);

        $this->assertEquals(array($this->newView), $schemaDiff->getCreatedViews());
        $this->assertEquals(array($this->oldView), $schemaDiff->getDroppedViews());
    }

    public function testCompareWithDroppedViewsDifference()
    {
        $this->oldSchema->addView($this->oldView);

        $schemaDiff = $this->schemaComparator->compare($this->oldSchema, $this->newSchema);

        $this->assertEquals(array($this->oldView), $schemaDiff->getDroppedViews());
    }
}
