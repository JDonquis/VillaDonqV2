<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('divisions')->insert
        (
            [
                ['office_id' => '1','name' => 'Sin Asignar'],
                ['office_id' => '3','name' => 'División  de Audiovisual'],
                ['office_id' => '3','name' => 'División de Prensa'],
                ['office_id' => '3','name' => 'División de Relaciones División Publicas'],
                ['office_id' => '4','name' => 'División de Planificación para la Salud'],
                ['office_id' => '4','name' => 'División de Seguimiento y Evaluación de Gestión'],
                ['office_id' => '4','name' => 'División de Organización y Métodos'],
                ['office_id' => '5','name' => 'División de Inspección Sanitaria a nivel de  Distritos'],
                ['office_id' => '5','name' => 'División  de Educación'],
                ['office_id' => '7','name' => 'División de Seguimiento y Control'],
                ['office_id' => '7','name' => 'División de Bienestar Social'],
                ['office_id' => '7','name' => 'División  Técnica'],
                ['office_id' => '7','name' => 'División de Apoyo Administrativo'],
                ['office_id' => '7','name' => 'División de Salud y Seguridad Laboral'],
                ['office_id' => '8','name' => 'División  de Sistema y Soporte a Usuarios'],
                ['office_id' => '8','name' => 'División Area Técnica'],
                ['office_id' => '9','name' => 'División de Investigación, Perfeccionamiento Profesional y Capacitación'],
                ['office_id' => '9','name' => 'División de Pre-grado y Post-grado'],
                ['office_id' => '9','name' => 'División del Observatorio del Recurso Humano'],
                ['office_id' => '10','name' => 'División de Promoción y Gestión Social'],
                ['office_id' => '10','name' => 'División  de Atención Gerontológica'],
                ['office_id' => '10','name' => 'División  de Comités Bolivarianos de Salud'],
                ['office_id' => '11','name' => 'División de Epidemiologia'],
                ['office_id' => '11','name' => 'División de Programas de Salud'],
                ['office_id' => '11','name' => 'División de Salud Ambiental']
            ]
        );
    }
}
