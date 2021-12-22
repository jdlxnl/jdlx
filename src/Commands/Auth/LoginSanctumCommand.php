<?php

namespace  Jdlx\Commands\Auth;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class LoginSanctumCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:login:sanctum {email} {password} {--device=cli} {--server=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get token for account';

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
    public function handle(): mixed
    {
        //$url = $request->get('url', null);
        $email = $this->argument('email');
        $password = $this->argument('password');
        $server = $this->option('server');
        $device = $this->option('device');

        if (!$server) {
            $user = User::where('email', $email)->first();

            if (!$user || !Hash::check($password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            $this->info($user->createToken($device)->plainTextToken);
            return 0;
        }

        $body = [
            'email' => $email,
            'password' => $password,
            'device_name' => $device
        ];


        $url = "${server}/api/auth/login";
        $this->info("Requesting tokens from ${url}");

        $response = Http::withoutVerifying()->post($url, $body);
        $json = json_decode($response->getBody());
        dump($json);
        return 0;
    }
}
