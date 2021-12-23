# Package
This packages offers a set of tools to support a REST API.
It is very biased and does not offer much in the way of configuration.

It can:
- Outputs all responses according to API Responses: https://google.github.io/styleguide/jsoncstyleguide.xml
- Generate swagger documentation, by compiling JSON files in a documentation folder
- Assumes sanctum and spatie wildcard permissions

## Installation
Add the package by loading it through composer.

```shell
composer require jdlxnl/jdlx
```


Run the following commands to setup the database
```shell
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Jdlx\JdlxServiceProvider"

## By default files won't be overwritten. This is good for existing intallations
## For new installation use force to overwrite the following:
##  - router/api.php
##  - app/Models/User.php
##  - app/Exception/Handler.php
##
php artisan vendor:publish --provider="Jdlx\JdlxServiceProvider" --force

php artisan migrate
```

Generate the User scaffold
```shell
php artisan api:scaffold User
```

Add to `config/app.php` to enable formatted responses
```php
    /*
     * Package Service Providers...
     */
     Jdlx\Providers\ResponseServiceProvider::class,
```

Update to `config/permission.php` to allow wildcards
```php
    'enable_wildcard_permission' => true,
```

Add to `.env`
```
SANCTUM_STATEFUL_DOMAINS=*.local.me,localhost,localhost:8000,localhost:3000,127.0.0.1,127.0.0.1:8000,::1
SESSION_SECURE_COOKIE=false
```

###### Tips and tricks
- In JetBreans mark the `publish` folder as excluded to prevent clashes
