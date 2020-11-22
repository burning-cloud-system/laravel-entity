<?php

namespace BurningCloudSystem\Entity\Query;

class MySqlBuilder extends Builder
{

    /**
     * Compile the query to determine the list of tables.
     *
     * @param string $table
     * @return string
     */
    public function compileTableExists($table = '*'): string
    {
        if ($table === '*') {
            return "select * from information_schema.tables where table_schema = ? and table_type = 'BASE TABLE'";
        } else {
            $table = $this->connection->getTablePrefix().$table;
            return "select * from information_schema.tables where table_schema = ? and table_name = '{$table}' and table_type = 'BASE TABLE'";
        }
    }

    /**
     * Compile the query to determine the list of columns.
     *
     * @return string
     */
    public function compileColumnListing(): string
    {
        return 'select * from information_schema.columns where table_schema = ? and table_name = ?';
    }

    /**
     * Process the results of a column name query.
     *
     * @param array $columnList
     * @return array
     */
    protected function processColumnNameList($columnList): array
    {
        $results = [];
        foreach($columnList as $columnObj)
        {
            $obj = (object) $columnObj;
            $results[$obj->COLUMN_NAME] = [
                Builder::FIELD_TABLE_SCHEMA   => $obj->TABLE_SCHEMA,
                Builder::FIELD_TABLE_NAME     => $obj->TABLE_NAME,
                Builder::FIELD_COLUMN_NAME    => $obj->COLUMN_NAME,
                Builder::FIELD_COLUMN_DEFAULT => $obj->COLUMN_DEFAULT,
                Builder::FIELD_IS_NULLABLE    => $obj->IS_NULLABLE,
                Builder::FIELD_DATA_TYPE      => $obj->DATA_TYPE,
                Builder::FIELD_CHARACTER_MAXIMUM_LENGTH => $obj->CHARACTER_MAXIMUM_LENGTH,
                Builder::FIELD_NUMERIC_SCALE  => $obj->NUMERIC_SCALE,
                Builder::FIELD_COLUMN_TYPE    => $obj->COLUMN_TYPE,
                Builder::FIELD_COLUMN_KEY     => $obj->COLUMN_KEY,
                Builder::FIELD_EXTRA          => $obj->EXTRA,
                Builder::FIELD_COLUMN_COMMENT => $obj->COLUMN_COMMENT
            ];
        }
        return $results;
    }

}