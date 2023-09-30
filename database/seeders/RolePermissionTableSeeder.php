<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class RolePermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // foreign key check disable
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // permission table truncate
        DB::table('permissions')->truncate();
        // role table truncate
        DB::table('roles')->truncate();
        // role permission table truncate
        DB::table('role_has_permissions')->truncate();
        // user permission table truncate
        DB::table('model_has_permissions')->truncate();
        // user role table truncate
        DB::table('model_has_roles')->truncate();
        // foreign key check enable
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $permissions = [
            'User' => [
                'user_management',
                'role_management',
                'permission_management',
            ],

        ];
        $roles       = [
            'User' => [
            ],
        ];

        $administrator = Role::create(['name' => 'Administrator']);
        foreach ($permissions as $group => $groups) {
            foreach ($groups as $permission) {
                Permission::create([
                    'name' => $permission,
                ])->assignRole($administrator);
            }
        }
        foreach ($roles as $role => $permissions) {
            $role = Role::create(['name' => $role]);
            $role->givePermissionTo($permissions);
        }
        $users = [
            [
                'name'              => 'IQBAL HASAN',
                'email'             => 'admin@gmail.com',
                'password'          => Hash::make('admin'),
                'email_verified_at' => now(),
                'status'            => 'Active',
                'role'              => 'Administrator',
            ],
            //  [
            //     'name'              => 'User',
            //     'email'             => 'user@gmail.com',
            //     'password'          => Hash::make('user'),
            //     'email_verified_at' => now(),
            //     'status'            => 'Active',
            //     'role'              => 'User',
            // ],
        ];
        foreach ($users as $userRaw) {
            $db_name = 'test_' . Str::random(10);
            //   find or create
            $user = User::create(
                [
                    'email'    => $userRaw['email'],
                    'name'     => $userRaw['name'],
                    'password' => $userRaw['password'],
                    'db_name'  => $db_name,
                ]
            );
            $user->assignRole($userRaw['role']);

            createDynamicDB($user, $db_name);

        }
    }
}