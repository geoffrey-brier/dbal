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

use Fridge\DBAL\Schema,
    Fridge\Tests\PHPUnitUtility;

/**
 * MySQL fixture.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class MySQLFixture extends AbstractPDOFixture
{
    /**
     * MySQL fixture constructor.
     */
    public function __construct()
    {
        parent::__construct(PHPUnitUtility::PDO_MYSQL);
    }

    /**
     * {@inheritdoc}
     */
    public function getSequences()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getViews()
    {
        $sql = 'select `dbal_test`.`tcolumns`.`cinteger` AS `cinteger` from `dbal_test`.`tcolumns`';

        return array(new Schema\View('vcolumns', $sql));
    }

    /**
     * {@inheritdoc}
     */
    public function getTablePrimaryKey($table)
    {
        $primaryKey = parent::getTablePrimaryKey($table);

        if ($primaryKey !== null) {
            $primaryKey->setName('PRIMARY');
        }

        return $primaryKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getTableIndexes($table)
    {
        $indexes = parent::getTableIndexes($table);

        foreach ($indexes as $index) {
            if (substr($index->getName(), 0, 2) === 'pk') {
                $index->setName('PRIMARY');
            }
        }

        return $indexes;
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
    carray LONGTEXT COMMENT 'comment(FridgeType::ARRAY)',
    cbiginteger BIGINT(20) UNSIGNED DEFAULT '1000000000' COMMENT 'comment',
    cboolean TINYINT(1) DEFAULT '1' COMMENT 'comment',
    cdatetime DATETIME DEFAULT '2012-01-01 12:12:12' COMMENT 'comment',
    cdate DATE DEFAULT '2012-01-01' COMMENT 'comment',
    cdecimal NUMERIC(5, 2) DEFAULT '1.1' COMMENT 'comment',
    cfloat DOUBLE PRECISION DEFAULT '1.1' COMMENT 'comment',
    cinteger INT(11) UNSIGNED DEFAULT '1' COMMENT 'comment',
    cobject LONGTEXT COMMENT '(FridgeType::OBJECT)',
    csmallinteger SMALLINT(6) UNSIGNED DEFAULT '1' COMMENT 'comment',
    cstring VARCHAR(20) DEFAULT 'foo' COMMENT 'comment',
    ctext LONGTEXT COMMENT 'comment',
    ctime TIME DEFAULT '12:12:12' COMMENT 'comment'
) ENGINE = InnoDB
EOT;

        $queries[] = <<<EOT
CREATE TABLE tprimarykeylock
(
    c1 INT(11) NOT NULL,
    c2 VARCHAR(20) NOT NULL,
    CONSTRAINT PRIMARY KEY(c1, c2)
) ENGINE = InnoDB
EOT;

        $queries[] = <<<EOT
CREATE TABLE tprimarykeyunlock
(
    c1 INT(11) NOT NULL,
    CONSTRAINT PRIMARY KEY(c1)
) ENGINE = InnoDB
EOT;

        $queries[] = <<<EOT
CREATE TABLE tforeignkey
(
    c1 INT(11) NOT NULL,
    c2 VARCHAR(20) NOT NULL,
    INDEX _fk1 (c1, c2),
    CONSTRAINT fk1 FOREIGN KEY(c1, c2) REFERENCES tprimarykeylock(c1, c2)
) ENGINE = InnoDB
EOT;

        $queries[] = <<<EOT
CREATE TABLE tindex
(
    c1 INT(11) NOT NULL,
    c2 VARCHAR(20) NOT NULL,
    CONSTRAINT idx1 UNIQUE (c1, c2),
    INDEX idx2 (c1)
) ENGINE = InnoDB
EOT;

        $queries[] = 'CREATE VIEW vcolumns AS SELECT cinteger FROM tcolumns';

        return $queries;
    }

    /**
     * {@inheritdoc}
     */
    public function getDropSchemaSQLQueries()
    {
        return array_slice(parent::getDropSchemaSQLQueries(), 1);
    }
}
