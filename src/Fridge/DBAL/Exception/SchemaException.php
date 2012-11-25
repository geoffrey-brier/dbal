<?php

/*
 * This file is part of the Fridge DBAL package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Fridge\DBAL\Exception;

/**
 * Schema exception.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class SchemaException extends Exception
{
    /**
     * Gets the "INVALID ASSET NAME" exception.
     *
     * @param string $asset The asset concerned by the exception.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "INVALID ASSET NAME" exception.
     */
    static public function invalidAssetName($asset)
    {
        return new static(sprintf('The %s name must be a string.', $asset));
    }

    /**
     * Gets the "INVALID COLUMN AUTO INCREMENT FLAG" exception.
     *
     * @param string $column The column concerned by the exception.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "INVALID COLUMN AUTO INCREMENT FLAG" exception.
     */
    static public function invalidColumnAutoIncrementFlag($column)
    {
        return new static(sprintf('The auto increment flag of the column "%s" must be a boolean.', $column));
    }

    /**
     * Gets the "INVALID COLUMN COMMENT" exception.
     *
     * @param string $column The column concerned by the exception.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "INVALID COLUMN COMMENT" exception.
     */
    static public function invalidColumnComment($column)
    {
        return new static(sprintf('The comment of the column "%s" must be a string.', $column));
    }

    /**
     * Gets the "INVALID COLUMN FIXED FLAG" exception.
     *
     * @param string $column The column concerned by the exception.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "INVALID COLUMN FIXED FLAG" exception.
     */
    static public function invalidColumnFixedFlag($column)
    {
        return new static(sprintf('The fixed flag of the column "%s" must be a boolean.', $column));
    }

    /**
     * Gets the "INVALID COLUMN LENGTH" exception.
     *
     * @param string $column The column concerned by the exception.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "INVALID COLUMN LENGTH" exception.
     */
    static public function invalidColumnLength($column)
    {
        return new static(sprintf('The length of the column "%s" must be a positive integer.', $column));
    }

    /**
     * Gets the "INVALID COLUMN NOT NULL FLAG" exception.
     *
     * @param string $column The column concerned by the exception.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "INVALID COLUMN NOT NULL FLAG" exception.
     */
    static public function invalidColumnNotNullFlag($column)
    {
        return new static(sprintf('The not null flag of the column "%s" must be a boolean.', $column));
    }

    /**
     * Gets the "INVALID COLUMN PROPERTY" exception.
     *
     * @param string $column   The column concerned by the exception.
     * @param string $property The column property.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "INVALID COLUMN PROPERTY" exception.
     */
    static public function invalidColumnProperty($column, $property)
    {
        return new static(sprintf('The property "%s" of the column "%s" does not exist.', $property, $column));
    }

    /**
     * Gets the "INVALID COLUMN PRECISION" exception.
     *
     * @param string $column The column concerned by the exception.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "INVALID COLUMN PRECISION" exception.
     */
    static public function invalidColumnPrecision($column)
    {
        return new static(sprintf('The precision of the column "%s" must be a positive integer.', $column));
    }

    /**
     * Gets the "INVALID COLUMN SCLAE" exception.
     *
     * @param string $column The column concerned by the exception.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "INVALID COLUMN SCALE" exception.
     */
    static public function invalidColumnScale($column)
    {
        return new static(sprintf('The scale of the column "%s" must be a positive integer.', $column));
    }

    /**
     * Gets the "INVALID COLUMN UNSIGNED FLAG" exception.
     *
     * @param string $column The column concerned by the exception.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "INVALID COLUMN UNSIGNED FLAG" exception.
     */
    static public function invalidColumnUnsignedFlag($column)
    {
        return new static(sprintf('The unsigned flag of the column "%s" must be a boolean.', $column));
    }

    /**
     * Gets the "INVALID FOREIGN KEY FOREIGN COLUMN NAME" exception.
     *
     * @param string $foreignKey The foreign key concerned by the exception.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "INVALID FOREIGN KEY FOREIGN COLUMN NAME" exception.
     */
    static public function invalidForeignKeyForeignColumnName($foreignKey)
    {
        return new static(sprintf('The foreign column name of the foreign key "%s" must be a string.', $foreignKey));
    }

    /**
     * Gets the "INVALID FOREIGN KEY FOREIGN TABLE NAME" exception.
     *
     * @param string $foreignKey The foreign key concerned by the exception.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "INVALID FOREIGN KEY FOREIGN TABLE NAME" exception.
     */
    static public function invalidForeignKeyForeignTableName($foreignKey)
    {
        return new static(sprintf('The foreign table name of the foreign key "%s" must be a string.', $foreignKey));
    }

    /**
     * Gets the "INVALID FOREIGN KEY LOCAL COLUMN NAME" exception.
     *
     * @param string $foreignKey The foreign key concerned by the exception.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "INVALID FOREIGN KEY LOCAL COLUMN NAME" exception.
     */
    static public function invalidForeignKeyLocalColumnName($foreignKey)
    {
        return new static(sprintf('The local column name of the foreign key "%s" must be a string.', $foreignKey));
    }

    /**
     * Gets the "INVALID INDEX COLUMN NAME" exception.
     *
     * @param string $index The index concerned by the exception.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "INVALID INDEX COLUMN NAME" exception.
     */
    static public function invalidIndexColumnName($index)
    {
        return new static(sprintf('The column name of the index "%s" must be a string.', $index));
    }

    /**
     * Gets the "INVALID INDEX UNIQUE FLAG" exception.
     *
     * @param string $index The index concerned by the exception.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "INVALID INDEX UNIQUE FLAG" exception.
     */
    static public function invalidIndexUniqueFlag($index)
    {
        return new static(sprintf('The unique flag of the index "%s" must be a boolean.', $index));
    }

    /**
     * Gets the "INVALID PRIMARY KEY COLUMN NAME" exception.
     *
     * @param string $primaryKey The primary key concerned by the exception.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "INVALID PRIMARY KEY COLUMN NAME" exception.
     */
    static public function invalidPrimaryKeyColumnName($primaryKey)
    {
        return new static(sprintf('The column name of the primary key "%s" must be a string.', $primaryKey));
    }

    /**
     * Gets the "INVALID SEQUENCE INITIAL VALUE" exception.
     *
     * @param string $sequence The sequence concerned by the exception.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "INVALID SEQUENCE INITIAL VALUE" exception.
     */
    static public function invalidSequenceInitialValue($sequence)
    {
        return new static(sprintf('The initial value of the sequence "%s" must be a positive integer.', $sequence));
    }

    /**
     * Gets the "INVALID SEQUENCE INCREMENT SIZE" exception.
     *
     * @param string $sequence The sequence concerned by the exception.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "INVALID SEQUENCE INCREMENT SIZE" exception.
     */
    static public function invalidSequenceIncrementSize($sequence)
    {
       return new static(sprintf('The increment size of the sequence "%s" must be a positive integer.', $sequence));
    }

    /**
     * Gets the "INVALID VIEW SQL" exception.
     *
     * @param string $view The view concerned by the exception.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "INVALID VIEW SQL" exception.
     */
    static public function invalidViewSQL($view)
    {
        return new static(sprintf('The SQL query of the view "%s" must be a string.', $view));
    }

    /**
     * Gets the "TABLE COLUMN ALREADY EXISTS" exception.
     *
     * @param string $table  The table name.
     * @param string $column The column name.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "TABLE COLUMN ALREADY EXISTS" exception.
     */
    static public function tableColumnAlreadyExists($table, $column)
    {
        return new static(sprintf('The column "%s" of the table "%s" already exists.', $column, $table));
    }

    /**
     * Gets the "TABLE COLUMN DOES NOT EXIST" exception.
     *
     * @param string $table  The table name.
     * @param string $column The column name.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "TABLE COLUMN DOES NOT EXIST" exception.
     */
    static public function tableColumnDoesNotExist($table, $column)
    {
        return new static(sprintf('The column "%s" of the table "%s" does not exist.', $column, $table));
    }

    /**
     * Gets the "TABLE PRIMARY KEY ALREADY EXISTS" exception.
     *
     * @param string $table The table name.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "TABLE PRIMARY KEY ALREADY EXISTS" exception.
     */
    static public function tablePrimaryKeyAlreadyExists($table)
    {
        return new static(sprintf('The table "%s" has already a primary key.', $table));
    }

    /**
     * Gets the "TABLE PRIMARY KEY DOES NOT EXIST" exception.
     *
     * @param string $table The table name.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "TABLE PRIMARY KEY DOES NOT EXIST" exception.
     */
    static public function tablePrimaryKeyDoesNotExist($table)
    {
        return new static(sprintf('The table "%s" has no primary key.', $table));
    }

    /**
     * Gets the "TABLE FOREIGN KEY ALREADY EXISTS" exception.
     *
     * @param string $table      The table name.
     * @param string $foreignKey The foreign key name.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "TABLE FOREIGN KEY ALREADY EXISTS" exception.
     */
    static public function tableForeignKeyAlreadyExists($table, $foreignKey)
    {
        return new static(sprintf('The foreign key "%s" of the table "%s" already exists.', $foreignKey, $table));
    }

    /**
     * Gets the "TABLE FOREIGN KEY DOES NOT EXIST" exception.
     *
     * @param string $table      The table name.
     * @param string $foreignKey The foreign key name.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "TABLE FOREIGN KEY DOES NOT EXIST" exception.
     */
    static public function tableForeignKeyDoesNotExist($table, $foreignKey)
    {
        return new static(sprintf('The foreign key "%s" of the table "%s" does not exist.', $foreignKey, $table));
    }

    /**
     * Gets the "TABLE INDEX ALREADY EXISTS" exception.
     *
     * @param string $table The table name.
     * @param string $index The index name.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "TABLE INDEX ALREADY EXISTS" exception.
     */
    static public function tableIndexAlreadyExists($table, $index)
    {
        return new static(sprintf('The index "%s" of the table "%s" already exists.', $index, $table));
    }

    /**
     * Gets the "TABLE INDEX DOES NOT EXIST" exception.
     *
     * @param string $table The table name.
     * @param string $index The index name.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "TABLE INDEX DOES NOT EXIST" exception.
     */
    static public function tableIndexDoesNotExist($table, $index)
    {
        return new static(sprintf('The index "%s" of the table "%s" does not exist.', $index, $table));
    }

    /**
     * Gets the "SCHEMA TABLE ALREADY EXISTS" exception.
     *
     * @param string $schema The schema name.
     * @param string $table  The table name.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "SCHEMA TABLE ALREADY EXISTS" exception.
     */
    static public function schemaTableAlreadyExists($schema, $table)
    {
        return new static(sprintf('The table "%s" of the schema "%s" already exists.', $table, $schema));
    }

    /**
     * Gets the "SCHEMA TABLE DOES NOT EXIST" exception.
     *
     * @param string $schema The schema name.
     * @param string $table  The table name.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "SCHEMA TABLE DOES NOT EXIST" exception.
     */
    static public function schemaTableDoesNotExist($schema, $table)
    {
        return new static(sprintf('The table "%s" of the schema "%s" does not exist.', $table, $schema));
    }

    /**
     * Gets the "SCHEMA SEQUENCE ALREADY EXISTS" exception.
     *
     * @param string $schema   The schema name.
     * @param string $sequence The sequence name.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "SCHEMA SEQUENCE ALREADY EXISTS" exception.
     */
    static public function schemaSequenceAlreadyExists($schema, $sequence)
    {
        return new static(sprintf('The sequence "%s" of the schema "%s" already exists.', $sequence, $schema));
    }

    /**
     * Gets the "SCHEMA SEQUENCE DOES NOT EXIST" exception.
     *
     * @param string $schema   The schema name.
     * @param string $sequence The sequence name.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "SCHEMA SEQUENCE DOES NOT EXIST" exception.
     */
    static public function schemaSequenceDoesNotExist($schema, $sequence)
    {
        return new static(sprintf('The sequence "%s" of the schema "%s" does not exist.', $sequence, $schema));
    }

    /**
     * Gets the "SCHEMA VIEW ALREADY EXISTS" exception.
     *
     * @param string $schema The schema name.
     * @param string $view   The view name.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "SCHEMA VIEW ALREADY EXISTS" exception.
     */
    static public function schemaViewAlreadyExists($schema, $view)
    {
        return new static(sprintf('The view "%s" of the schema "%s" already exists.', $view, $schema));
    }

    /**
     * Gets the "SCHEMA VIEW DOES NOT EXIST" exception.
     *
     * @param string $schema The schema name.
     * @param string $view   The view name.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "SCHEMA VIEW DOES NOT EXIST" exception.
     */
    static public function schemaViewDoesNotExist($schema, $view)
    {
        return new static(sprintf('The view "%s" of the schema "%s" does not exist.', $view, $schema));
    }

    /**
     * Gets the "TABLE CHECK DOES NOT EXIST" exception.
     *
     * @param string $table The table name.
     * @param string $check The check name.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "TABLE CHECK DOES NOT EXIST" exception.
     */
    static public function tableCheckDoesNotExist($table, $check)
    {
        return new static(sprintf('The check "%s" of the table "%s" does not exist.', $check, $table));
    }

    /**
     * Gets the "TABLE CHECK ALREADY EXISTS" exception.
     *
     * @param string $table The table name.
     * @param string $check The check name.
     *
     * @return \Fridge\DBAL\Exception\SchemaException The "TABLE CHECK ALREADY EXISTS" exception.
     */
    static public function tableCheckAlreadyExists($table, $check)
    {
        return new static(sprintf('The check "%s" of the table "%s" already exists.', $check, $table));
    }
}
