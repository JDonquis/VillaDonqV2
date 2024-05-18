<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('departments')->insert
        (   
            
            [
                ['division_id' => '1','name' => 'Sin Asignar'],
                ['division_id' => '4','name' => 'Departamento de Protocolo y Evento'],
                ['division_id' => '12','name' => 'Departamento de Ingreso Reclutamiento y Selección'],
                ['division_id' => '12','name' => 'Departamento de  Clasificación y Remuneración'],
                ['division_id' => '12','name' => 'Departamento de Evaluación y Capacitación'],
                ['division_id' => '12','name' => 'Departamento de Archivo Laboral'],
                ['division_id' => '13','name' => 'Dpartamento de Nomina'],
                ['division_id' => '13','name' => 'Dpartamento de Egreso'],
                ['division_id' => '17','name' => 'Departamento de Investigación'],
                ['division_id' => '17','name' => 'Departamento de  Perfeccionamiento Profesional y Capacitación'],
                ['division_id' => '17','name' => 'Departamento de  Gestión Académica'],
                ['division_id' => '18','name' => 'Departamento de Pre-grado y Post-grado de Medicina'],
                ['division_id' => '18','name' => 'Departamento de Pre-grado y Post-grado de enfermería y otros Gremios'],
                ['division_id' => '19','name' => 'Departamento de  Medico Integrales Comunitarios (MIC) y Médicos Rurales'],
                ['division_id' => '19','name' => 'Departamento  de  Formación de Recurso  Humano']
            ]
          );
        
    }
}
