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

/**
 * Postgre SQL schema manager.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PostgreSQLSchemaManager extends AbstractSchemaManager
{
    /**
     * {@inheritdoc}
     *
     * The $sequence parameter contains:
     *  - name
     */
    protected function getGenericSequence($sequence)
    {
        $sql = 'SELECT'.
               '  min_value AS initial_value,'.
               '  increment_by AS increment_size'.
               ' FROM '.$sequence['name'];

        $datas = $this->getConnection()->fetchAssoc($sql);

        return parent::getGenericSequence(array(
            'name'           => $sequence['name'],
            'initial_value'  => $datas['initial_value'],
            'increment_size' => $datas['increment_size'],
        ));
    }

    /**
     * {@inheritdoc}
     *
     * The $column parameter contains:
     *  - name
     *  - type
     *  - full_type
     *  - not_null
     *  - default
     *  - comment
     */
    protected function getGenericTableColumn(array $column)
    {
        $databaseType = $column['type'];

        $length = $precision = $scale = null;

        if (!in_array($databaseType, array('time', 'timestamp'))) {
            $typeToken = '(),';
            strtok($column['full_type'], $typeToken);

            $length = $precision = strtok($typeToken);
            $length = $precision = ($length !== false) ? (int) $length : null;

            $scale = strtok($typeToken);
            $scale = ($scale !== false) ? (int) $scale : null;
        }

        $default = null;

        if ($column['default'] !== null) {
            if (preg_match('/^\'(.*)\'::character varying$/', $column['default'], $matches)) {
                $default = $matches[1];
            } elseif (preg_match('/^\'(.*)\'::timestamp without time zone$/', $column['default'], $matches)) {
                $default = $matches[1];
            } elseif (preg_match('/^\'(.*)\'::date$/', $column['default'], $matches)) {
                $default = $matches[1];
            } elseif (preg_match('/^\'(.*)\'::time without time zone/', $column['default'], $matches)) {
                $default = $matches[1];
            } else {
                $default = $column['default'];
            }
        }

        switch ($databaseType) {
            case 'decimal':
            case 'double':
            case 'double precision':
            case 'float':
            case 'float4':
            case 'float8':
            case 'money':
            case 'numeric':
            case 'real':
                $length = null;
                break;

            case 'char':
            case 'varchar':
                $precision = null;
                $scale = null;
                break;

            default:
                $length = null;
                $precision = null;
                $scale = null;
                break;
        }

        return parent::getGenericTableColumn(array(
            'name'           => $column['name'],
            'type'           => $databaseType,
            'length'         => $length,
            'precision'      => $precision,
            'scale'          => $scale,
            'unsigned'       => null,
            'fixed'          => ($databaseType === 'char') ? true : null,
            'not_null'       => $column['not_null'],
            'default'        => $default,
            'auto_increment' => null,
            'comment'        => !empty($column['comment']) ? $column['comment'] : null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function getGenericTableChecks(array $checks)
    {
        foreach ($checks as &$check) {
            $check['definition'] = substr($check['definition'], 1, -1);
        }

        return parent::getGenericTableChecks($checks);
    }
}
