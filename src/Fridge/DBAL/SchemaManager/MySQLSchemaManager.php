<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\SchemaManager;

use Fridge\DBAL\Schema;

/**
 * MySQL schema manager.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class MySQLSchemaManager extends AbstractSchemaManager
{
    /**
     * {@inheritdoc}
     */
    public function dropAndCreateConstraint(Schema\ConstraintInterface $constraint, $table)
    {
        if ($constraint instanceof Schema\PrimaryKey) {
            $this->dropAndCreatePrimaryKey($constraint, $table);
        } else if ($constraint instanceof Schema\ForeignKey) {
            $this->dropAndCreateForeignKey($constraint, $table);
        } else if ($constraint instanceof Schema\Index) {
            $this->dropAndCreateIndex($constraint, $table);
        }
    }

    /**
     * {@inheritdoc}
     *
     * The $column parameter contains:
     *  - name
     *  - type
     *  - unsigned
     *  - not_ull
     *  - default
     *  - auto_increment
     *  - comment
     */
    protected function getGenericTableColumn(array $column)
    {
        $typeToken = '(),';
        $databaseType = strtok($column['type'], $typeToken);

        $length = $precision = strtok($typeToken);
        $length = $precision = ($length !== false) ? (int) $length : null;

        $scale = strtok($typeToken);
        $scale = ($scale !== false) ? (int) $scale : null;

        $default = !empty($column['default']) ? $column['default'] : null;

        switch ($databaseType) {
            case 'decimal':
            case 'double':
            case 'float':
            case 'numeric':
            case 'real':
                $length = null;
                break;

            case 'bigint':
            case 'int':
            case 'integer':
            case 'mediumint':
            case 'smallint':
            case 'char':
            case 'longtext':
            case 'mediumint':
            case 'mediumtext':
            case 'string':
            case 'text':
            case 'tinytext':
            case 'varchar':
                $precision = $scale = null;
                break;

            default:
                $length = $precision = $scale = null;
                break;
        }

        return parent::getGenericTableColumn(array(
            'name'           => $column['name'],
            'type'           => $databaseType,
            'length'         => $length,
            'precision'      => $precision,
            'scale'          => $scale,
            'unsigned'       => $column['unsigned'] ? true : null,
            'fixed'          => ($databaseType === 'char') ? true : null,
            'not_null'       => $column['not_null'],
            'default'        => $default,
            'auto_increment' => $column['auto_increment'] ? true : null,
            'comment'        => !empty($column['comment']) ? $column['comment'] : null,
        ));
    }
}
