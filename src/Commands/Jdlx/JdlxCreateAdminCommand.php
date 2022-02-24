<?php


namespace Jdlx\Commands\Jdlx;


use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class JdlxCreateAdminCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'jdlx:create_admin';

    protected $signature = 'jdlx:create_admin {email} {password} {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create and admin account';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() : int
    {
        $email = $this->argument('email');
        $user = User::where('email', '=', $email)->first();
        $admin = Role::where('name', 'Super Admin')->first();

        if (empty($admin)) {
            $this->error("Super Admin doesn't exist, did you run: php artisan db:seed JdlxInstallCommand");
            return parent::FAILURE;
        }

        if (empty($user)) {
            $this->call('account:create', $this->input->getArguments());
            $user = User::where('email', '=', $email)->first();
        }

        $this->call('account:assign:role', ["email" => $email, "role" => "Super Admin"]);

        return parent::SUCCESS;
    }
}
