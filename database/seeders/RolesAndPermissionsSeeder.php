<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            ['name' => 'view activity logs','guard_name' => 'web'],
            ['name' => 'create activity log','guard_name' => 'web'],
            ['name' => 'update activity log','guard_name' => 'web'],
            ['name' => 'delete activity log','guard_name' => 'web'],
            ['name' => 'view users','guard_name' => 'web'],
            ['name' => 'create user','guard_name' => 'web'],
            ['name' => 'update user','guard_name' => 'web'],
            ['name' => 'delete user','guard_name' => 'web'],
            ['name'=> 'view roles','guard_name' => 'web'],
            ['name'=> 'create role','guard_name' => 'web'],
            ['name'=> 'update role','guard_name' => 'web'],
            ['name'=> 'delete role','guard_name' => 'web'],
            ['name' => 'view governorates','guard_name' => 'web'],
            ['name' => 'create governorate','guard_name' => 'web'],
            ['name' => 'update governorate','guard_name' => 'web'],
            ['name' => 'delete governorate','guard_name' => 'web'],
            ['name'=> 'view cities','guard_name' => 'web'],
            ['name'=> 'create city','guard_name' => 'web'],
            ['name'=> 'update city','guard_name' => 'web'],
            ['name'=> 'delete city','guard_name' => 'web'],
            ['name'=> 'view areas','guard_name' => 'web'],
            ['name'=> 'create area','guard_name' => 'web'],
            ['name'=> 'update area','guard_name' => 'web'],
            ['name'=> 'delete area','guard_name' => 'web'],
            ['name'=> 'view areas groups','guard_name' => 'web'],
            ['name'=> 'create areas group','guard_name' => 'web'],
            ['name'=> 'update areas group','guard_name' => 'web'],
            ['name'=> 'delete areas group','guard_name' => 'web'],
            ['name'=> 'view departments','guard_name' => 'web'],
            ['name'=> 'create department','guard_name' => 'web'],
            ['name'=> 'update department','guard_name' => 'web'],
            ['name'=> 'delete department','guard_name' => 'web'],
            ['name'=> 'view employees','guard_name' => 'web'],
            ['name'=> 'create employee','guard_name' => 'web'],
            ['name'=> 'update employee','guard_name' => 'web'],
            ['name'=> 'delete employee','guard_name' => 'web'],
            ['name'=> 'view donors','guard_name' => 'web'],
            ['name'=> 'create donor','guard_name' => 'web'],
            ['name'=> 'update donor','guard_name' => 'web'],
            ['name'=> 'delete donor','guard_name' => 'web'],
            ['name'=> 'view donation categories','guard_name' => 'web'],
            ['name'=> 'create donation category','guard_name' => 'web'],
            ['name'=> 'update donation category','guard_name' => 'web'],
            ['name'=> 'delete donation category','guard_name' => 'web'],
            ['name'=> 'view monthly forms','guard_name' => 'web'],
            ['name'=> 'create monthly form','guard_name' => 'web'],
            ['name'=> 'update monthly form','guard_name' => 'web'],
            ['name'=> 'delete monthly form','guard_name' => 'web'],
            ['name'=> 'view donations','guard_name' => 'web'],
            ['name'=> 'create donation','guard_name' => 'web'],
            ['name'=> 'update donation','guard_name' => 'web'],
            ['name'=> 'delete donation','guard_name' => 'web'],
            ['name'=> 'view gathered donations','guard_name' => 'web'],
            ['name'=> 'create gathered donation','guard_name' => 'web'],
            ['name'=> 'update gathered donation','guard_name' => 'web'],
            ['name'=> 'delete gathered donation','guard_name' => 'web'],
            ['name'=> 'view collecting lines','guard_name' => 'web'],
            ['name'=> 'create collecting line','guard_name' => 'web'],
            ['name'=> 'update collecting line','guard_name' => 'web'],
            ['name'=> 'delete collecting line','guard_name' => 'web'],
            ['name'=> 'view call types','guard_name' => 'web'],
            ['name'=> 'create call type','guard_name' => 'web'],
            ['name'=> 'update call type','guard_name' => 'web'],
            ['name'=> 'delete call type','guard_name' => 'web'],
        ];
        // Create permissions
        Permission::insert($permissions);

        // Create roles and assign existing permissions
        $role = Role::create(['name' => 'user']);
        $role->givePermissionTo(['name'=> 'create user']);

        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(Permission::all());
    }
}
