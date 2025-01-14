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
            ['name' => 'view users','guard_name' => 'web'],
            ['name' => 'create users','guard_name' => 'web'],
            ['name' => 'update users','guard_name' => 'web'],
            ['name' => 'delete users','guard_name' => 'web'],
            ['name'=> 'view roles','guard_name' => 'web'],
            ['name'=> 'create roles','guard_name' => 'web'],
            ['name'=> 'update roles','guard_name' => 'web'],
            ['name'=> 'delete roles','guard_name' => 'web'],
            ['name' => 'view governorates','guard_name' => 'web'],
            ['name' => 'create governorates','guard_name' => 'web'],
            ['name' => 'update governorates','guard_name' => 'web'],
            ['name' => 'delete governorates','guard_name' => 'web'],
            ['name'=> 'view cities','guard_name' => 'web'],
            ['name'=> 'create cities','guard_name' => 'web'],
            ['name'=> 'update cities','guard_name' => 'web'],
            ['name'=> 'delete cities','guard_name' => 'web'],
            ['name'=> 'view areas','guard_name' => 'web'],
            ['name'=> 'create areas','guard_name' => 'web'],
            ['name'=> 'update areas','guard_name' => 'web'],
            ['name'=> 'delete areas','guard_name' => 'web'],
            ['name'=> 'view departments','guard_name' => 'web'],
            ['name'=> 'create departments','guard_name' => 'web'],
            ['name'=> 'update departments','guard_name' => 'web'],
            ['name'=> 'delete departments','guard_name' => 'web'],
            ['name'=> 'view employees','guard_name' => 'web'],
            ['name'=> 'create employees','guard_name' => 'web'],
            ['name'=> 'update employees','guard_name' => 'web'],
            ['name'=> 'delete employees','guard_name' => 'web'],
            ['name'=> 'view donors','guard_name' => 'web'],
            ['name'=> 'create donors','guard_name' => 'web'],
            ['name'=> 'update donors','guard_name' => 'web'],
            ['name'=> 'delete donors','guard_name' => 'web'],
            ['name'=> 'view donation categories','guard_name' => 'web'],
            ['name'=> 'create donation categories','guard_name' => 'web'],
            ['name'=> 'update donation categories','guard_name' => 'web'],
            ['name'=> 'delete donation categories','guard_name' => 'web'],
            ['name'=> 'view monthly donations','guard_name' => 'web'],
            ['name'=> 'create monthly donations','guard_name' => 'web'],
            ['name'=> 'update monthly donations','guard_name' => 'web'],
            ['name'=> 'delete monthly donations','guard_name' => 'web'],
            ['name'=> 'view donations','guard_name' => 'web'],
            ['name'=> 'create donations','guard_name' => 'web'],
            ['name'=> 'update donations','guard_name' => 'web'],
            ['name'=> 'delete donations','guard_name' => 'web'],
            ['name'=> 'view call types','guard_name' => 'web'],
            ['name'=> 'create call types','guard_name' => 'web'],
            ['name'=> 'update call types','guard_name' => 'web'],
            ['name'=> 'delete call types','guard_name' => 'web'],
        ];
        // Create permissions
        Permission::insert($permissions);

        // Create roles and assign existing permissions
        $role = Role::create(['name' => 'user']);
        $role->givePermissionTo(['name'=> 'create users']);

        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(Permission::all());
    }
}
