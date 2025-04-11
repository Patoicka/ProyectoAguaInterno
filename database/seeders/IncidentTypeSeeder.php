<?php

namespace Database\Seeders;

use App\Models\Incident;
use App\Models\IncidentStatus;
use App\Models\IncidentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IncidentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        IncidentType::insert([
            [
                'name' => 'Falta de agua',
                'description' => 'Fuga de agua',
                'status' => true,
            ],
            [
                'name' => 'Fuga',
                'description' => 'Fuga',
                'status' => true,
            ],
            [
                'name' => 'Mala calidad del agua',
                'description' => 'Mala calidad del agua',
                'status' => true,
            ],
            [
                'name' => 'Mal uso',
                'description' => 'Mal uso',
                'status' => true,
            ],
            [
                'name' => 'Falta de tapa en caja de valvula',
                'description' => 'Falta de tapa en caja de valvula',
                'status' => true,
            ],
            [
                'name' => 'Solicitud de pipa',
                'description' => 'Solicitud de pipa',
                'status' => true,
            ],
            [
                'name' => 'Huachicol',
                'description' => 'Huachicol',
                'status' => true,
            ],
            [
                'name' => 'Brote de aguas negras',
                'description' => 'Brote de aguas negras',
                'status' => true,
            ],
            [
                'name' => 'Encharcamiento',
                'description' => 'Encharcamiento',
                'status' => true,
            ],
            [
                'name' => 'Coladera sin tapa',
                'description' => 'Coladera sin tapa',
                'status' => true,
            ],
            [
                'name' => 'Socavón',
                'description' => 'Socavón',
                'status' => true,
            ],

        ]);

        IncidentStatus::insert([
            [
                'name' => 'Enviada a revisión',
                'class' => 'border border-yellow-400 dark:bg-yellow-800 dark:text-yellow-200 bg-yellow-50 text-yellow-500 rounded-lg px-2 dark:opacity-95 w-full text-center',
            ],
            [
               'name' => 'En proceso de atención',
                'class' => 'border border-blue-600 dark:bg-blue-800 dark:text-blue-200 bg-blue-100 text-blue-500 rounded-lg px-2 dark:opacity-95 w-full text-center',
            ],
            [
                'name' => 'Incidencia resuelta',
                'class' => 'border border-green-600 dark:bg-green-800 dark:text-green-200 bg-green-100 text-green-500 rounded-lg px-2 dark:opacity-95 w-full text-center',
            ]
        ]);
    }
}
