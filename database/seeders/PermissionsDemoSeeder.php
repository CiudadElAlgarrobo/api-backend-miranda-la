<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsDemoSeeder extends Seeder
{
    /**
     * Create the initial roles and permissions.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['guard_name' => 'api', 'name' => 'edit articles']);
        Permission::create(['guard_name' => 'api', 'name' => 'delete articles']);
        Permission::create(['guard_name' => 'api', 'name' => 'publish articles']);
        Permission::create(['guard_name' => 'api', 'name' => 'unpublish articles']);

        Permission::create(['guard_name' => 'api', 'name' => 'register_role']);
        Permission::create(['guard_name' => 'api', 'name' => 'update_role']);
        Permission::create(['guard_name' => 'api', 'name' => 'delete_role']);
        Permission::create(['guard_name' => 'api', 'name' => 'list_role']);

        Permission::create(['guard_name' => 'api', 'name' => 'register_user']);
        Permission::create(['guard_name' => 'api', 'name' => 'update_user']);
        Permission::create(['guard_name' => 'api', 'name' => 'delete_user']);
        Permission::create(['guard_name' => 'api', 'name' => 'list_user']);


        // create roles and assign existing permissions
        $role1 = Role::create(['guard_name' => 'api', 'name' => 'writer']);
        $role1->givePermissionTo('edit articles');
        $role1->givePermissionTo('delete articles');

        $role2 = Role::create(['guard_name' => 'api', 'name' => 'admin']);
        $role2->givePermissionTo('publish articles');
        $role2->givePermissionTo('unpublish articles');

        $role3 = Role::create(['guard_name' => 'api', 'name' => 'Super-Admin']);
        // gets all permissions via Gate::before rule; see AuthServiceProvider

        // create demo users
        $user = \App\Models\User::factory()->create([
            'name' => 'Example User',
            'email' => 'tester@example.com',
            'password' => bcrypt('1234567800'),
        ]);
        $user->assignRole($role1);

        $user = \App\Models\User::factory()->create([
            'name' => 'Example Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('1234567800'),
        ]);
        $user->assignRole($role2);

        $user = \App\Models\User::factory()->create([
            'name' => 'Example Super-Admin User',
            'email' => 'devcode1@gmail.com',
            'password' => bcrypt('1234567800'),
        ]);
        $user->assignRole($role3);
    }
}
