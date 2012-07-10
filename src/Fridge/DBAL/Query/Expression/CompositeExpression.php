<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Query\Expression;

/**
 * A composite expression groups expression according to a type (AND, OR).
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CompositeExpression
{
    /** @cosnt The AND composite expression type */
    const TYPE_AND = 'AND';

    /** @const The OR composite expression type */
    const TYPE_OR  = 'OR';

    /** @var string */
    protected $type;

    /** @var array */
    protected $parts;

    /**
     * Composite expression constructor.
     *
     * @param string $type  The type (AND, OR).
     * @param array  $parts The parts.
     */
    public function __construct($type, array $parts = array())
    {
        $this->setType($type);
        $this->setParts($parts);
    }

    /**
     * Gets the type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the type.
     *
     * @param string $type The type.
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Gets the parts.
     *
     * @return array
     */
    public function getParts()
    {
        return $this->parts;
    }

    /**
     * Set the composite expression parts.
     *
     * @param array $parts The composite expression parts.
     */
    public function setParts(array $parts)
    {
        $this->parts = array();

        foreach ($parts as $part) {
            $this->addPart($part);
        }
    }

    /**
     * Add a part to the composite expression.
     *
     * @param string|\Fridge\DBAL\Query\Expression\CompositeExpression $part The part to add to the composite expression.
     */
    public function addPart($part)
    {
        $this->parts[] = $part;
    }

    /**
     * Gets the string representation of the composite expression.
     *
     * @return string The string representation of the composite expression.
     */
    public function __toString()
    {
        if (empty($this->parts)) {
            return '';
        }

        if (count($this->parts) === 1) {
            return (string) $this->parts[0];
        }

        return '('.implode(') '.$this->type.' (', $this->parts).')';
    }
}
