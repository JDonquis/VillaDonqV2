<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('offices')->insert
        (   [
                ['name' => 'Sin Asignar'],
                ['name' => 'Oficina Direccion General'],
                ['name' => 'Oficina de Comunicación y Relaciones Institucionales'],
                ['name' => 'Oficina de Planificación, Control de Gestión y Organización'],
                ['name' => 'Oficina de Inspección de Salud Publica'],
                ['name' => 'Asesoría Jurídica'],
                ['name' => 'Oficina de Recursos Humanos'],
                ['name' => 'Oficina de Sistema y Tecnología de la Información'],
                ['name' => 'Oficina de Investigación y Educación'],
                ['name' => 'Dirección de Promoción y Participación Popular en Salud'],
                ['name' => 'Dirección de Salud Colectiva'],
                ['name' => 'Dirección de Servicios de Salud'],
                ['name' => 'Dirección de Recursos para la Salud'],
                ['name' => 'Oficina de Gestión Administrativa y de Finanzas']
            ]
        );
    }
}
