<?php

namespace BurningCloudSystem\Entity\Console;

use BurningCloudSystem\Entity\DatabaseEntityRepository;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;

class MakeEntityCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:entity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new entity class';

    /**
     * The repository instance.
     *
     * @var DatabaseEntityRepository;
     */
    protected $repository;

    /**
     * The create entity.
     *
     * @var array
     */
    protected $createEntity = [];

    /**
     * The table name.
     *
     * @var string
     */
    protected $tableName;

    /**
     * Create a new command instance.
     *
     * @param DatabaseEntityRepository $repository
     * @param Filesystem $files
     */
    public function __construct(DatabaseEntityRepository $repository,
                                Filesystem $files)
    {
        parent::__construct($files);
        
        $this->repository = $repository;
    }

    /**
     * Validity check.
     *
     * @param string $name
     * @return boolean
     */
    protected function chkValidity(string $name): bool
    {
        return $this->repository->hasTable($name);
    }

    /**
     * Create table name.
     *
     * @param string $name
     * @return string
     */
    protected function createTableName(string $name): string
    {
        return $this->tableName = $name;
    }

    /**
     * Get replace class data.
     *
     * @return string
     */
    protected function replaceClassData(): string
    {
        if ($this->option('property')) 
        {
            return $this->repository->getProperties($this->tableName, $this->getPropertyStub());
        }
        return $this->repository->getProperties($this->tableName, $this->getPropertyStub());
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/entity.stub');
    }

    /**
     * Get the property stub file for the generator.
     *
     * @return string
     */
    protected function getPropertyStub(): string
    {
        return $this->resolveStubPath('/stubs/property.stub');
    }

    /**
     * Resolve the full-qualified path to the stub.
     *
     * @param string $stub
     * @return string
     */
    protected function resolveStubPath($stub): string
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
                ? $customPath
                : __DIR__.$stub;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['property', 'p', InputOption::VALUE_NONE, 'Create a new property for the entity']
        ];
    }

}