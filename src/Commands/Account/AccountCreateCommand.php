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
     * @return mixed
     */
    public function handle()
    {
        //$url = $request->get('url', null);
        $email = $this->argument('email');
        $password = $this->argument('password');
        $name = $this->argument('name') ?? "John Doe";


        $user = User::where('email', $email)->first();

        if($user){
            $this->error("User Exists");
            return -1;
        }

        $fields = [
            'email'=>$email,
            'password'=>\Illuminate\Support\Facades\Hash::make($password),
            'name'=>$name
        ];

        $user = new User($fields);
        $user->save();

        $this->info("User created");
        return 0;
    }
}
