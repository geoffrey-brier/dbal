<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\Tests\Fixture;

use \DateTime,
    \stdClass;

use Fridge\DBAL\Schema,
    Fridge\DBAL\Type\Type,
    Fridge\Tests\PHPUnitUtility;

/**
 * {@inheritdoc}
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractFixture implements FixtureInterface
{
    /** @var array */
    protected $settings;

    /**
     * Fixture constructor.
     *
     * @param type $prefix The PHPUnit prefix which describes this fixture.
     */
    public function __construct($prefix)
    {
        if (PHPUnitUtility::hasSettings($prefix)) {
            $this->settings = PHPUnitUtility::getSettings($prefix);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $this->createSchema();
        $this->createDatas();
    }

    /**
     * {@inheritdoc}
     */
    public function drop()
    {
        $this->dropDatabase();
    }

    /**
     * {@inheritdoc}
     */
    public function createDatabase()
    {
        if ($this->settings !== null) {
            $connection = $this->getConnection(false);
            $connection->exec($this->getCreateDatabaseSQLQuery());
            unset($connection);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function dropDatabase()
    {
        if ($this->settings !== null) {
            $connection = $this->getConnection(false);
            $connection->exec($this->getDropDatabaseSQLQuery());
            unset($connection);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createSchema()
    {
        $this->dropDatabase();
        $this->createDatabase();

        if ($this->settings !== null) {
            $this->executeQueries($this->getCreateSchemaSQLQueries());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function dropSchema()
    {
        if ($this->settings !== null) {
            $this->executeQueries($this->getDropSchemaSQLQueries());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createDatas()
    {
        $this->dropDatas();

        if ($this->settings !== null) {
            $this->executeQueries($this->getCreateDatasSQLQueries());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function dropDatas()
    {
        if ($this->settings !== null) {
            $this->executeQueries($this->getDropDatasSQLQueries());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * {@inheritdoc}
     */
    public function getDatabase()
    {
        if ($this->settings !== null) {
            return $this->settings['dbname'];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSchema()
    {
        $name = $this->getDatabase();
        $sequences = $this->getSequences();
        $views = $this->getViews();

        $tables = array();

        $tableNames = $this->getTableNames();
        sort($tableNames);

        foreach ($tableNames as $tableName) {
            $tables[] = $this->getTable($tableName);
        }

        return new Schema\Schema($name, $tables, $sequences, $views);
    }

    /**
     * {@inheritdoc}
     */
    public function getSequences()
    {
        return array(new Schema\Sequence('s1', 1, 1));
    }

    /**
     * {@inheritdoc}
     */
    public function getViews()
    {
        $sql = 'SELECT tcolumns.cinteger FROM tcolumns;';

        return array(new Schema\View('vcolumns', $sql));
    }

    /**
     * {@inheritdoc}
     */
    public function getTableNames()
    {
        return array('tcolumns', 'tprimarykeylock', 'tprimarykeyunlock', 'tforeignkey', 'tindex');
    }

    /**
     * {@inheritdoc}
     */
    public function getTable($name)
    {
        return new Schema\Table(
            $name,
            $this->getTableColumns($name),
            $this->getTablePrimaryKey($name),
            $this->getTableForeignKeys($name),
            $this->getTableIndexes($name)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTableColumns($table)
    {
        $columns = array();

        switch ($table) {
            case 'tcolumns':
                $columns = array(
                    new Schema\Column('carray', Type::getType(Type::TARRAY), array(
                        'comment' => 'comment',
                    )),
                    new Schema\Column('cbiginteger', Type::getType(Type::BIGINTEGER), array(
                        'length'   => 20,
                        'unsigned' => true,
                        'default'  => 1000000000,
                        'comment'  => 'comment',
                    )),
                    new Schema\Column('cboolean', Type::getType(Type::BOOLEAN), array(
                        'default' => true,
                        'comment' => 'comment',
                    )),
                    new Schema\Column('cdatetime', Type::getType(Type::DATETIME), array(
                        'default' => new DateTime('2012-01-01 12:12:12'),
                        'comment' => 'comment',
                    )),
                    new Schema\Column('cdate', Type::getType(Type::DATE), array(
                        'default' => new DateTime('2012-01-01'),
                        'comment' => 'comment',
                    )),
                    new Schema\Column('cdecimal', Type::getType(Type::DECIMAL), array(
                        'precision' => 5,
                        'scale'     => 2,
                        'default'   => 1.1,
                        'comment'   => 'comment',
                    )),
                    new Schema\Column('cfloat', Type::getType(Type::FLOAT), array(
                        'default' => 1.1,
                        'comment' => 'comment',
                    )),
                    new Schema\Column('cinteger', Type::getType(Type::INTEGER), array(
                        'length'   => 11,
                        'unsigned' => true,
                        'default'  => 1,
                        'comment'  => 'comment',
                    )),
                    new Schema\Column('cobject', Type::getType(Type::OBJECT)),
                    new Schema\Column('csmallinteger', Type::getType(Type::SMALLINTEGER), array(
                        'length'   => 6,
                        'unsigned' => true,
                        'default'  => 1,
                        'comment'  => 'comment',
                    )),
                    new Schema\Column('cstring', Type::getType(Type::STRING), array(
                        'length'  => 20,
                        'default' => 'foo',
                        'comment' => 'comment',
                    )),
                    new Schema\Column('ctext', Type::getType(Type::TEXT), array(
                        'comment' => 'comment',
                    )),
                    new Schema\Column('ctime', Type::getType(Type::TIME), array(
                        'default' => new DateTime('12:12:12'),
                        'comment' => 'comment',
                    )),
                );
                break;

            case 'tprimarykeylock':
                $columns = array(
                    new Schema\Column('c1', Type::getType(Type::INTEGER), array(
                        'length'         => 11,
                        'not_null'       => true,
                    )),
                    new Schema\Column('c2', Type::getType(Type::STRING), array(
                        'length'   => 20,
                        'not_null' => true,
                    )),
                );
                break;

            case 'tprimarykeyunlock':
                $columns = array(
                    new Schema\Column('c1', Type::getType(Type::INTEGER), array(
                        'length'         => 11,
                        'not_null'       => true,
                    )),
                );
                break;

            case 'tforeignkey':
                $columns = array(
                    new Schema\Column('c1', Type::getType(Type::INTEGER), array(
                        'length'   => 11,
                        'not_null' => true,
                    )),
                    new Schema\Column('c2', Type::getType(Type::STRING), array(
                        'length'   => 20,
                        'not_null' => true,
                    )),
                );
                break;

            case 'tindex':
                $columns = array(
                    new Schema\Column('c1', Type::getType(Type::INTEGER), array(
                        'length'   => 11,
                        'not_null' => true,
                    )),
                    new Schema\Column('c2', Type::getType(Type::STRING), array(
                        'length'   => 20,
                        'not_null' => true,
                    )),
                );
                break;
        }

        return $columns;
    }

    /**
     * {@inheritdoc}
     */
    public function getTablePrimaryKey($table)
    {
        $primaryKey = null;

        switch ($table) {
            case 'tprimarykeylock':
                $primaryKey = new Schema\PrimaryKey('pk1', array('c1', 'c2'));
                break;

            case 'tprimarykeyunlock':
                $primaryKey = new Schema\PrimaryKey('pk2', array('c1'));
                break;
        }

        return $primaryKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getTableForeignKeys($table)
    {
        $foreignKeys = array();

        switch ($table) {
            case 'tforeignkey':
                $foreignKeys = array(
                    new Schema\ForeignKey('fk1', array('c1', 'c2'), 'tprimarykeylock', array('c1', 'c2')),
                );
                break;
        }

        return $foreignKeys;
    }

    /**
     * {@inheritdoc}
     */
    public function getTableIndexes($table)
    {
        $indexes = array();

        switch ($table) {
            case 'tprimarykeylock':
                $indexes = array(new Schema\Index('pk1', array('c1', 'c2'), true));
                break;

            case 'tprimarykeyunlock':
                $indexes = array(new Schema\Index('pk2', array('c1'), true));
                break;

            case 'tforeignkey':
                $indexes = array(
                    new Schema\Index('_fk1', array('c1', 'c2')),
                );
                break;

            case 'tindex':
                $indexes = array(
                    new Schema\Index('idx1', array('c1', 'c2'), true),
                    new Schema\Index('idx2', array('c1')),
                );
                break;
        }

        return $indexes;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery()
    {
        return 'SELECT * FROM tcolumns';
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryWithNamedParameters()
    {
        return 'SELECT * '.
               'FROM tcolumns '.
               'WHERE carray = :carray '.
               'AND cbiginteger = :cbiginteger '.
               'AND cboolean = :cboolean '.
               'AND cdatetime = :cdatetime '.
               'AND cdate = :cdate '.
               'AND cdecimal = :cdecimal '.
               'AND cfloat = :cfloat '.
               'AND cinteger = :cinteger '.
               'AND cobject = :cobject '.
               'AND csmallinteger = :csmallinteger '.
               'AND cstring = :cstring '.
               'AND ctext = :ctext '.
               'AND ctime = :ctime';
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryWithPositionalParameters()
    {
        return 'SELECT * '.
               'FROM tcolumns '.
               'WHERE carray = ? '.
               'AND cbiginteger = ? '.
               'AND cboolean = ? '.
               'AND cdatetime = ? '.
               'AND cdate = ? '.
               'AND cdecimal = ? '.
               'AND cfloat = ? '.
               'AND cinteger = ? '.
               'AND cobject = ? '.
               'AND csmallinteger = ? '.
               'AND cstring = ? '.
               'AND ctext = ? '.
               'AND ctime = ?';
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdateQuery()
    {
        return 'TRUNCATE tcolumns';
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdateQueryWithNamedParameters()
    {
        $columns = array_keys($this->getNamedQueryParameters());
        $values = array_map(function ($placeholder) {
            return sprintf(':%s', $placeholder);
        }, $columns);

        return 'INSERT INTO tcolumns '.
               '('.implode(', ', $columns).') '.
               'VALUES '.
               '('.implode(', ', $values).')';
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdateQueryWithPositionalParameters()
    {
        $columns = array_keys($this->getNamedQueryParameters());
        $values = array_pad(array(), count($this->getNamedQueryParameters()), '?');

        return 'INSERT INTO tcolumns '.
               '('.implode(', ', $columns).') '.
               'VALUES '.
               '('.implode(', ', $values).')';
    }

    /**
     * {@inheritdoc}
     */
    public function getNamedQueryParameters()
    {
        return array(
            'carray'        => 'a:1:{s:3:"foo";s:3:"bar";}',
            'cbiginteger'   => 1000000000,
            'cboolean'      => 1,
            'cdatetime'     => '2000-01-01 12:12:12',
            'cdate'         => '2000-01-01',
            'cdecimal'      => 1.1,
            'cfloat'        => 1.1,
            'cinteger'      => 1,
            'cobject'       => 'O:8:"stdClass":0:{}',
            'csmallinteger' => 1,
            'cstring'       => 'foo',
            'ctext'         => 'foo',
            'ctime'         => '12:12:12',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPositionalQueryParameters()
    {
        return array_values($this->getNamedQueryParameters());
    }

    /**
     * {@inheritdoc}
     */
    public function getNamedTypedQueryParameters()
    {
        return array(
            'carray'        => array('foo' => 'bar'),
            'cbiginteger'   => 1000000000,
            'cboolean'      => true,
            'cdatetime'     => new DateTime('2000-01-01 12:12:12'),
            'cdate'         => new DateTime('2000-01-01'),
            'cdecimal'      => 1.1,
            'cfloat'        => 1.1,
            'cinteger'      => 1,
            'cobject'       => new stdClass(),
            'csmallinteger' => 1,
            'cstring'       => 'foo',
            'ctext'         => 'foo',
            'ctime'         => new DateTime('12:12:12'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPositionalTypedQueryParameters()
    {
        return array_values($this->getNamedTypedQueryParameters());
    }

    /**
     * {@inheritdoc}
     */
    public function getNamedQueryTypes()
    {
        return array(
            'carray'        => Type::TARRAY,
            'cbiginteger'   => Type::BIGINTEGER,
            'cboolean'      => Type::BOOLEAN,
            'cdatetime'     => Type::DATETIME,
            'cdate'         => Type::DATE,
            'cdecimal'      => Type::DECIMAL,
            'cfloat'        => Type::FLOAT,
            'cinteger'      => Type::INTEGER,
            'cobject'       => Type::OBJECT,
            'csmallinteger' => Type::SMALLINTEGER,
            'cstring'       => Type::STRING,
            'ctext'         => Type::TEXT,
            'ctime'         => Type::TIME,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPositionalQueryTypes()
    {
        return array_values($this->getNamedQueryTypes());
    }

    /**
     * {@inheritdoc}
     */
    public function getPartialNamedQueryTypes()
    {
        return array(
            'carray'    => Type::TARRAY,
            'cdatetime' => Type::DATETIME,
            'cdate'     => Type::DATE,
            'cobject'   => Type::OBJECT,
            'ctime'     => Type::TIME,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPartialPositionalQueryTypes()
    {
        return array(
            0  => Type::TARRAY,
            3  => Type::DATETIME,
            4  => Type::DATE,
            8  => Type::OBJECT,
            12 => Type::TIME,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryResult()
    {
        return $this->getNamedQueryParameters();
    }

    /**
     * Gets the connection used for creating/dropping the fixture.
     *
     * @param boolean $database TRUE if the connection uses an explicit database else FALSE.
     *
     * @return mixed The connection.
     */
    abstract protected function getConnection($database = true);

    /**
     * Gets the SQL queries used to create the database schema.
     *
     * @return array The SQL queries used to create the database schema.
     */
    abstract protected function getCreateSchemaSQLQueries();

    /**
     * Gets the SQL query used to create the database.
     *
     * @return string The SQL query used to create the database.
     */
    protected function getCreateDatabaseSQLQuery()
    {
        return 'CREATE DATABASE '.$this->settings['dbname'];
    }

    /**
     * Gets the SQL query used to drop the database.
     *
     * @return strinh The SQL query used to drop the database.
     */
    protected function getDropDatabaseSQLQuery()
    {
        return 'DROP DATABASE IF EXISTS '.$this->settings['dbname'];
    }

    /**
     * Gets the SQL queries used to drop the database schema.
     *
     * @return array The SQL queries used to drop the database schema.
     */
    protected function getDropSchemaSQLQueries()
    {
        return array(
            'DROP SEQUENCE IF EXISTS s1',
            'DROP VIEW IF EXISTS vcolumns',
            'DROP TABLE IF EXISTS tforeignkey',
            'DROP TABLE IF EXISTS tprimarykeyunlock',
            'DROP TABLE IF EXISTS tprimarykeylock',
            'DROP TABLE IF EXISTS tindex',
            'DROP TABLE IF EXISTS tcompound',
            'DROP TABLE IF EXISTS tcolumns',
        );
    }

    /**
     * Gets the SQL queries used to create the database datas.
     *
     * @return array The SQL queries used to create the database datas.
     */
    protected function getCreateDatasSQLQueries()
    {
        $queries = array();

        $queries[] = <<<EOT
INSERT INTO tcolumns
(
    carray,
    cbiginteger,
    cboolean,
    cdatetime,
    cdate,
    cdecimal,
    cfloat,
    cinteger,
    cobject,
    csmallinteger,
    cstring,
    ctext,
    ctime
)
VALUES
(
    'a:1:{s:3:"foo";s:3:"bar";}',
    1000000000,
    true,
    '2000-01-01 12:12:12',
    '2000-01-01',
    1.1,
    1.1,
    1,
    'O:8:"stdClass":0:{}',
    1,
    'foo',
    'foo',
    '12:12:12'
)
EOT;

        return $queries;
    }

    /**
     * Gets the SQL queries used to drop the database datas.
     *
     * @return array The SQL queries used to drop the database datas.
     */
    protected function getDropDatasSQLQueries()
    {
        return array('TRUNCATE tcolumns');
    }

    /**
     * Executes the queries on the fixture connection.
     *
     * @param array $queries The queries to execute.
     */
    protected function executeQueries(array $queries)
    {
        $connection = $this->getConnection();

        foreach ($queries as $query) {
            $connection->exec($query);
        }

        unset($connection);
    }
}
