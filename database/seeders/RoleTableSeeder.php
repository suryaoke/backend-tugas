<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'lihat']);
        Permission::create(['name' => 'tambah']);
        Permission::create(['name' => 'edit']);
        Permission::create(['name' => 'hapus']);


        Permission::create(['name' => 'history']);

        Role::create(['name' => 'admin', 'guard_name' => 'api']);
        Role::create(['name' => 'user', 'guard_name' => 'api']);

        $roelAdmin = Role::findByName('admin');
        $roelAdmin->givePermissionTo('tambah');
        $roelAdmin->givePermissionTo('edit');
        $roelAdmin->givePermissionTo('hapus');
        $roelAdmin->givePermissionTo('lihat');


        $roleuser = Role::findByName('user');
        $roleuser->givePermissionTo('history');
    }
}
