<?php

namespace Linhdanchu\Module\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class CreateModuleCommand extends Command
{
    protected $name_module; // name module
    protected $files; // library files
    protected $path_module; // path module

    /**
     * The name and signature of the console command.
     *
     * @var string
     * @param string $module name module
     */
    protected $signature = 'ldc-make:module {module}';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->files = new Filesystem;
    }

    /**
     * Get name module
     */
    private function getNameModule()
    {
        $name_module = preg_replace('/[^A-Za-z0-9]/', '', $this->argument('module'));
        $name_module = preg_replace('/^[0-9]*/', '', $name_module);
        $name_module = ucfirst($name_module);
        $this->name_module = $name_module;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->getNameModule();
        $this->path_module = app_path() . '/Modules/' . $this->name_module;
        // check module exists
        if(is_dir($this->path_module)) {
            return $this->error('Module đã tồn tại!');
        }

        // create folder module
        $this->createFolder($this->path_module);

        // create folder controllers
        $this->createFolder($this->path_module . '/Controllers');
        // create folder models
        $this->createFolder($this->path_module . '/Models');
        // create folder routes
        $this->createFolder($this->path_module . '/routes');
    }

    /**
     * Create folder module
     */
    private function createFolder($path)
    {
        $result = $this->files->makeDirectory($path, 0777, true, true);
    }
}