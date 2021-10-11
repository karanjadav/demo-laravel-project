<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AssignUserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = [
            'name'  =>  'Karan',
            'email' =>  'admin@admin.com',
            'password' => Hash::make(123456)
        ];

        $admin = User::updateOrCreate(['email' => 'admin@admin.com'], $admin);
        $roles = [
            'admin',
            'user'
        ];
        $adminPermission = [
            "user.index",
            "user.create",
            "user.store",
            "user.show",
            "user.edit",
            "user.update",
            "user.destroy"
        ];
        $routeList = Route::getRoutes();
        foreach($roles as $roleName) {
            $role = Role::updateOrcreate(['name' => $roleName],['name' => $roleName]);
            foreach ($routeList as $route) {
                if(!empty($route->getName())) {
                    $routeName = $route->getName();
                    $permission = Permission::updateOrcreate(['name' => $routeName],['name' => $routeName]);
                    if($roleName == 'admin') {
                        $admin->assignRole('admin');
                        $role->givePermissionTo($permission);
                    } elseif(!(in_array($routeName, $adminPermission) ||
                            preg_match('/destroy|update/i', $routeName))) {
                        $role->givePermissionTo($permission);
                    }
                }
            }
        }
    }
}
