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

use \Exception;

use Fridge\DBAL\Connection\ConnectionInterface,
    Fridge\DBAL\Schema,
    Fridge\DBAL\Type\Type;

/**
 * {@inheritdoc}
 *
 * All schema managers must extend this class.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractSchemaManager implements SchemaManagerInterface
{
    /** @var \Fridge\DBAL\Connection\ConnectionInterface */
    protected $connection;

    /**
     * Creates a schema manager.
     *
     * @param \Fridge\DBAL\Connection\ConnectionInterface $connection The connection used.
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getDatabases()
    {
        $query = $this->getConnection()->getPlatform()->getSelectDatabasesSQLQuery();
        $databases = $this->connection->fetchAll($query);

        return $this->getGenericDatabases($databases);
    }

    /**
     * Gets the current database name.
     *
     * @return string The current database name
     */
    public function getDatabase()
    {
        $parameters = $this->getConnection()->getParameters();

        if (isset($parameters['dbname'])) {
            return $parameters['dbname'];
        }

        $query = $this->getConnection()->getPlatform()->getSelectDatabaseSQLQuery();

        return $this->getConnection()->fetchColumn($query);
    }

    /**
     * {@inheritdoc}
     */
    public function getSchema($database = null)
    {
        if ($database === null) {
            $database = $this->getDatabase();
        }

        $tables = $this->getTables($database);

        $sequences = array();
        if ($this->getConnection()->getPlatform()->supportSequence()) {
            $sequences = $this->getSequences($database);
        }

        $views = array();
        if ($this->getConnection()->getPlatform()->supportView()) {
            $views = $this->getViews($database);
        }

        return new Schema\Schema($database, $tables, $sequences, $views);
    }

    /**
     * {@inheritdoc}
     */
    public function getSequences($database = null)
    {
        if ($database === null) {
            $database = $this->getDatabase();
        }

        $query = $this->getConnection()->getPlatform()->getSelectSequencesSQLQuery($database);
        $sequences = $this->getConnection()->fetchAll($query);

        return $this->getGenericSequences($sequences);
    }

    /**
     * {@inheritdoc}
     */
    public function getViews($database = null)
    {
        if ($database === null) {
            $database = $this->getDatabase();
        }

        $query = $this->getConnection()->getPlatform()->getSelectViewsSQLQuery($database);
        $views = $this->getConnection()->fetchAll($query);

        return $this->getGenericViews($views);
    }

    /**
     * {@inheritdoc}
     */
    public function getTableNames($database = null)
    {
        if ($database === null) {
            $database = $this->getDatabase();
        }

        $query = $this->getConnection()->getPlatform()->getSelectTableNamesSQLQuery($database);
        $tableNames = $this->getConnection()->fetchAll($query);

        return $this->getGenericTableNames($tableNames);
    }

    /**
     * {@inheritdoc}
     */
    public function getTables($database = null)
    {
        if ($database === null) {
            $database = $this->getDatabase();
        }

        $tables = array();

        foreach ($this->getTableNames($database) as $name) {
            $tables[] = $this->getTable($name, $database);
        }

        return $tables;
    }

    /**
     * {@inheritdoc}
     */
    public function getTable($table, $database = null)
    {
        if ($database === null) {
            $database = $this->getDatabase();
        }

        $columns = $this->getTableColumns($table, $database);

        $primaryKey = null;
        if ($this->getConnection()->getPlatform()->supportPrimaryKey()) {
            $primaryKey = $this->getTablePrimaryKey($table, $database);
        }

        $foreignKeys = array();
        if ($this->getConnection()->getPlatform()->supportForeignKey()) {
            $foreignKeys = $this->getTableForeignKeys($table, $database);
        }

        $indexes = array();
        if ($this->getConnection()->getPlatform()->supportIndex()) {
            $indexes = $this->getTableIndexes($table, $database);
        }

        $checks = array();
        if ($this->getConnection()->getPlatform()->supportCheck()) {
            $checks = $this->getTableChecks($table, $database);
        }

        return new Schema\Table($table, $columns, $primaryKey, $foreignKeys, $indexes, $checks);
    }

    /**
     * {@inheritdoc}
     */
    public function getTableColumns($table, $database = null)
    {
        if ($database === null) {
            $database = $this->getDatabase();
        }

        $query = $this->getConnection()->getPlatform()->getSelectTableColumnsSQLQuery($table, $database);
        $columns = $this->getConnection()->fetchAll($query);

        return $this->getGenericTableColumns($columns);
    }

    /**
     * {@inheritdoc}
     */
    public function getTablePrimaryKey($table, $database = null)
    {
        if ($database === null) {
            $database = $this->getDatabase();
        }

        $query = $this->getConnection()->getPlatform()->getSelectTablePrimaryKeySQLQuery($table, $database);
        $primaryKey = $this->getConnection()->fetchAll($query);

        if ($primaryKey) {
            return $this->getGenericTablePrimaryKey($primaryKey);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTableForeignKeys($table, $database = null)
    {
        if ($database === null) {
            $database = $this->getDatabase();
        }

        $query = $this->getConnection()->getPlatform()->getSelectTableForeignKeysSQLQuery($table, $database);
        $foreignKeys = $this->getConnection()->fetchAll($query);

        return $this->getGenericTableForeignKeys($foreignKeys);
    }

    /**
     * {@inheritdoc}
     */
    public function getTableIndexes($table, $database = null)
    {
        if ($database === null) {
            $database = $this->getDatabase();
        }

        $query = $this->getConnection()->getPlatform()->getSelectTableIndexesSQLQuery($table, $database);
        $indexes = $this->getConnection()->fetchAll($query);

        return $this->getGenericTableIndexes($indexes);
    }

    /**
     * {@inheritdoc}
     */
    public function getTableChecks($table, $database = null)
    {
        if ($database === null) {
            $database = $this->getDatabase();
        }

        $query = $this->getConnection()->getPlatform()->getSelectTableCheckSQLQuery($table, $database);
        $checks = $this->getConnection()->fetchAll($query);

        return $this->getGenericTableChecks($checks);
    }

    /**
     * {@inheritdoc}
     */
    public function createDatabase($database)
    {
        $currentDatabase = $this->getConnection()->getDatabase();

        $this->getConnection()->setDatabase(null);

        $queries = $this->getConnection()->getPlatform()->getCreateDatabaseSQLQueries($database);
        $this->executeUpdates($queries);

        $this->getConnection()->setDatabase($currentDatabase);
    }

    /**
     * {@inheritdoc}
     */
    public function createSchema(Schema\Schema $schema)
    {
        $this->createDatabase($schema->getName());
        $this->createTables($schema->getTables());

        foreach ($schema->getSequences() as $sequence) {
            $this->createSequence($sequence);
        }

        foreach ($schema->getViews() as $view) {
            $this->createView($view);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createSequence(Schema\Sequence $sequence)
    {
        $queries = $this->getConnection()->getPlatform()->getCreateSequenceSQLQueries($sequence);
        $this->executeUpdates($queries);
    }

    /**
     * {@inheritdoc}
     */
    public function createView(Schema\View $view)
    {
        $queries = $this->getConnection()->getPlatform()->getCreateViewSQLQueries($view);
        $this->executeUpdates($queries);
    }

    /**
     * {@inheritdoc}
     */
    public function createTables(array $tables)
    {
        $sqlCollector = new SQLCollector\CreateTableSQLCollector($this->getConnection()->getPlatform());

        foreach ($tables as $table) {
            $sqlCollector->collect($table);
        }

        $this->executeUpdates($sqlCollector->getQueries());
    }

    /**
     * {@inheritdoc}
     */
    public function createTable(Schema\Table $table)
    {
        $queries = $this->getConnection()->getPlatform()->getCreateTableSQLQueries($table);
        $this->executeUpdates($queries);
    }

    /**
     * {@inheritdoc}
     */
    public function createColumn(Schema\Column $column, $table)
    {
        $queries = $this->getConnection()->getPlatform()->getCreateColumnSQLQueries($column, $table);
        $this->executeUpdates($queries);
    }

    /**
     * {@inheritdoc}
     */
    public function createConstraint(Schema\ConstraintInterface $constraint, $table)
    {
        $queries = $this->getConnection()->getPlatform()->getCreateConstraintSQLQueries($constraint, $table);
        $this->executeUpdates($queries);
    }

    /**
     * {@inheritdoc}
     */
    public function createPrimaryKey(Schema\PrimaryKey $primaryKey, $table)
    {
        $queries = $this->getConnection()->getPlatform()->getCreatePrimaryKeySQLQueries($primaryKey, $table);
        $this->executeUpdates($queries);
    }

    /**
     * {@inheritdoc}
     */
    public function createForeignKey(Schema\ForeignKey $foreignKey, $table)
    {
        $queries = $this->getConnection()->getPlatform()->getCreateForeignKeySQLQueries($foreignKey, $table);
        $this->executeUpdates($queries);
    }

    /**
     * {@inheritdoc}
     */
    public function createIndex(Schema\Index $index, $table)
    {
        $queries = $this->getConnection()->getPlatform()->getCreateIndexSQLQueries($index, $table);
        $this->executeUpdates($queries);
    }

    /**
     * {@inheritdoc}
     */
    public function createCheck(Schema\Check $check, $table)
    {
        $queries = $this->getConnection()->getPlatform()->getCreateCheckSQLQueries($check, $table);
        $this->executeUpdates($queries);
    }

    /**
     * {@inheritdoc}
     */
    public function alterSchema(Schema\Diff\SchemaDiff $schemaDiff)
    {
        $sqlCollector = new SQLCollector\AlterSchemaSQLCollector($this->getConnection()->getPlatform());
        $sqlCollector->collect($schemaDiff);

        $this->executeUpdates($sqlCollector->getQueries());
    }

    /**
     * {@inheritdoc}
     */
    public function alterTables(array $tableDiffs)
    {
        $sqlCollector = new SQLCollector\AlterTableSQLCollector($this->getConnection()->getPlatform());

        foreach ($tableDiffs as $tableDiff) {
            $sqlCollector->collect($tableDiff);
        }

        $this->executeUpdates($sqlCollector->getQueries());
    }

    /**
     * {@inheritdoc}
     */
    public function alterTable(Schema\Diff\TableDiff $tableDiff)
    {
        $this->alterTables(array($tableDiff));
    }

    /**
     * {@inheritdoc}
     */
    public function alterColumn(Schema\Diff\ColumnDiff $columnDiff, $table)
    {
        $queries = $this->getConnection()->getPlatform()->getAlterColumnSQLQueries($columnDiff, $table);
        $this->executeUpdates($queries);
    }

    /**
     * {@inheritdoc}
     */
    public function dropDatabase($database)
    {
        $currentDatabase = $this->getConnection()->getDatabase();

        $this->getConnection()->setDatabase(null);

        $queries = $this->getConnection()->getPlatform()->getDropDatabaseSQLQueries($database);
        $this->executeUpdates($queries);

        $this->getConnection()->setDatabase($currentDatabase);
    }

    /**
     * {@inheritdoc}
     */
    public function dropSchema(Schema\Schema $schema)
    {
        $this->dropDatabase($schema->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function dropSequence(Schema\Sequence $sequence)
    {
        $query = $this->getConnection()->getPlatform()->getDropSequenceSQLQuery($sequence);
        $this->getConnection()->executeUpdate($query);
    }

    /**
     * {@inheritdoc}
     */
    public function dropView(Schema\View $view)
    {
        $query = $this->getConnection()->getPlatform()->getDropViewSQLQuery($view);
        $this->getConnection()->executeUpdate($query);
    }

    /**
     * {@inheritdoc}
     */
    public function dropTables(array $tables)
    {
        $sqlCollector = new SQLCollector\DropTableSQLCollector($this->getConnection()->getPlatform());

        foreach ($tables as $table) {
            $sqlCollector->collect($table);
        }

        $this->executeUpdates($sqlCollector->getQueries());
    }

    /**
     * {@inheritdoc}
     */
    public function dropTable(Schema\Table $table)
    {
        $query = $this->getConnection()->getPlatform()->getDropTableSQLQuery($table);
        $this->getConnection()->executeUpdate($query);
    }

    /**
     * {@inheritdoc}
     */
    public function dropColumn(Schema\Column $column, $table)
    {
        $query = $this->getConnection()->getPlatform()->getDropColumnSQLQuery($column, $table);
        $this->getConnection()->executeUpdate($query);
    }

    /**
     * {@inheritdoc}
     */
    public function dropConstraint(Schema\ConstraintInterface $constraint, $table)
    {
        $query = $this->getConnection()->getPlatform()->getDropConstraintSQLQuery($constraint, $table);
        $this->getConnection()->executeUpdate($query);
    }

    /**
     * {@inheritdoc}
     */
    public function dropPrimaryKey(Schema\PrimaryKey $primaryKey, $table)
    {
        $query = $this->getConnection()->getPlatform()->getDropPrimaryKeySQLQuery($primaryKey, $table);
        $this->getConnection()->executeUpdate($query);
    }

    /**
     * {@inheritdoc}
     */
    public function dropForeignKey(Schema\ForeignKey $foreignKey, $table)
    {
        $query = $this->getConnection()->getPlatform()->getDropForeignKeySQLQuery($foreignKey, $table);
        $this->getConnection()->executeUpdate($query);
    }

    /**
     * {@inheritdoc}
     */
    public function dropIndex(Schema\Index $index, $table)
    {
        $query = $this->getConnection()->getPlatform()->getDropIndexSQLQuery($index, $table);
        $this->getConnection()->executeUpdate($query);
    }

    /**
     * {@inheritdoc}
     */
    public function dropCheck(Schema\Check $check, $table)
    {
        $query = $this->getConnection()->getPlatform()->getDropCheckSQLQuery($check, $table);
        $this->getConnection()->executeUpdate($query);
    }

    /**
     * {@inheritdoc}
     */
    public function dropAndCreateDatabase($database)
    {
        $this->tryMethod('dropDatabase', array($database));
        $this->createDatabase($database);
    }

    /**
     * {@inheritdoc}
     */
    public function dropAndCreateSchema(Schema\Schema $schema)
    {
        $this->tryMethod('dropSchema', array($schema));
        $this->createSchema($schema);
    }

    /**
     * {@inheritdoc}
     */
    public function dropAndCreateSequence(Schema\Sequence $sequence)
    {
        $this->tryMethod('dropSequence', array($sequence));
        $this->createSequence($sequence);
    }

    /**
     * {@inheritdoc}
     */
    public function dropAndCreateView(Schema\View $view)
    {
        $this->tryMethod('dropView', array($view));
        $this->createView($view);
    }

    /**
     * {@inheritdoc}
     */
    public function dropAndCreateTables(array $tables)
    {
        $this->tryMethod('dropTables', array($tables));
        $this->createTables($tables);
    }

    /**
     * {@inheritdoc}
     */
    public function dropAndCreateTable(Schema\Table $table)
    {
        $this->tryMethod('dropTable', array($table));
        $this->createTable($table);
    }

    /**
     * {@inheritdoc}
     */
    public function dropAndCreateColumn(Schema\Column $column, $table)
    {
        $this->tryMethod('dropColumn', array($column, $table));
        $this->createColumn($column, $table);
    }

    /**
     * {@inheritdoc}
     */
    public function dropAndCreateConstraint(Schema\ConstraintInterface $constraint, $table)
    {
        $this->tryMethod('dropConstraint', array($constraint, $table));
        $this->createConstraint($constraint, $table);
    }

    /**
     * {@inheritdoc}
     */
    public function dropAndCreatePrimaryKey(Schema\PrimaryKey $primaryKey, $table)
    {
        $this->tryMethod('dropPrimaryKey', array($primaryKey, $table));
        $this->createPrimaryKey($primaryKey, $table);
    }

    /**
     * {@inheritdoc}
     */
    public function dropAndCreateForeignKey(Schema\ForeignKey $foreignKey, $table)
    {
        $this->tryMethod('dropForeignKey', array($foreignKey, $table));
        $this->createForeignKey($foreignKey, $table);
    }

    /**
     * {@inheritdoc}
     */
    public function dropAndCreateIndex(Schema\Index $index, $table)
    {
        $this->tryMethod('dropIndex', array($index, $table));
        $this->createIndex($index, $table);
    }

    /**
     * {@inheritdoc}
     */
    public function dropAndCreateCheck(Schema\Check $check, $table)
    {
        $this->tryMethod('dropCheck', array($check, $table));
        $this->createCheck($check, $table);
    }

    /**
     * Gets the generic databases.
     *
     * @param array $databases The databases.
     *
     * @return array The generic databases.
     */
    protected function getGenericDatabases(array $databases)
    {
        $genericDatabases = array();

        foreach ($databases as $database) {
            $genericDatabases[] = $this->getGenericDatabase($database);
        }

        return $genericDatabases;
    }

    /**
     * Gets the generic database.
     *
     * The $database parameter must contain:
     *  - database
     *
     * @param mixed $database The database.
     *
     * @return string The generic database.
     */
    protected function getGenericDatabase($database)
    {
        return $database['database'];
    }

    /**
     * Gets the generic sequences.
     *
     * @param array $sequences The sequences.
     *
     * @return array The generic sequences.
     */
    protected function getGenericSequences(array $sequences)
    {
        $genericSequences = array();

        foreach ($sequences as $sequence) {
            $genericSequences[] = $this->getGenericSequence($sequence);
        }

        return $genericSequences;
    }

    /**
     * Gets the generic sequence.
     *
     * The $sequence parameter must contain:
     *  - name
     *  - initial_value
     *  - increment_size
     *
     * @param mixed $sequence The sequence.
     *
     * @return \Fridge\DBAL\Schema\Sequence The generic sequence.
     */
    protected function getGenericSequence($sequence)
    {
        $name = $sequence['name'];
        $initialValue = (int) $sequence['initial_value'];
        $incrementSize = (int) $sequence['increment_size'];

        return new Schema\Sequence($name, $initialValue, $incrementSize);
    }

    /**
     * Gets the generic views
     *
     * @param array $views The views.
     *
     * @return array The generic views.
     */
    protected function getGenericViews(array $views)
    {
        $genericViews = array();

        foreach ($views as $view) {
            $genericViews[] = $this->getGenericView($view);
        }

        return $genericViews;
    }

    /**
     * Gets the generic view.
     *
     * The $view parameter must contain:
     *  - name
     *  - sql
     *
     * @param mixed $view The view.
     *
     * @return \Fridge\DBAL\Schema\View The generic view.
     */
    protected function getGenericView($view)
    {
        return new Schema\View($view['name'], $view['sql']);
    }

    /**
     * Gets the generic table names.
     *
     * @param array $tableNames The table names.
     *
     * @return array The generic table names.
     */
    protected function getGenericTableNames(array $tableNames)
    {
        $genericTableNames = array();

        foreach ($tableNames as $tableName) {
            $genericTableNames[] = $this->getGenericTableName($tableName);
        }

        return $genericTableNames;
    }

    /**
     * Gets the generic table name.
     *
     * The $tableName parameter must contain:
     *  - name
     *
     * @param mixed $tableName The table name.
     *
     * @return string The generic table name.
     */
    protected function getGenericTableName($tableName)
    {
        return $tableName['name'];
    }

    /**
     * Gets the generic table columns
     *
     * @param array $columns The columns.
     *
     * @return array The generic columns.
     */
    protected function getGenericTableColumns(array $columns)
    {
        $genericColumns = array();

        foreach ($columns as $column) {
            $genericColumns[] = $this->getGenericTableColumn($column);
        }

        return $genericColumns;
    }

    /**
     * Gets the generic table column.
     *
     * The $column parameter must contain:
     *  - name
     *  - type
     *  - length
     *  - precision
     *  - scale
     *  - unisgned
     *  - fixed
     *  - not_null
     *  - default
     *  - auto_increment
     *  - comment
     *
     * @param array $column The column.
     *
     * @return \Fridge\DBAL\Schema\Column The generic column.
     */
    protected function getGenericTableColumn(array $column)
    {
        $name = $column['name'];

        list($column['comment'], $typeName) = $this->extractTypeFromComment($column['comment']);

        if ($typeName === null) {
            $typeName = $this->getConnection()->getPlatform()->getMappedType($column['type']);
        }

        $type = Type::getType($typeName);

        if ($column['default'] !== null) {
            $column['default'] = $type->convertToPHPValue($column['default'], $this->getConnection()->getPlatform());
        }

        $options = array(
            'length'         => ($column['length'] !== null) ? (int) $column['length'] : null,
            'precision'      => ($column['precision'] !== null) ? (int) $column['precision'] : null,
            'scale'          => ($column['scale'] !== null) ? (int) $column['scale'] : null,
            'unsigned'       => ($column['unsigned'] !== null) ? (bool) $column['unsigned'] : null,
            'fixed'          => ($column['fixed'] !== null) ? (bool) $column['fixed'] : null,
            'not_null'       => (bool) $column['not_null'],
            'default'        => $column['default'],
            'auto_increment' => ($column['auto_increment'] !== null) ? (bool) $column['auto_increment'] : null,
            'comment'        => $column['comment'],
        );

        return new Schema\Column($name, $type, $options);
    }

    /**
     * Gets the generic table primary key.
     *
     * The $primaryKey parameter must contain:
     *  - name
     *  - column_name
     *
     * @param array $primaryKey The primary key.
     *
     * @return \Fridge\DBAL\Schema\PrimaryKey|null The generic primary key.
     */
    protected function getGenericTablePrimaryKey(array $primaryKey)
    {
        $genericPrimaryKey = new Schema\PrimaryKey($primaryKey[0]['name']);

        foreach ($primaryKey as $primaryKeyColumn) {
            $genericPrimaryKey->addColumnName($primaryKeyColumn['column_name']);
        }

        return $genericPrimaryKey;
    }

    /**
     * Gets the generic table foreign keys.
     *
     * The $foreignKeys parameter contains:
     *  - name
     *  - local_column_name
     *  - foreign_table_name
     *  - foreign_column_name
     *  - on_delete
     *  - on_update
     *
     * @param array $foreignKeys The foreign keys.
     *
     * @return array The generic foreign keys.
     */
    protected function getGenericTableForeignKeys(array $foreignKeys)
    {
        $genericForeignKeys = array();

        foreach ($foreignKeys as $foreignKey) {
            $name = $foreignKey['name'];

            if (!isset($genericForeignKeys[$name])) {
                $genericForeignKeys[$name] = new Schema\ForeignKey(
                    $foreignKey['name'],
                    array($foreignKey['local_column_name']),
                    $foreignKey['foreign_table_name'],
                    array($foreignKey['foreign_column_name']),
                    $foreignKey['on_delete'],
                    $foreignKey['on_update']
                );
            } else {
                $genericForeignKeys[$name]->addLocalColumnName($foreignKey['local_column_name']);
                $genericForeignKeys[$name]->addForeignColumnName($foreignKey['foreign_column_name']);
            }
        }

        return array_values($genericForeignKeys);
    }

    /**
     * Gets the generic table indexes.
     *
     * The $indexes parameter contains:
     *  - name
     *  - column_name
     *  - unique
     *
     * @param array $indexes The indexes.
     *
     * @return array The generic indexes.
     */
    protected function getGenericTableIndexes(array $indexes)
    {
        $genericIndexes = array();

        foreach ($indexes as $index) {
            $name = $index['name'];

            if (!isset($genericIndexes[$name])) {
                $genericIndexes[$name] = new Schema\Index($name, array($index['column_name']), (bool) $index['unique']);
            } else {
                $genericIndexes[$name]->addColumnName($index['column_name']);
            }
        }

        return array_values($genericIndexes);
    }

    /**
     * Gets the generic table checks.
     *
     * The $checks parameter contains:
     *  - name
     *  - definition
     *
     * @param array $checks The checks.
     *
     * @return array The generic checks.
     */
    protected function getGenericTableChecks(array $checks)
    {
        $genericChecks = array();

        foreach ($checks as $check) {
            $genericChecks[] = new Schema\Check($check['name'], $check['definition']);
        }

        return $genericChecks;
    }

    /**
     * Extracts the type from the comment if it exists.
     *
     * @param string $comment The comment.
     *
     * @return array 0 => The extracted comment, 1 => The extracted type.
     */
    protected function extractTypeFromComment($comment)
    {
        if (preg_match('/^(.*)\(FridgeType::([a-zA-Z0-9]+)\)$/', $comment, $matches)) {
            if (empty($matches[1])) {
                $matches[1] = null;
            }

            return array($matches[1], strtolower($matches[2]));
        }

        return array($comment, null);
    }

    /**
     * Tries to execute a method.
     *
     * @param string $method    The method name.
     * @param array  $arguments The method arguments.
     *
     * @return boolean TRUE if the method has been executed successfully else FALSE.
     */
    protected function tryMethod($method, array $arguments = array())
    {
        try {
            call_user_func_array(array($this, $method), $arguments);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Executes multipes updates on the connection.
     *
     * @param array $queries The queries to execute.
     */
    protected function executeUpdates(array $queries)
    {
        foreach ($queries as $query) {
            $this->getConnection()->executeUpdate($query);
        }
    }
}
