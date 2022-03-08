<?php


namespace Jdlx\Commands\Jdlx;


use Illuminate\Console\Command;

class JdlxInstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'jdlx:install {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Complete all steps of the JDLX framework';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {

        if (!$this->validDatabase()) {
            $this->output->error("Database not setup!");
            return;
        }

        $force = $this->input->getOption("force");

        $this->output->title("Publishing Vendor Packages");
        $this->output->info("Sanctum");
        $this->call('vendor:publish', ['--provider' => 'Laravel\Sanctum\SanctumServiceProvider']);
        $this->output->info("Spatie");
        $this->call('vendor:publish', ['--provider' => 'Spatie\Permission\PermissionServiceProvider']);

        ## config option
        $this->output->info("JDLX");
        $this->call('vendor:publish', ['--provider' => 'Jdlx\JdlxServiceProvider', "--force" => $force]);

        $this->output->title("Run the migration");
        $this->call('migrate');

        // $this->call('api:scaffold', ["User"]);

        $this->output->title("Add stateful middleware to kernel");
        $path = base_path() . "/app/http/kernel.php";
        $insert = "            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,";
        $content = file_get_contents($path);
        if (stristr($content, $insert)) {
            $this->output->warning("Already added");
        } else {
            $res = $this->cut("after", '/(.*)\/\/ \\\\Laravel\\\\Sanctum\\\\Http\\\\Middleware\\\\EnsureFrontendRequestsAreStateful(.*)/', $content);
            $start = explode("\n", $res[0]);
            $end = explode("\n", $res[1]);

            array_pop($start);
            $start[] = $insert;
            file_put_contents($path, implode("\n", array_merge($start, $end)));
            $this->output->success("Enabled Stateful middleware in  app/http/kernel.php");
        }

        $this->output->title("Add force json to kernel");
        $content = file_get_contents($path);
        if (stristr($content, "ForceJsonResponse")) {
            $this->output->warning("Already added");
        } else {
            $res = $this->cut("after", '/(.*)Kernel extends HttpKernel(.*)/', $content);
            $start = explode("\n", $res[0]);
            $end = explode("\n", $res[1]);

            $start[] = array_shift($end);
            $start[] = '    public function __construct(\Illuminate\Contracts\Foundation\Application $app, \Illuminate\Routing\Router $router)';
            $start[] = '    {';
            $start[] = '        parent::__construct($app, $router);';
            $start[] = '        $this->prependMiddlewareToGroup(\'api\', \App\Http\Middleware\ForceJsonResponse::class);';
            $start[] = '    }';
            $start[] = '';

            file_put_contents($path, implode("\n", array_merge($start, $end)));
            $this->output->success("Enabled Stateful middleware in  app/http/kernel.php");
        }

        $this->output->title("Adding service provider to config/app.php");

        $path = base_path() . "/config/app.php";
        $insert = "Jdlx\Providers\ResponseServiceProvider::class";
        $content = file_get_contents($path);
        if (stristr($content, $insert)) {
            $this->output->warning("Already added");
        } else {
            $res = $this->cut("after", "/\* Package Service Providers/m", $content);
            $start = explode("\n", $res[0]);
            $end = explode("\n", $res[1]);

            $start[] = array_shift($end);
            $start[] = "        " . $insert . ",";
            file_put_contents($path, implode("\n", array_merge($start, $end)));
            $this->output->success("Jdlx\\Providers\\ResponseServiceProvider::class added to config/app.php");
        }

        $this->output->title("Adding api guard");
        $path = base_path() . "/config/auth.php";
        $content = file_get_contents($path);
        if (stristr($content, "'api' => [")) {
            $this->output->warning("Already added");
        } else {
            $res = $this->cut("after", "/\'provider\' => \'users\',/m", $content);
            $start = explode("\n", $res[0]);
            $end = explode("\n", $res[1]);

            $start[] = array_shift($end);
            $start[] = "        'api' => [";
            $start[] = "             'driver' => 'token',";
            $start[] = "             'provider' => 'users',";
            $start[] = "             'hash' => false,";
            $start[] = "         ]";

            file_put_contents($path, implode("\n", array_merge($start, $end)));
            $this->output->success("api guard added to config/auth.php");
        }

        $content = file_get_contents($path);

        // 'provider' => 'users',
        $this->output->title("Set support credentials to true");
        $path = base_path() . "/config/cors.php";
        $insert = "'supports_credentials' => true,";
        $content = file_get_contents($path);
        if (stristr($content, $insert)) {
            $this->output->warning("Already Set");
        } else {
            $content = str_replace( "'supports_credentials' => false,", $insert, $content);
            file_put_contents($path, $content);
            $this->output->success("Set $insert");
        }

        $this->output->title("Enable wildcards on spatie");
        $path = base_path() . "/config/permission.php";
        $insert = "'enable_wildcard_permission' => true";
        $content = file_get_contents($path);
        if (stristr($content, $insert)) {
            $this->output->warning("Already Set");
        } else {
            $content = str_replace( "'enable_wildcard_permission' => false", $insert, $content);
            file_put_contents($path, $content);
            $this->output->success("Set $insert");
        }
        $this->output->title("Update .gitignore");
        $this->addToGitignore("studio.json");

        $this->output->title("Set .env values");

        $this->updateEnvValue("SANCTUM_STATEFUL_DOMAINS", "*.local.me,localhost,localhost:8000,localhost:3000,127.0.0.1,127.0.0.1:8000,::1");
        $this->updateEnvValue("SESSION_SECURE_COOKIE", "false");
    }

    protected function validDatabase()
    {
        try {
            \DB::connection()->getPDO();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $order string before or after
     * @param $regexp
     * @param $content
     * @return array
     */
    public function cut(string $order, string $regexp, string $content): array
    {
        $lines = explode("\n", $content);
        $start = [];

        while (sizeof($lines) > 0) {
            $line = array_shift($lines);
            if (preg_match($regexp, $line)) {
                if ($order === "before") {
                    array_unshift($lines, $line);
                } else {
                    $start[] = $line;
                }
                break;
            } else {
                $start[] = $line;
            }
        }

        return [
            implode("\n", $start),
            implode("\n", $lines)
        ];
    }

    protected function updateEnvValue($key, $value, $force = false){

        $path = base_path() . "/.env";
        $content = file_get_contents($path);

        $insert = "$key=$value";
        if (stristr($content, "$key") && !$force) {
            $this->output->warning("$key is already set and will not be overwritten. \n Suggested value: \n  $insert");
        } else {
            $content .= "\n$insert";
            file_put_contents($path, $content);
            $this->output->success("Added '$insert' to .env");
        }

    }

    protected function addToGitignore($value){

        $path = base_path() . "/.gitignore";
        $content = file_get_contents($path);

        $insert = "$value";
        if (stristr($content, "$value")) {
            $this->output->warning("$value already set");
        } else {
            $content .= "\n$value";
            file_put_contents($path, $content);
            $this->output->success("Added '$value' to .gitignore");
        }

    }


}
