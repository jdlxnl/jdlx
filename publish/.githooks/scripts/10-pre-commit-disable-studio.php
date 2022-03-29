#!/usr/bin/env php
<?php

$base = dirname(dirname(__DIR__));
$studioFile = $base . "/studio.json";

if (file_exists($studioFile)) {
    $data = getJson($studioFile);
    $paths = $data["paths"] ?? [];
    $names = [];
    foreach ($paths as $path) {
        $config = getJson($path . "/composer.json");
        if (isset($config["name"])) {
            $names[] = $config["name"];
        }
    }

    if (count($names) > 0) {
        rename($studioFile, $studioFile . ".bak");
        chdir($base);
        exec("composer update " . implode(" ", $names));
        exec("git add composer.lock");
    }
    return 0;
}

function getJson($path)
{
    if (!file_exists($path)) {
        return [];
    }
    $json = json_decode(file_get_contents($path), true);
    if (empty($json)) {
        return [];
    }
    return $json;
}
