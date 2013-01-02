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

use \PDO,
    Fridge\DBAL\Type\Type,
    Fridge\Tests\PHPUnitUtility;

/**
 * PostgreSQL fixture.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PostgreSQLFixture extends AbstractFixture
{
    /**
     * PostgreSQL fixture constructor.
     */
    public function __construct()
    {
        parent::__construct(PHPUnitUtility::PDO_PGSQL);
    }

    /**
     * {@inheritdoc}
     */
    public function getTableColumns($table)
    {
        $columns = parent::getTableColumns($table);

        foreach ($columns as $column) {
            if ($column->getType()->getName() !== Type::STRING) {
                $column->setLength(null);
            }

            $column->setUnsigned(null);
            $column->setAutoIncrement(null);
        }

        return $columns;
    }

    /**
     * {@inheritdoc}
     */
    protected function getCreateSchemaSQLQueries()
    {
        $queries = array();

        $queries[] = <<<EOT
CREATE TABLE tcolumns
(
    carray TEXT,
    cbiginteger BIGINT DEFAULT '1000000000',
    cblob BYTEA,
    cboolean BOOLEAN DEFAULT '1',
    cdatetime TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT '2012-01-01 12:12:12',
    cdate DATE DEFAULT '2012-01-01',
    cdecimal NUMERIC(5, 2) DEFAULT '1.1',
    cfloat DOUBLE PRECISION DEFAULT '1.1',
    cinteger INT DEFAULT '1',
    cobject TEXT,
    csmallinteger SMALLINT DEFAULT '1',
    cstring VARCHAR(20) DEFAULT 'foo',
    ctext TEXT,
    ctime TIME(0) WITHOUT TIME ZONE DEFAULT '12:12:12'
)
EOT;

        $queries[] = 'COMMENT ON COLUMN tcolumns.carray IS \'comment(FridgeType::ARRAY)\'';
        $queries[] = 'COMMENT ON COLUMN tcolumns.cbiginteger IS \'comment\'';
        $queries[] = 'COMMENT ON COLUMN tcolumns.cblob IS \'comment\'';
        $queries[] = 'COMMENT ON COLUMN tcolumns.cboolean IS \'comment\'';
        $queries[] = 'COMMENT ON COLUMN tcolumns.cdatetime IS \'comment\'';
        $queries[] = 'COMMENT ON COLUMN tcolumns.cdate IS \'comment\'';
        $queries[] = 'COMMENT ON COLUMN tcolumns.cdecimal IS \'comment\'';
        $queries[] = 'COMMENT ON COLUMN tcolumns.cfloat IS \'comment\'';
        $queries[] = 'COMMENT ON COLUMN tcolumns.cinteger IS \'comment\'';
        $queries[] = 'COMMENT ON COLUMN tcolumns.cobject IS \'(FridgeType::OBJECT)\'';
        $queries[] = 'COMMENT ON COLUMN tcolumns.csmallinteger IS \'comment\'';
        $queries[] = 'COMMENT ON COLUMN tcolumns.cstring IS \'comment\'';
        $queries[] = 'COMMENT ON COLUMN tcolumns.ctext IS \'comment\'';
        $queries[] = 'COMMENT ON COLUMN tcolumns.ctime IS \'comment\'';

        $queries[] = <<<EOT
CREATE TABLE tprimarykeylock
(
    c1 INT NOT NULL,
    c2 VARCHAR(20) NOT NULL,
    CONSTRAINT pk1 PRIMARY KEY(c1, c2)
)
EOT;

        $queries[] = <<<EOT
CREATE TABLE tprimarykeyunlock
(
    c1 INT NOT NULL,
    CONSTRAINT pk2 PRIMARY KEY(c1)
)
EOT;

        $queries[] = <<<EOT
CREATE TABLE tforeignkey
(
    c1 INT NOT NULL,
    c2 VARCHAR(20) NOT NULL
)
EOT;

        $queries[] = 'CREATE INDEX _fk1 ON tforeignkey(c1, c2)';
        $queries[] = 'ALTER TABLE tforeignkey'.
                     ' ADD CONSTRAINT fk1'.
                     ' FOREIGN KEY(c1, c2)'.
                     ' REFERENCES tprimarykeylock(c1, c2)'.
                     ' ON DELETE CASCADE'.
                     ' ON UPDATE CASCADE';

        $queries[] = <<<EOT
CREATE TABLE tindex
(
    c1 INT NOT NULL,
    c2 VARCHAR(20) NOT NULL,
    CONSTRAINT idx1 UNIQUE (c1, c2)
)
EOT;

        $queries[] = 'CREATE INDEX idx2 ON tindex(c1)';

        $queries[] = <<<EOT
CREATE TABLE tcheck
(
    c1 INT NOT NULL,
    CONSTRAINT ck1 CHECK (c1 > 0)
)
EOT;

        $queries[] = 'CREATE SEQUENCE s1 INCREMENT 1 MINVALUE 1';
        $queries[] = 'CREATE VIEW vcolumns AS SELECT cinteger FROM tcolumns';

        return $queries;
    }

    /**
     * {@inheritdoc}
     */
    protected function getConnection($database = true)
    {
        $dsnOptions = array();

        $haystack = array('host', 'port');

        if ($database) {
            $haystack[] = 'dbname';
        }

        foreach ($this->settings as $dsnKey => $dsnSetting) {
            if (in_array($dsnKey, $haystack)) {
                $dsnOptions[] = $dsnKey.'='.$dsnSetting;
            }
        }

        $username = $this->settings['username'];
        $password = $this->settings['password'];

        return new PDO('pgsql:'.implode(';', $dsnOptions), $username, $password);
    }
}
