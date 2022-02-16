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
    protected $name = 'jdlx:install';

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

        $this->output->title("Publishing Vendor Packages");
        $this->output->info("Sanctum");
        $this->call('vendor:publish', ['--provider' => 'Laravel\Sanctum\SanctumServiceProvider']);
        $this->output->info("Spatie");
        $this->call('vendor:publish', ['--provider' => 'Spatie\Permission\PermissionServiceProvider']);

        ## config option
        $this->output->info("JDLX");
        $this->call('vendor:publish', ['--provider' => 'Jdlx\JdlxServiceProvider', "--force" => true]);

        $this->output->title("Run the migration");
        $this->call('migrate');

        // $this->call('api:scaffold', ["User"]);

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

        $this->output->title("Adding wildcard permissions to spatie");

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

}
