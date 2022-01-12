<?php

if(!function_exists('base_path_module')) {
    function base_path_module(string $path = '')
    {
        $path_file_call = debug_backtrace()[0]['file'] ?? null;

        if(!$path_file_call) {
            throw new \Exception('Not found file call');
        }
        
        $app_path = base_module();
        $module = str_replace($app_path . '/', '', $path_file_call);
        $module_array = explode('/', $module);
        
        $name_module = $module_array[0] ?? null;

        if($name_module === null) {
            throw new \Exception('Not found module');
        }

        $path_return = $app_path . '/' . $name_module;

        if($path !== '') {
            if(substr($path, 0, 1) != '/') {
                $path_return .= '/';
            }
            $path_return .= $path;
        }

        return $path_return;
    }
}

if(!function_exists('base_module')) {
    function base_module()
    {
        return app_path() . '/Modules';
    }
}