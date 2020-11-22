<?php

namespace BurningCloudSystem\Entity\Query;

use BurningCloudSystem\Entity\DatabaseEntityRepository;
use Illuminate\Database\ConnectionResolverInterface as Resolver;
use InvalidArgumentException;

abstract class Builder
{
    public const FIELD_TABLE_SCHEMA   = "TABLE_SCHEMA";
    public const FIELD_TABLE_NAME     = "TABLE_NAME";
    public const FIELD_COLUMN_NAME    = "COLUMN_NAME";
    public const FIELD_IS_NULLABLE    = "IS_NULLABLE";
    public const FIELD_COLUMN_DEFAULT = "COLUMN_DEFAULT";
    public const FIELD_DATA_TYPE      = "DATA_TYPE";
    public const FIELD_CHARACTER_MAXIMUM_LENGTH = "CHARACTER_MAXIMUM_LENGTH";
    public const FIELD_NUMERIC_SCALE  = "NUMERIC_SCALE";
    public const FIELD_COLUMN_TYPE    = "COLUMN_TYPE";
    public const FIELD_COLUMN_COMMENT = "COLUMN_COMMENT";
    public const FIELD_COLUMN_KEY     = "COLUMN_KEY";
    public const FIELD_EXTRA          = "EXTRA";

    /**
     * The repository instance.
     *
     * @var DatabaseEntityRepository;
     */
    protected $repository;

    /**
     * The database connection instance.
     *
     * @var \Illuminate\Database\Connection
     */
    protected $connection;

    /**
     * Compile the query to determine the list of table.
     *
     * @return string
     */
    abstract public function compileTableExists($table = '*'): string;

    /**
     * Compile the query to determine the list of columns.
     *
     * @return string
     */
    abstract public function compileColumnListing(): string;

    /**
     * Process the results of a column name query.
     *
     * @param array $columnList
     * @return array
     */
    abstract protected function processColumnNameList($columnList): array;

    /**
     * Create a new query builder instance.
     *
     * @param DatabaseEntityRepository $repository
     */
    private function __construct(DatabaseEntityRepository $repository)
    {
        $this->repository = $repository;
        $this->connection = $repository->getConnection();
    }

    /**
     * Get the table name listing.
     *
     * @param string $table
     * @return array
     */
    public function getTableNameList($table = '*'): array
    {
        $results = $this->connection->select(
            $this->compileTableExists($table), [$this->connection->getDatabaseName()]
        );
        return $this->processTableNameList($results);
    }

    /**
     * Process the results of a table name query.
     *
     * @param array $tableList
     * @return array
     */
    protected function processTableNameList($tableList): array
    {
        return array_map(function($result){
            return ((object) $result)->table_name;
        }, $tableList);
    }

    /**
     * Get the column name listing.
     *
     * @param string $table
     * @return array
     */
    public function getColumnList(string $table): array
    {
        $results = $this->connection->select(
            $this->compileColumnListing(), [$this->connection->getDatabaseName(), $table]
        );
        return $this->processColumnNameList($results);
    }

    /**
     * Get the database builder.
     *
     * @param DatabaseEntityRepository $repository
     * @return void
     * 
     * @throws InvalidArgumentException
     */
    public static function factory(DatabaseEntityRepository $repository) 
    {
        $name = $repository->getDefaultConnection();
        switch($name) 
        {
            case 'mysql': return new MySqlBuilder($repository);
        }

        throw new InvalidArgumentException("Database [{$name}] not builder.");
    }
}