<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // PERMISOS DE MODULOS DEL SISTEMA (visibilidad en el menu)
        Permission::create(['name' => 'menu.seguridad', 'guard_name' => 'web', 'description' => 'Visibilidad menú', 'module_key' => 'menu']);
        Permission::create(['name' => 'menu.catalogo', 'guard_name' => 'web', 'description' => 'Visibilidad menú', 'module_key' => 'menu']);

        // PERMISOS DE SEGURIDAD
        Permission::create(['name' => 'module.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'seg']);
        Permission::create(['name' => 'module.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'seg']);
        Permission::create(['name' => 'module.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'seg']);
        Permission::create(['name' => 'module.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'seg']);

        Permission::create(['name' => 'permission.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'seg']);
        Permission::create(['name' => 'permission.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'seg']);
        Permission::create(['name' => 'permission.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'seg']);
        Permission::create(['name' => 'permission.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'seg']);

        Permission::create(['name' => 'role.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'seg']);
        Permission::create(['name' => 'role.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'seg']);
        Permission::create(['name' => 'role.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'seg']);
        Permission::create(['name' => 'role.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'seg']);

        Permission::create(['name' => 'user.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'seg']);
        Permission::create(['name' => 'user.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'seg']);
        Permission::create(['name' => 'user.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'seg']);
        Permission::create(['name' => 'user.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'seg']);

        // CATALOGOS
        Permission::create(['name' => 'incidentType.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'cat']);
        Permission::create(['name' => 'incidentType.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'cat']);
        Permission::create(['name' => 'incidentType.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'cat']);
        Permission::create(['name' => 'incidentType.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'cat']);

        Permission::create(['name' => 'incident.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'inc']);
        // Permission::create(['name' => 'incident.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'inc']);
        Permission::create(['name' => 'incident.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'inc']);
        Permission::create(['name' => 'incident.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'inc']);
        Permission::create(['name' => 'incident.assign', 'guard_name' => 'web', 'description' => 'Asignar Registros', 'module_key' => 'inc']);
        Permission::create(['name' => 'incident.all', 'guard_name' => 'web', 'description' => 'Todos los Registros', 'module_key' => 'inc']);
    }
}
