<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Platform;

use Fridge\DBAL\Type\Type;

/**
 * PostgreSQL platform.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PostgreSQLPlatform extends AbstractPlatform
{
    /**
     * {@inheritdoc}
     */
    public function getBigIntegerSQLDeclaration(array $options = array())
    {
        if (isset($options['auto_increment']) && $options['auto_increment']) {
            return 'BIGSERIAL';
        }

        return parent::getBigIntegerSQLDeclaration($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getDateTimeSQLDeclaration(array $options = array())
    {
        return 'TIMESTAMP(0) WITHOUT TIME ZONE';
    }

    /**
     * {@inheritdoc}
     */
    public function getIntegerSQLDeclaration(array $options = array())
    {
        if (isset($options['auto_increment']) && $options['auto_increment']) {
            return 'SERIAL';
        }

        return parent::getIntegerSQLDeclaration($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeSQLDeclaration(array $options = array())
    {
        return 'TIME(0) WITHOUT TIME ZONE';
    }

    /**
     * {@inheritdoc}
     */
    public function supportInlineTableColumnComment()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getSetTransactionIsolationSQLQuery($isolation)
    {
        return 'SET SESSION CHARACTERISTICS AS TRANSACTION ISOLATION LEVEL '.
               $this->getTransactionIsolationSQLDeclaration($isolation);
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectDatabaseSQLQuery()
    {
        return 'SELECT current_database()';
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectDatabasesSQLQuery()
    {
        return 'SELECT'.
               '  datname AS database'.
               ' FROM pg_database'.
               ' ORDER BY database ASC';
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectSequencesSQLQuery($database)
    {
        return 'SELECT'.
               '  c.relname AS name'.
               ' FROM pg_class c'.
               ' WHERE c.relkind = \'S\''.
               ' ORDER BY name ASC';
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectViewsSQLQuery($database)
    {
        return 'SELECT'.
               '  viewname AS name,'.
               '  definition AS sql'.
               ' FROM pg_views'.
               ' WHERE schemaname NOT IN (\'pg_catalog\', \'information_schema\')'.
               ' ORDER BY name ASC';
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectTableNamesSQLQuery($database)
    {
        return 'SELECT'.
               '  tablename AS name'.
               ' FROM pg_tables'.
               ' WHERE tablename NOT LIKE \'pg_%\''.
               ' AND tablename NOT LIKE \'sql_%\''.
               ' ORDER BY name ASC';
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectTableColumnsSQLQuery($table, $database)
    {
        return 'SELECT'.
               '  a.attname AS name,'.
               '  t.typname AS type,'.
               '  format_type(a.atttypid, a.atttypmod) AS full_type,'.
               '  a.attnotnull AS not_null,'.
               '  ad.adsrc AS default,'.
               '  d.description AS comment'.
               ' FROM pg_attribute a'.
               ' INNER JOIN pg_class c ON (a.attrelid = c.oid AND c.relname = \''.$table.'\')'.
               ' INNER JOIN pg_type t ON a.atttypid = t.oid'.
               ' LEFT JOIN pg_attrdef ad ON (a.attnum = ad.adnum AND c.oid = ad.adrelid)'.
               ' LEFT JOIN pg_description d ON (a.attnum = d.objsubid AND c.oid = d.objoid)'.
               ' WHERE a.attnum > 0'.
               ' ORDER BY a.attnum ASC';
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectTablePrimaryKeySQLQuery($table, $database)
    {
        return 'SELECT'.
               '  co.conname AS name,'.
               '  a.attname AS column_name'.
               ' FROM pg_constraint co'.
               ' INNER JOIN pg_class c ON (co.conrelid = c.oid AND c.relname = \''.$table.'\')'.
               ' INNER JOIN pg_attribute a ON (c.oid = a.attrelid AND a.attnum > 0)'.
               ' INNER JOIN pg_index i ON (c.oid = i.indrelid AND a.attnum = any(i.indkey) AND i.indisprimary = \'t\')';
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectTableForeignKeysSQLQuery($table, $database)
    {
        return 'SELECT'.
               '  sq.conname AS name,'.
               '  a.attname AS local_column_name,'.
               '  sq.confrelid::regclass AS foreign_table_name,'.
               '  a1.attname AS foreign_column_name'.
               ' FROM'.
               ' ('.
               '  SELECT'.
               '   ssq.conname,'.
               '   ssq.conrelid,'.
               '   ssq.confrelid,'.
               '   ssq.conkey[i] AS conkey,'.
               '   ssq.confkey[i] as confkey,'.
               '   i AS position'.
               '  FROM'.
               '  ('.
               '   SELECT'.
               '    co.conname,'.
               '    co.conrelid,'.
               '    co.confrelid,'.
               '    co.conkey,'.
               '    co.confkey,'.
               '    generate_series(1, array_upper(co.conkey, 1)) AS i'.
               '   FROM pg_constraint co'.
               '   INNER JOIN pg_class c ON (co.conrelid = c.oid AND c.relname = \''.$table.'\')'.
               '   WHERE co.contype = \'f\''.
               '  ) AS ssq'.
               ' ) AS sq'.
               ' INNER JOIN pg_attribute a ON (sq.conrelid = a.attrelid AND sq.conkey = a.attnum)'.
               ' INNER JOIN pg_attribute a1 ON (sq.confrelid = a1.attrelid AND sq.confkey = a1.attnum)'.
               ' ORDER BY sq.conname ASC, sq.position ASC';
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectTableIndexesSQLQuery($table, $database)
    {
        return 'SELECT'.
               '  c2.relname AS name,'.
               '  a.attname AS column_name,'.
               '  i.indisunique AS unique'.
               ' FROM pg_index i'.
               ' INNER JOIN pg_class c1 ON (i.indrelid = c1.oid AND c1.relname = \''.$table.'\')'.
               ' INNER JOIN pg_class c2 ON (i.indexrelid = c2.oid)'.
               ' INNER JOIN pg_attribute a ON (c1.oid = a.attrelid AND a.attnum = any(i.indkey) AND a.attnum > 0)'.
               ' ORDER BY name ASC';
    }

    /**
     * {@inheritdoc}
     */
    protected function initializeMappedTypes()
    {
        $this->mappedTypes = array(
            'bigint'           => Type::BIGINTEGER,
            'bigserial'        => Type::BIGINTEGER,
            'bool'             => Type::BOOLEAN,
            'char'             => Type::STRING,
            'date'             => Type::DATE,
            'datetime'         => Type::DATETIME,
            'decimal'          => Type::DECIMAL,
            'double'           => Type::FLOAT,
            'double precision' => Type::FLOAT,
            'float'            => Type::FLOAT,
            'float4'           => Type::FLOAT,
            'float8'           => Type::FLOAT,
            'int'              => Type::INTEGER,
            'int2'             => Type::SMALLINTEGER,
            'int4'             => Type::INTEGER,
            'int8'             => Type::BIGINTEGER,
            'integer'          => Type::INTEGER,
            'interval'         => Type::STRING,
            'money'            => Type::DECIMAL,
            'numeric'          => Type::DECIMAL,
            'real'             => Type::FLOAT,
            'serial'           => Type::INTEGER,
            'serial4'          => Type::INTEGER,
            'serial8'          => Type::BIGINTEGER,
            'smallint'         => Type::SMALLINTEGER,
            'text'             => Type::TEXT,
            'time'             => Type::TIME,
            'timestamp'        => Type::DATETIME,
            'varchar'          => Type::STRING,
            'year'             => Type::DATE,
        );
    }
}
