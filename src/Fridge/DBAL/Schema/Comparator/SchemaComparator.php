<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Schema\Comparator;

use Fridge\DBAL\Schema;

/**
 * Schema comparator.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class SchemaComparator
{
    /** @var \Fridge\DBAL\Schema\Comparator\TableComparator */
    protected $tableComparator;

    /**
     * Schema comparator constructor.
     */
    public function __construct()
    {
        $this->tableComparator = new TableComparator();
    }

    /**
     * Compares two schemas.
     *
     * @param \Fridge\DBAL\Schema\Schema $oldSchema The old schema.
     * @param \Fridge\DBAL\Schema\Schema $newSchema The new schema.
     *
     * @return \Fridge\DBAL\Schema\Diff\SchemaDiff The difference between the two schemas.
     */
    public function compare(Schema\Schema $oldSchema, Schema\Schema $newSchema)
    {
        return new Schema\Diff\SchemaDiff(
            $oldSchema->getName(),
            $newSchema->getName(),
            $this->getCreatedTables($oldSchema, $newSchema),
            $this->getAlteredTables($oldSchema, $newSchema),
            $this->getDroppedTables($oldSchema, $newSchema),
            $this->getCreatedSequences($oldSchema, $newSchema),
            $this->getDroppedSequences($oldSchema, $newSchema),
            $this->getCreatedViews($oldSchema, $newSchema),
            $this->getDroppedViews($oldSchema, $newSchema)
        );
    }

    /**
     * Compares two sequences.
     *
     * @param \Fridge\DBAL\Schema\Sequence $oldSequence The old sequence.
     * @param \Fridge\DBAL\Schema\Sequence $newSequence The new sequence.
     *
     * @return boolean TRUE if sequences have difference else FALSE.
     */
    public function compareSequences(Schema\Sequence $oldSequence, Schema\Sequence $newSequence)
    {
        return ($oldSequence->getName() !== $newSequence->getName())
            || ($oldSequence->getInitialValue() !== $newSequence->getInitialValue())
            || ($oldSequence->getIncrementSize() !== $newSequence->getIncrementSize());
    }

    /**
     * Compares two views.
     *
     * @param \Fridge\DBAL\Schema\View $oldView The old view.
     * @param \Fridge\DBAL\Schema\View $newView The new view.
     *
     * @return boolean TRUE if views have difference else FALSE.
     */
    public function compareViews(Schema\View $oldView, Schema\View $newView)
    {
        return ($oldView->getName() !== $newView->getName())
            || ($oldView->getSQL() !== $newView->getSQL());
    }

    /**
     * Gets the created tables according to the old/new schemas.
     *
     * @param \Fridge\DBAL\Schema\Schema $oldSchema The old schema.
     * @param \Fridge\DBAL\Schema\Schema $newSchema The new schema.
     *
     * @return array The created tables.
     */
    protected function getCreatedTables(Schema\Schema $oldSchema, Schema\Schema $newSchema)
    {
        $createdTables = array();

        foreach ($newSchema->getTables() as $table) {
            if (!$oldSchema->hasTable($table->getName())) {
                $createdTables[] = $table;
            }
        }

        return $createdTables;
    }

    /**
     * Gets the altered tables according to the old/new schemas.
     *
     * @param \Fridge\DBAL\Schema\Schema $oldSchema The old schema.
     * @param \Fridge\DBAL\Schema\Schema $newSchema The new schema.
     *
     * @return array The altered tables.
     */
    protected function getAlteredTables(Schema\Schema $oldSchema, Schema\Schema $newSchema)
    {
        $alteredTables = array();

        foreach ($newSchema->getTables() as $table) {
            if ($oldSchema->hasTable($table->getName())) {
                $tableDiff = $this->tableComparator->compare($oldSchema->getTable($table->getName()), $table);

                if ($tableDiff->hasDifference()) {
                    $alteredTables[] = $tableDiff;
                }
            }
        }

        return $alteredTables;
    }

    /**
     * Gets the dropped tables according to the old/new schemas.
     *
     * @param \Fridge\DBAL\Schema\Schema $oldSchema The old schema.
     * @param \Fridge\DBAL\Schema\Schema $newSchema The new schema.
     *
     * @return array The dropped tables.
     */
    protected function getDroppedTables(Schema\Schema $oldSchema, Schema\Schema $newSchema)
    {
        $droppedTables = array();

        foreach ($oldSchema->getTables() as $table) {
            if (!$newSchema->hasTable($table->getName())) {
                $droppedTables[] = $table;
            }
        }

        return $droppedTables;
    }

    /**
     * Gets the created sequences according to the old/new schemas.
     *
     * @param \Fridge\DBAL\Schema\Schema $oldSchema The old schema.
     * @param \Fridge\DBAL\Schema\Schema $newSchema The new schema.
     *
     * @return array The created sequences.
     */
    protected function getCreatedSequences(Schema\Schema $oldSchema, Schema\Schema $newSchema)
    {
        $createdSequences = array();

        foreach ($newSchema->getSequences() as $sequence) {
            if (!$oldSchema->hasSequence($sequence->getName())
                || $this->compareSequences($oldSchema->getSequence($sequence->getName()), $sequence)) {
                $createdSequences[] = $sequence;
            }
        }

        return $createdSequences;
    }

    /**
     * Gets the dropped sequences according to the old/new schemas.
     *
     * @param \Fridge\DBAL\Schema\Schema $oldSchema The old schema.
     * @param \Fridge\DBAL\Schema\Schema $newSchema The new schema.
     *
     * @return array The dropped sequences.
     */
    protected function getDroppedSequences(Schema\Schema $oldSchema, Schema\Schema $newSchema)
    {
        $droppedSequences = array();

        foreach ($oldSchema->getSequences() as $sequence) {
            if (!$newSchema->hasSequence($sequence->getName())
                || $this->compareSequences($sequence, $newSchema->getSequence($sequence->getName()))) {
                $droppedSequences[] = $sequence;
            }
        }

        return $droppedSequences;
    }

    /**
     * Gets the created views according to the old/new schemas.
     *
     * @param \Fridge\DBAL\Schema\Schema $oldSchema The old schema.
     * @param \Fridge\DBAL\Schema\Schema $newSchema The new schema.
     *
     * @return array The created views.
     */
    protected function getCreatedViews(Schema\Schema $oldSchema, Schema\Schema $newSchema)
    {
        $createdViews = array();

        foreach ($newSchema->getViews() as $view) {
            if (!$oldSchema->hasView($view->getName())
                || $this->compareViews($oldSchema->getView($view->getName()), $view)) {
                $createdViews[] = $view;
            }
        }

        return $createdViews;
    }

    /**
     * Gets the dropped views according to the old/new schemas.
     *
     * @param \Fridge\DBAL\Schema\Schema $oldSchema The old schema.
     * @param \Fridge\DBAL\Schema\Schema $newSchema The new schema.
     *
     * @return array The dropped views.
     */
    protected function getDroppedViews(Schema\Schema $oldSchema, Schema\Schema $newSchema)
    {
        $droppedViews = array();

        foreach ($oldSchema->getViews() as $view) {
            if (!$newSchema->hasView($view->getName())
                || $this->compareViews($view, $newSchema->getView($view->getName()))) {
                $droppedViews[] = $view;
            }
        }

        return $droppedViews;
    }
}
