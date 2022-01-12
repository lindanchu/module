<?php

namespace Linhdanchu\Module;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use ReflectionClass;
use Illuminate\Console\Command;

class ServiceProvider extends BaseServiceProvider
{
    protected $commands = [
        // 'Linhdanchu\Module\Commands\CreateModuleCommand',
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        require_once __DIR__ . '/Functions/functions.php';
    }


    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        
        $path_module = base_module();
        // load provider
        $provider_paths = glob($path_module.'/*/Providers/*.php');
        foreach ($provider_paths as $provider_path) {
            $provider_path = str_replace(app_path(), '', $provider_path);
            $provider_path = str_replace('/', '\\', $provider_path);
            $provider_namespace = str_replace('.php', '', $provider_path);
            
            if (is_subclass_of($provider_namespace, BaseServiceProvider::class) &&
                ! (new ReflectionClass($provider_namespace))->isAbstract()) {
                $this->app->register($provider_namespace);
            }
        }

        // load views
        $array_path_modules = glob($path_module. '/*', GLOB_NOSORT);
        
        foreach ($array_path_modules as $value) {
            $module = str_replace($path_module . '/', '', $value); // lấy tên module
            if(is_dir($value. '/Views')) {
                $this->loadViewsFrom($value. '/Views', $module); // đăng kí view
            }
        }

        // load config
        $configs = glob($path_module.'/*/config/*.php');
        
        foreach ($configs as $config) {
            $module = str_replace($path_module . '/', '', $value); // lấy tên module
            $module = explode('/', $module)[0] ?? null;
            if(!$module) continue;
            $module = strtolower($module);
            $name_file_explode = explode('/', $config);
            $name_file = end($name_file_explode);
            $name_file = str_replace('.php', '', $name_file); // lấy tên file
            $name_file = strtolower($name_file);

            $this->mergeConfigFrom($config, $module . ':' . $name_file);
        }

        // load migrations
        $migrations = glob($path_module.'/*/database/migrations/*.php');

        foreach ($migrations as $migration) {
            $this->loadMigrationsFrom($migration);
        }

        // load commands
        $commands = glob($path_module.'/*/Console/Commands/*.php');
        foreach ($commands as $command) {
            $command_path = str_replace(app_path(), '', $command);
            $command_path = str_replace('/', '\\', $command_path);
            $command_namespace = str_replace('.php', '', $command_path);
            
            if (is_subclass_of($command_namespace, Command::class) &&
                ! (new ReflectionClass($command_namespace))->isAbstract()) {
                $this->commands[] = $command_namespace;
            }
        }
        if($this->commands) {
            $this->commands($this->commands);
        }
        
    }
}
