<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\Neighborhood;
use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\SimpleExcel\SimpleExcelReader;
use Maatwebsite\Excel\Facades\Excel;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ini_set('memory_limit', '-1');

        $this->states();
        $this->citiesAndNeighborhood();
    }

    private function states()
    {
        State::insert([
            ['name' => 'Aguascalientes', 'code' => '01', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Baja California', 'code' => '02', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Baja California Sur', 'code' => '03', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Campeche', 'code' => '04', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Chiapas', 'code' => '05', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Chihuahua', 'code' => '06', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Coahuila', 'code' => '07', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Colima', 'code' => '08', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Ciudad de México', 'code' => '09', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Durango', 'code' => '10', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Guanajuato', 'code' => '11', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Guerrero', 'code' => '12', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Hidalgo', 'code' => '13', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Jalisco', 'code' => '14', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'México', 'code' => '15', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Michoacán', 'code' => '16', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Morelos', 'code' => '17', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Nayarit', 'code' => '18', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Nuevo León', 'code' => '19', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Oaxaca', 'code' => '20', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Puebla', 'code' => '21', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Querétaro', 'code' => '22', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Quintana Roo', 'code' => '23', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'San Luis Potosí', 'code' => '24', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Sinaloa', 'code' => '25', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Sonora', 'code' => '26', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Tabasco', 'code' => '27', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Tamaulipas', 'code' => '28', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Tlaxcala', 'code' => '29', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Veracruz', 'code' => '30', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Yucatán', 'code' => '31', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Zacatecas', 'code' => '32', 'created_at' => date('Y-m-d H:i:s')]
        ]);
    }

    private function citiesAndNeighborhood()
    {
        // $reader = SimpleExcelReader::create(database_path('seeders/files/CPdescarga.xlsx')); // Asegúrate de que el archivo sea .xlsx

        // for ($sheetIndex = 1; $sheetIndex <= 32; $sheetIndex++) {
        //     $rows = $reader->fromSheet($sheetIndex)->getRows();

        //     $rows->each(function (array $row) {
        //         if (!isset($row['D_mnpio']) || empty($row['D_mnpio'])) {
        //             return;
        //         }
        //         $city = City::firstOrCreate([
        //             'name' => trim($row['D_mnpio']),
        //             'code' => trim($row['c_mnpio']),
        //             'state_id' => trim($row['c_estado']),
        //         ]);
        //         Neighborhood::firstOrCreate([
        //             'name' => trim($row['d_asenta']),
        //             'postal_code' => trim($row['d_codigo']),
        //             'city_id' => $city->id,
        //         ]);
        //     });
        // }
        $doc = glob('database/seeders/Files/CPdescarga.xls');
        $data = Excel::toArray([], $doc[0]);

        for ($i = 0; $i < 32; $i++) {
            $skipHeader = true;
            foreach ($data[$i] as $row) {
                if ($skipHeader) {
                    $skipHeader = false;
                    continue; // Saltar la primera fila (encabezado)
                }

                // Crear o buscar el municipio
                if ($row[3] == null || $row[7] == null || $row[11] == null || $row[12] == null || $row[0] == null || $row[1] == null) {
                    break;
                }

                $city = City::firstOrCreate([
                    'name' => $row[3],
                    'code' => $row[11],
                    'state_id' => $row[7],
                ]);

                // Crear la colonia y relacionarla con el municipio
                $neighborhood = new Neighborhood();
                $neighborhood->code = $row[12];
                $neighborhood->postal_code = $row[0];
                $neighborhood->name = $row[1];
                $neighborhood->city_id = $city->id;
                $neighborhood->save();
            }
        }
    }
}
