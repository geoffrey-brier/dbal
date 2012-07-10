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

use Fridge\DBAL\Exception\SchemaException;

/**
 * Describes a database view.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class View extends AbstractAsset
{
    /** @var string */
    protected $sql;

    /**
     * Creates a view.
     *
     * @param string $name The view name.
     * @param string $sql  The SQL query.
     */
    public function __construct($name, $sql = null)
    {
        parent::__construct($name);

        $this->sql = $sql;
    }

    /**
     * Gets the SQL query.
     *
     * @return string The SQL query.
     */
    public function getSQL()
    {
        return $this->sql;
    }

    /**
     * Sets the SQL query.
     *
     * @param string $sql The SQL query.
     */
    public function setSQL($sql)
    {
        if ((is_string($sql) && (strlen($sql) <= 0)) || (!is_string($sql) && ($sql !== null))) {
            throw SchemaException::invalidViewSQL($this->getName());
        }

        $this->sql = $sql;
    }
}
