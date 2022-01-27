<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Exceptions\PermissionAlreadyExists;

class UserModelPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        $this->createGlobalPermissions();

        // Assign the created permissions to the Super Admin
        /** @var Role $admin */
        $admin = Role::where('name','Super Admin')->first();

        if ($admin) {
            $admin->givePermissionTo(Permission::all());
        } else {
            Role::create(['name' => 'Super Admin'])->givePermissionTo(Permission::all());
        }
    }

    public function createGlobalPermissions()
    {
        $permissions = array(
            ['name' => "user.create", 'guard_name' => 'api'],
            ['name' => "user.view.*", 'guard_name' => 'api'],
            ['name' => "user.update.*", 'guard_name' => 'api'],
            ['name' => "user.restore.*", 'guard_name' => 'api'],
            ['name' => "user.delete.*", 'guard_name' => 'api']
        );
        collect($permissions)->each(function (array $attributes) {
            try {
                $this->createPermission($attributes);
            } catch (PermissionAlreadyExists $permissionAlreadyExists) {
                Log::warning($permissionAlreadyExists->getMessage());
            }
        });
    }

    /**
     * @param array $attributes
     * @throws PermissionAlreadyExists
     */
    protected function createPermission(array $attributes) : void
    {
        Permission::create($attributes);
    }
}
