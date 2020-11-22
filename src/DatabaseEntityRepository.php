<?php

namespace BurningCloudSystem\Entity;

use BurningCloudSystem\Entity\Query\Builder;
use Illuminate\Database\ConnectionResolverInterface as Resolver;
use Illuminate\Support\Str;

class DatabaseEntityRepository
{
    /**
     * The database connection resolver instance.
     *
     * @var \Illuminate\Database\ConnectionResolverInterface
     */
    protected $resolver;

    /**
     * The database builder instance.
     *
     * @var Builder
     */
    protected $builder;

    /**
     * The name of the database connection to use.
     *
     * @var string
     */
    protected $connection;

    /**
     * Create a new database repository instance.
     *
     * @param Resolver $resolver
     */
    public function __construct(Resolver $resolver)
    {
        $this->resolver = $resolver;
        $this->builder = Builder::factory($this);
    }

    /**
     * Determine if the given table exists.
     *
     * @param string $table
     * @return boolean
     */
    public function hasTable(string $table): bool
    {
        return $this->getConnection()->getSchemaBuilder()->hasTable($table);
    }

    /**
     * Get the connection resolver instance.
     *
     * @return \Illuminate\Database\ConnectionResolverInterface
     */
    public function getConnectionResolver()
    {
        return $this->resolver;
    }

    /**
     * Resolve the database connection instance.
     *
     * @return \Illuminate\Database\Connection
     */
    public function getConnection()
    {
        return $this->resolver->connection();
    }

    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDefaultConnection()
    {
        return $this->resolver->getDefaultConnection();
    }

    /**
     * Create a property from a database column
     *
     * @param string $tableName
     * @return void
     */
    public function getProperties(string $tableName, string $propertyStub)
    {
        $result = '';
        foreach($this->builder->getColumnList($tableName) as $columnName => $column)
        {
            if ($column[Builder::FIELD_TABLE_NAME] == $tableName) 
            {
                $stub = $propertyStub;

                $columnName     = Str::studly($column[Builder::FIELD_COLUMN_NAME]);
                $columnComment  = $column[Builder::FIELD_COLUMN_COMMENT];
                $columnDataType = '';
                switch(Str::upper($column[Builder::FIELD_DATA_TYPE]))
                {
                    case "BIGINT":
                    case "INTEGER":
                    case "INT": 
                    case "MEDIUMINT":
                    case "SMALLINT":
                    case "TINYINT":
                        $columnDataType = 'int';
                        break;
                    case "DOUBLE":
                        $columnDataType = 'double';
                        break;
                    case "FLOAT":
                        $columnDataType = 'float';
                        break;
                    case "NUMERIC":
                    case "DECIMAL":
                        $columnDataType = 'double';
                        break;
                    case "DATE":
                    case "DATETIME":
                    case "TIMESTAMP":
                        $columnDataType = '\DateTime';
                        break;
                    case "CHAR":
                    case "VARCHAR":
                    case "BINARY":
                    case "VARBINARY":
                    case "BLOB":
                    case "TEXT":
                    case "SET":
                        $columnDataType = 'string';
                        break;
                    case "ENUM":
                        $columnDataType = 'Enum';
                        break;
                    default:
                        $columnDataType = 'mixed';
                        break;
                }
                $stub = str_replace('{{ property }}'    , $columnName       , $stub);
                $stub = str_replace('{{ data_type }}'   , $columnDataType   , $stub);
                $stub = str_replace('{{ comment }}'     , $columnComment    , $stub);

                $result .= "\n".$stub."\n";

            }
        }
        return $result;
    }
}