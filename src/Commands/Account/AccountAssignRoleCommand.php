<?php

namespace Jdlx\Commands\Account;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class AccountAssignRoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account:assign:role {email} {role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign role to a existing user.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() : int
    {
        $email = $this->argument('email');
        $roleName  = $this->argument('role');

        /** @var HasRoles $user */
        $user = User::where('email', '=', $email)->first();

        if(!$user){
            $this->error("User not found!");
            return parent::FAILURE;
        }

        try {
            /** @var Role $role */
            $role = Role::findByName($roleName, 'api');
        } catch (RoleDoesNotExist $roleDoesNotExistException) {
            $this->error($roleDoesNotExistException->getMessage());
            return parent::FAILURE;
        }

        $user->assignRole($role);

        $this->info("Role '{$roleName}' assigned successfully!");

        return parent::SUCCESS;
    }


}
