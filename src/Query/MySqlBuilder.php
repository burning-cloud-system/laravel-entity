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
            $results[$obj->column_name] = [
                Builder::FIELD_TABLE_SCHEMA   => $obj->table_schema,
                Builder::FIELD_TABLE_NAME     => $obj->table_name,
                Builder::FIELD_COLUMN_NAME    => $obj->column_name,
                Builder::FIELD_COLUMN_DEFAULT => $obj->column_default,
                Builder::FIELD_IS_NULLABLE    => $obj->is_nullable,
                Builder::FIELD_DATA_TYPE      => $obj->data_type,
                Builder::FIELD_CHARACTER_MAXIMUM_LENGTH => $obj->character_maximum_length,
                Builder::FIELD_NUMERIC_SCALE  => $obj->numeric_scale,
                Builder::FIELD_COLUMN_TYPE    => $obj->column_type,
                Builder::FIELD_COLUMN_KEY     => $obj->column_key,
                Builder::FIELD_EXTRA          => $obj->extra,
                Builder::FIELD_COLUMN_COMMENT => $obj->column_comment
            ];
        }
        return $results;
    }

}