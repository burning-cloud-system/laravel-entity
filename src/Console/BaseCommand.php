<?php

namespace BurningCloudSystem\Entity\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

abstract class BaseCommand extends GeneratorCommand
{
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Entity';

    /**
     * Create a new controller creator command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct($files);
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // First we need to ensure that the given name is not a reserved word within the PHP
        // language and that the class name will actually be valid. If it is not valid we
        // can error now and prevent from polluting the filesystem using invalid files.
        if ($this->isReservedName($this->getNameInput())) {
            $this->error('The name "'.$this->getNameInput().'" is reserved by PHP.');
            return;
        }

        $name = $this->getNameInput();

        if (!$this->chkValidity($name)) 
        {
            $this->error('The name "'.$this->getNameInput().'" not valid.');
            return;
        }

        $tableName = $this->createTableName($name);
        $className = $this->qualifyClass($this->createClassName($tableName));
        $path      = $this->getPath($className);

        $this->makeDirectory($path);

        $this->files->put($path, $this->sortImports($this->buildClass($className)));

        $this->info($this->type.' created successfully.');
    }

    /**
     * Validity check.
     *
     * @param string $name
     * @return boolean
     */
    abstract protected function chkValidity(string $name): bool;

    /**
     * Create table name.
     *
     * @param string $name
     * @return string
     */
    abstract protected function createTableName(string $name): string;

    /**
     * Get replace class data.
     *
     * @return string
     */
    abstract protected function replaceClassData(): string;

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\\'.Str::plural('Entity');
    }

    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param string $name
     * @return string
     */
    protected function createClassName(string $name): string
    {
        return Str::studly(Str::singular($name));
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name)
    {
        $stub = str_replace('{{ namespace }}', $this->getNamespace($name), $stub);
        return $this;
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        return parent::replaceClass($stub, $name);
    }
}