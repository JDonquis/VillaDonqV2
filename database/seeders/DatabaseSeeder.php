<?php

namespace Database\Seeders;

use DB;

use Illuminate\Database\Seeder;
use Database\Seeders\QuotaSeeder;
use Database\Seeders\PaymentMethodSeeder;
use Database\Seeders\AccountPaymentSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        $this->truncateTable([

            'payment_methods',
            'account_payments',
            'courses',
            'sections',
            'course_sections',
            'request_status',
            'type_documents',
            'type_users',
            'users',
            'main_configs',
            'school_lapses',
            'quotas',


        ]);

        $this->call([

            PaymentMethodSeeder::class,
            AccountPaymentSeeder::class,
            CourseSeeder::class,
            SectionSeeder::class,
            CourseSectionSeeder::class,
            RequestStatusSeeder::class,
            TypeUserSeeder::class,
            UserSeeder::class,
            TypeDocumentSeeder::class,
            MainConfigSeeder::class,
            SchoolLapseSeeder::class,
            QuotaSeeder::class,
            
        ]);
    }

    protected function truncateTable(array $tables){

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        foreach ($tables as $table)
        {
            DB::table($table)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

    }
}
