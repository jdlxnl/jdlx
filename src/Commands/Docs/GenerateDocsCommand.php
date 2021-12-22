<?php


namespace Jdlx\Commands\Docs;


use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Jdlx\OpenApi\DocBuilder;

class GenerateDocsCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'docs:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate OpenAPI json from a given path';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $path = base_path()."/app/Documentation";
        $dest = base_path()."/storage/api-docs/api-docs.json";
        (new DocBuilder([$path]))->writeTo($dest);
    }

}
