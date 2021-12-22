<?php
// Place this file on the Providers folder of your project
namespace Jdlx\Providers;

use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\ServiceProvider;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(ResponseFactory $factory)
    {
        $factory->macro('success', function ($data = null, $code = 200) use ($factory) {
            $format = [
                'data' => $data,
            ];
            return $factory->make($format, $code);
        });

        $factory->macro('error', function ($code, string $message = '', $errors = []) use ($factory) {
            $format = [
                'error' => [
                    'code' => $code,
                    'message' => $message
                ]
            ];

            if (sizeof($errors)) {
                $format['error']['errors'] = $errors;
            }

            return $factory->make($format, $code);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
