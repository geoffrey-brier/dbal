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

use Fridge\DBAL\Schema\Diff\ColumnDiff,
    Fridge\DBAL\Schema\Diff\SchemaDiff,
    Fridge\DBAL\Schema\ForeignKey,
    Fridge\DBAL\Schema\Index,
    Fridge\DBAL\Schema\PrimaryKey,
    Fridge\DBAL\Schema\Table,
    Fridge\DBAL\Type\Type;

/**
 * MySQL Platform.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class MySQLPlatform extends AbstractPlatform
{
    /**
     * {@inheritdoc}
     */
    public function getBigIntegerSQLDeclaration(array $options = array())
    {
        return parent::getBigIntegerSQLDeclaration($options).$this->getIntegerSQLDeclarationSnippet($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getBooleanSQLDeclaration(array $options = array())
    {
        return 'TINYINT(1)';
    }

    /**
     * {@inheritdoc}
     */
    public function getClobSQLDeclaration(array $options = array())
    {
        $length = isset($options['length']) ? $options['length'] : null;

        if ($length !== null) {
            if ($length <= 255) {
                return 'TINYTEXT';
            }

            if ($length <= 65535) {
                return parent::getClobSQLDeclaration($options);
            }

            if ($length <= 16777215) {
                return 'MEDIUMTEXT';
            }
        }

        return 'LONGTEXT';
    }

    /**
     * {@inheritdoc}
     */
    public function getDateTimeSQLDeclaration(array $options = array())
    {
        if (isset($options['version']) && $options['version']) {
            return 'TIMESTAMP';
        }

        return parent::getDateTimeSQLDeclaration($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getIntegerSQLDeclaration(array $options = array())
    {
        return parent::getIntegerSQLDeclaration($options).$this->getIntegerSQLDeclarationSnippet($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getSmallIntegerSQLDeclaration(array $options = array())
    {
        return parent::getSmallIntegerSQLDeclaration($options).$this->getIntegerSQLDeclarationSnippet($options);
    }

    /**
     * {@inheritdoc}
     */
    public function supportSequence()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function supportCheck()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getSetTransactionIsolationSQLQuery($isolation)
    {
        return 'SET SESSION TRANSACTION ISOLATION LEVEL '.$this->getTransactionIsolationSQLDeclaration($isolation);
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectDatabaseSQLQuery()
    {
        return 'SELECT DATABASE()';
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectDatabasesSQLQuery()
    {
        return 'SELECT schema_name AS '.$this->quoteIdentifier('database').
               ' FROM information_schema.schemata'.
               ' ORDER BY `database` ASC';
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectViewsSQLQuery($database)
    {
        return 'SELECT'.
               '  table_name AS name,'.
               '  view_definition AS `sql`'.
               ' FROM information_schema.views'.
               ' WHERE table_schema = '.$this->quote($database).
               ' ORDER BY name ASC';
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectTableNamesSQLQuery($database)
    {
        return 'SELECT'.
               '  table_name AS name'.
               ' FROM information_schema.tables'.
               ' WHERE table_schema = '.$this->quote($database).
               ' AND table_type = '.$this->quote('BASE TABLE').
               ' ORDER BY name ASC';
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectTableColumnsSQLQuery($table, $database)
    {
        return 'SELECT'.
               '  column_name AS name,'.
               '  column_type AS type,'.
               '  IF (column_type REGEXP '.$this->quote('.*unsigned.*').', true, NULL) AS '.$this->quoteIdentifier('unsigned').','.
               '  IF (is_nullable = '.$this->quote('NO').', TRUE, FALSE) AS not_null,'.
               '  column_default AS '.$this->quoteIdentifier('default').','.
               '  IF (extra = '.$this->quote('auto_increment').', TRUE, NULL) AS auto_increment,'.
               '  column_comment AS comment'.
               ' FROM information_schema.columns'.
               ' WHERE table_schema = '.$this->quote($database).
               ' AND table_name = '.$this->quote($table).
               ' ORDER BY ordinal_position ASC';
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectTablePrimaryKeySQLQuery($table, $database)
    {
        return 'SELECT'.
               '  c.constraint_name AS name,'.
               '  k.column_name'.
               ' FROM information_schema.table_constraints c'.
               ' INNER JOIN information_schema.key_column_usage k'.
               ' ON'.
               ' ('.
               '  c.table_name = k.table_name'.
               '  AND c.table_schema = k.table_schema'.
               '  AND c.constraint_name = k.constraint_name'.
               ' )'.
               ' WHERE c.constraint_type = '.$this->quote('PRIMARY KEY').
               ' AND c.table_schema = '.$this->quote($database).
               ' AND c.table_name = '.$this->quote($table).
               ' ORDER BY k.ordinal_position ASC';
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectTableForeignKeysSQLQuery($table, $database)
    {
        return 'SELECT'.
               '  c.constraint_name AS name,'.
               '  k.column_name AS local_column_name,'.
               '  k.referenced_table_name AS foreign_table_name,'.
               '  k.referenced_column_name AS foreign_column_name,'.
               '  rc.delete_rule AS on_delete,'.
               '  rc.update_rule AS on_update'.
               ' FROM information_schema.table_constraints c'.
               ' INNER JOIN information_schema.key_column_usage k'.
               ' ON'.
               ' ('.
               '  c.table_name = k.table_name'.
               '  AND c.table_schema = k.table_schema'.
               '  AND c.constraint_name = k.constraint_name'.
               ' )'.
               ' INNER JOIN information_schema.referential_constraints rc'.
               ' ON'.
               ' ('.
               '  rc.table_name = c.table_name'.
               '  AND rc.constraint_name = c.constraint_name'.
               ' )'.
               ' WHERE c.constraint_type = '.$this->quote('FOREIGN KEY').
               ' AND c.table_schema = '.$this->quote($database).
               ' AND c.table_name = '.$this->quote($table).
               ' ORDER BY c.constraint_name ASC, k.ordinal_position ASC';
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectTableIndexesSQLQuery($table, $database)
    {
        return 'SELECT'.
               '  s.index_name AS name,'.
               '  s.column_name,'.
               '  !s.non_unique AS '.$this->quoteIdentifier('unique').
               ' FROM information_schema.statistics s'.
               ' WHERE s.table_schema = '.$this->quote($database).
               ' AND s.table_name = '.$this->quote($table).
               ' ORDER BY s.index_name ASC, s.seq_in_index ASC';
    }

    /**
     * {@inheritdoc}
     */
    public function getCreateTableSQLQueries(Table $table, array $flags = array())
    {
        $queries = parent::getCreateTableSQLQueries($table, $flags);

        $queries[0] .= ' ENGINE = InnoDB';

        return $queries;
    }

    /**
     * {@inheritdoc}
     */
    public function getRenameDatabaseSQLQueries(SchemaDiff $schemaDiff)
    {
        $queries = $this->getCreateDatabaseSQLQueries($schemaDiff->getNewAsset()->getName());

        foreach ($schemaDiff->getNewAsset()->getTables() as $table) {
            $queries[] = 'RENAME TABLE '.$schemaDiff->getOldAsset()->getName().'.'.$table->getName().
                         ' TO '.$schemaDiff->getNewAsset()->getName().'.'.$table->getName();
        }

        return array_merge($queries, $this->getDropDatabaseSQLQueries($schemaDiff->getOldAsset()->getName()));
    }

    /**
     * {@inheritdoc}
     */
    public function getAlterColumnSQLQueries(ColumnDiff $columnDiff, $table)
    {
        return array(
            'ALTER TABLE '.$table.' CHANGE COLUMN '.$columnDiff->getOldAsset()->getName().' '.
            $this->getColumnSQLDeclaration($columnDiff->getNewAsset())
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getDropPrimaryKeySQLQueries(PrimaryKey $primaryKey, $table)
    {
        return array('ALTER TABLE '.$table.' DROP PRIMARY KEY');
    }

    /**
     * {@inheritdoc}
     */
    public function getDropForeignKeySQLQueries(ForeignKey $foreignKey, $table)
    {
        return array('ALTER TABLE '.$table.' DROP FOREIGN KEY '.$foreignKey->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function getDropIndexSQLQueries(Index $index, $table)
    {
        return array('ALTER TABLE '.$table.' DROP INDEX '.$index->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function getQuoteIdentifier()
    {
        return '`';
    }

    /**
     * {@inheritdoc}
     */
    protected function initializeMappedTypes()
    {
        $this->mappedTypes = array(
            'bigint'     => Type::BIGINTEGER,
            'char'       => Type::STRING,
            'date'       => Type::DATE,
            'datetime'   => Type::DATETIME,
            'decimal'    => Type::DECIMAL,
            'double'     => Type::FLOAT,
            'float'      => Type::FLOAT,
            'int'        => Type::INTEGER,
            'integer'    => Type::INTEGER,
            'longtext'   => Type::TEXT,
            'mediumint'  => Type::INTEGER,
            'mediumtext' => Type::TEXT,
            'numeric'    => Type::DECIMAL,
            'real'       => Type::FLOAT,
            'smallint'   => Type::SMALLINTEGER,
            'string'     => Type::STRING,
            'text'       => Type::TEXT,
            'time'       => Type::TIME,
            'timestamp'  => Type::DATETIME,
            'tinyint'    => Type::BOOLEAN,
            'tinytext'   => Type::TEXT,
            'varchar'    => Type::STRING,
            'year'       => Type::DATE,
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getPrimaryKeySQLDeclaration(PrimaryKey $primaryKey)
    {
        return 'CONSTRAINT PRIMARY KEY ('.implode(', ', $primaryKey->getColumnNames()).')';
    }

    /**
     * Gets the integer SQL declaration snippet.
     *
     * @param array $options The integer options.
     *
     * @return string The integer SQL declaration snippet.
     */
    protected function getIntegerSQLDeclarationSnippet(array $options = array())
    {
        $length = isset($options['length']) ? (int) $options['length'] : null;
        $unsigned = isset($options['unsigned']) && $options['unsigned'] ? ' UNSIGNED' : null;
        $autoIncrement = isset($options['auto_increment']) && $options['auto_increment'] ? ' AUTO_INCREMENT' : null;

        $sql = $unsigned.$autoIncrement;

        if ($length !== null) {
            $sql = '('.$length.')'.$sql;
        }

        return $sql;
    }
}
