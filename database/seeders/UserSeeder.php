<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::create(['name' => 'Admin', 'description' => 'Administrador']);
        $gerente = Role::create(['name' => 'Gerente', 'description' => 'Gerente']);
        $subgerente = Role::create(['name' => 'SubGerente', 'description' => 'SubGerente']);
        $visualizador = Role::create(['name' => 'Visualizador', 'description' => 'Visualizador']);

        User::insert([
           [
            'name'              => 'Administrador',
            'email'             => 'vitervo.lc@cenidet.tecnm.mx',
            'password'          => Hash::make('Password'),
            'email_verified_at' => now(),
            'created_at'        => now()
           ],
           [
            'name'              => 'Gerente',
            'email'             => 'veranocientifico@cenidet.tecnm.mx',
            'password'          => Hash::make('Password'),
            'email_verified_at' => now(),
            'created_at'        => now()
           ],
           [
            'name'              => 'Subgerente',
            'email'             => 'vitervo@cenidet.edu.mx',
            'password'          => Hash::make('Password'),
            'email_verified_at' => now(),
            'created_at'        => now()
           ],
           [
            'name'              => 'Visualizador',
            'email'             => 'vitervolopezcaballero@gmail.com',
            'password'          => Hash::make('Password'),
            'email_verified_at' => now(),
            'created_at'        => now()
           ]
        ]);

        User::find(1)->assignRole($admin);
        User::find(2)->assignRole($gerente);
        User::find(3)->assignRole($subgerente);
        User::find(4)->assignRole($visualizador);
    }
}
