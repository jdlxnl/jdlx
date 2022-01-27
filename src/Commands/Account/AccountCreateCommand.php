<?php

namespace  Jdlx\Commands\Account;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class AccountCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account:create {email} {password} {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a user account';

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
        $password = $this->argument('password');
        $name = $this->argument('name') ?? "John Doe";

        $user = User::where('email', '=', $email)->first();

        if($user){
            $this->error("User Exists");
            return parent::FAILURE;
        }

        $fields = [
            'email'=>$email,
            'password'=>Hash::make($password),
            'name'=>$name
        ];

        $user = new User($fields);
        $user->save();

        $this->info("User created");

        return parent::SUCCESS;
    }
}
