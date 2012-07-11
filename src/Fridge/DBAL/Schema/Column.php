<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Schema;

use Fridge\DBAL\Exception\SchemaException,
    Fridge\DBAL\Type\TypeInterface;

/**
 * Describes a database column.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Column extends AbstractAsset
{
    /** @var \Fridge\DBAL\Type\TypeInterface */
    protected $type;

    /** @var integer */
    protected $length;

    /** @var integer */
    protected $precision;

    /** @var integer */
    protected $scale;

    /** @var boolean */
    protected $unsigned;

    /** @var boolean */
    protected $fixed;

    /** @var boolean */
    protected $notNull;

    /** @var mixed */
    protected $default;

    /** @var boolean */
    protected $autoIncrement;

    /** @var string */
    protected $comment;

    /**
     * Creates a column.
     *
     * $options can contain:
     *  - length (integer)
     *  - precision (integer)
     *  - scale (integer)
     *  - unsigned (boolean)
     *  - fixed (boolean)
     *  - not_null (boolean)
     *  - default (string)
     *  - auto_increment (boolean)
     *  - comment (string)
     *
     * @param string                          $name    The column name.
     * @param \Fridge\DBAL\Type\TypeInterface $type    The column type.
     * @param array                           $options Associative array that describes property => value pairs.
     */
    public function __construct($name, TypeInterface $type, array $options = array())
    {
        parent::__construct($name);

        $this->notNull = false;

        $this->setType($type);
        $this->setOptions($options);
    }

    /**
     * Gets the column type.
     *
     * @return \Fridge\DBAL\Type\TypeInterface The column type.
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the column type.
     *
     * @param \Fridge\DBAL\Type\TypeInterface $type The column type.
     */
    public function setType(TypeInterface $type)
    {
        $this->type = $type;
    }

    /**
     * Gets the column length.
     *
     * @return integer|null The column length.
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Sets the column length.
     *
     * @param integer|null $length The column length.
     */
    public function setLength($length)
    {
        if (($length !== null) && (!is_int($length) || ($length <= 0))) {
            throw SchemaException::invalidColumnLength($this->getName());
        }

        $this->length = $length;
    }

    /**
     * Gets the column precision.
     *
     * @return integer|null The column precision.
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * Sets the column precision.
     *
     * @param integer|null $precision The column precision.
     */
    public function setPrecision($precision)
    {
        if (($precision !== null) && (!is_int($precision) || ($precision <= 0))) {
            throw SchemaException::invalidColumnPrecision($this->getName());
        }

        $this->precision = $precision;
    }

    /**
     * Gets the column scale.
     *
     * @return integer|null The column scale.
     */
    public function getScale()
    {
        return $this->scale;
    }

    /**
     * Sets the column scale.
     *
     * @param integer|null $scale The column scale.
     */
    public function setScale($scale)
    {
        if (($scale !== null) && (!is_int($scale) || ($scale < 0))) {
            throw SchemaException::invalidColumnScale($this->getName());
        }

        $this->scale = $scale;
    }

    /**
     * Gets the column unsigned flag.
     *
     * @return boolean|null The column unsigned flag.
     */
    public function isUnsigned()
    {
        return $this->unsigned;
    }

    /**
     * Sets the column unsigned flag.
     *
     * @param boolean|null $unsigned The column unsigned flag.
     */
    public function setUnsigned($unsigned)
    {
        if (($unsigned !== null) && !is_bool($unsigned)) {
            throw SchemaException::invalidColumnUnsignedFlag($this->getName());
        }

        $this->unsigned = $unsigned;
    }

    /**
     * Gets the column fixed flag.
     *
     * @return boolean|null The column fixed flag.
     */
    public function isFixed()
    {
        return $this->fixed;
    }

    /**
     * Sets the column fixed flag.
     *
     * @param boolean $fixed The column fixed flag.
     */
    public function setFixed($fixed)
    {
        if (($fixed !== null) && !is_bool($fixed)) {
            throw SchemaException::invalidColumnFixedFlag($this->getName());
        }

        $this->fixed = $fixed;
    }

    /**
     * Gets the column not null flag.
     *
     * @return boolean|null The column not null flag.
     */
    public function isNotNull()
    {
        return $this->notNull;
    }

    /**
     * Sets the column not null flag.
     *
     * @param boolean|null $notNull The column not null flag.
     */
    public function setNotNull($notNull)
    {
        if (($notNull !== null) && !is_bool($notNull)) {
            throw SchemaException::invalidColumnNotNullFlag($this->getName());
        }

        $this->notNull = $notNull;
    }

    /**
     * Gets the default column value.
     *
     * @return mixed The default column value.
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Sets the default column value.
     *
     * @param mixed $default The default column value.
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * Gets the column auto increment flag.
     *
     * @return boolean|null The column auto increment flag.
     */
    public function isAutoIncrement()
    {
        return $this->autoIncrement;
    }

    /**
     * Sets the column auto increment flag.
     *
     * @param boolean|null $autoIncrement The column auto increment flag.
     */
    public function setAutoIncrement($autoIncrement)
    {
        if (($autoIncrement !== null) && !is_bool($autoIncrement)) {
            throw SchemaException::invalidColumnAutoIncrementFlag($this->getName());
        }

        $this->autoIncrement = $autoIncrement;
    }

    /**
     * Gets the column comment.
     *
     * @return string|null The column comment.
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Sets the column comment.
     *
     * @param string|null $comment The column comment.
     */
    public function setComment($comment)
    {
        if (($comment !== null) && !is_string($comment)) {
            throw SchemaException::invalidColumnComment($this->getName());
        }

        $this->comment = $comment;
    }

    /**
     * Sets the column options.
     *
     * @param array $options Associative array that describes property => value pairs.
     */
    public function setOptions(array $options)
    {
        foreach ($options as $option => $value) {
            $method = sprintf('set%s', str_replace('_', '', $option));

            if (!method_exists($this, $method)) {
                 throw SchemaException::invalidColumnOption($this->getName(), $option);
            }

            $this->$method($value);
        }
    }

    /**
     * Converts a column to an array.
     *
     * @return array The column converted to an array.
     */
    public function toArray()
    {
        return array(
            'name'           => $this->getName(),
            'type'           => $this->getType()->getName(),
            'length'         => $this->getLength(),
            'precision'      => $this->getPrecision(),
            'scale'          => $this->getScale(),
            'unsigned'       => $this->isUnsigned(),
            'fixed'          => $this->isFixed(),
            'not_null'       => $this->isNotNull(),
            'default'        => $this->getDefault(),
            'auto_increment' => $this->isAutoIncrement(),
            'comment'        => $this->getComment(),
        );
    }
}
