<?php

use Jdlx\OpenApi\DocBuilder;

Route::get('/api/documentation', function(){
    return view("jdlx::swagger");
});

Route::get('/api/api-docs', function(){

    $path = base_path()."/app/Documentation";
    $dest = base_path()."/storage/api-docs/api-docs.json";
    (new DocBuilder([$path]))->writeTo($dest);

    return file_get_contents(base_path()."/storage/api-docs/api-docs.json");
});
