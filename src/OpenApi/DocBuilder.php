<?php

namespace Jdlx\OpenApi;

use Illuminate\Support\Str;

class DocBuilder
{
    protected $paths = [];

    public function __construct($paths = [])
    {
        $this->paths = $paths;
    }

    public function addPath($path)
    {
        $this->paths[] = $path;
        return $this;
    }

    public function writeTo($path){
        $config = str_replace("\/", "/", json_encode($this->build(), JSON_PRETTY_PRINT));
        $dir = dirname($path);
        if(!file_exists($dir)) mkdir($dir, 0755, true);
        file_put_contents($path, $config);
    }

    public function build()
    {
        $config = [];
        foreach ($this->paths as $path) {
            $config = $this->merge($config, $path, "");
        }

        return $this->replacePlaceholders($config);
    }

    protected function replacePlaceholders($config){
        $string = json_encode($config, JSON_PRETTY_PRINT);
        $string =  str_replace("#Application#", config("app.name"), $string);
        return json_decode($string, true);
    }

    protected function merge($config, $path, $dir)
    {
        $q = rtrim("$path/${dir}", "/");

        $files = glob("$q/*.json");
        foreach ($files as $file) {
            $config = $this->mergeFile($file, $config, $dir);
        };

        $directories = glob("$q/*", GLOB_ONLYDIR);
        foreach ($directories as $directory) {
            $dir = trim(Str::replaceFirst($path,"", $directory), "/");
            $config = $this->merge($config, $path, $dir);
        }
        return $config;
    }

    protected function mergeFile($filePath, $config, $configPath): array
    {
        $json = json_decode(file_get_contents($filePath), true);
        $parts = empty($configPath) ? [] : explode("/", $configPath);
        $target = &$config;

        if (COUNT($parts) > 0) {
            $resultKey = Str::camel(array_pop($parts));

            foreach ($parts as $part) {
                $key = Str::camel($part);
                if (!isset($target[$key])) {
                    $target[$key] = [];
                }
                $target = &$target[$key];
            }
            $target[$resultKey] = array_merge_recursive($target[$resultKey] ?? [], $json);

        }else {
            $config =  array_merge_recursive($config, $json);
        }

        return $config;
    }
}
