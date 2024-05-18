<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([

            TypeUserSeeder::class,
            AreaSeeder::class,
            TypeActivitySeeder::class,
            StatusSeeder::class,
            LocationSeeder::class,
            OfficeSeeder::class,
            DivisionSeeder::class,
            DepartmentSeeder::class,
            UserSeeder::class,
            ModuleSeeder::class,
            UserModuleSeeder::class,
            ActivitySeeder::class,

        ]);
        
    }
}
