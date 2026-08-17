<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
            'permissions.view',
            'activity-logs.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // super_admin — buong kontrol sa system
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions(Permission::all());

        // admin — namamahala ng moderator at user accounts
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions([
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'roles.view',
            'permissions.view',
            'activity-logs.view',
        ]);

        // moderator — view-only, para sa pag-monitor ng users
        $moderator = Role::firstOrCreate(['name' => 'moderator']);
        $moderator->syncPermissions([
            'users.view',
            'activity-logs.view',
        ]);

        // user — regular end-user, walang access sa admin panel
        Role::firstOrCreate(['name' => 'user']);

        $this->makeUser('Super Admin', 'superadmin@example.com', 'super_admin');
        $this->makeUser('Administrator', 'admin@example.com', 'admin');
        $this->makeUser('Moderator', 'moderator@example.com', 'moderator');
        $this->makeUser('Regular User', 'user@example.com', 'user');
    }

    private function makeUser(string $name, string $email, string $role): void
    {
        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => bcrypt('password')]
        );

        $user->assignRole($role);
    }
}
