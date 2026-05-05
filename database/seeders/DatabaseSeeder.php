<?php

namespace Database\Seeders;

use App\Models\SchoolLapse;
use DB;
use Illuminate\Database\Seeder;

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

            'students',
            'representatives',
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
            StudentSeeder::class,

        ]);

        // SchoolLapse::where('status', 1)->update(['status' => 0]);

        // SchoolLapse::create([
        //     'start' => '2027-09-01',
        //     'end' => '2028-08-31',
        //     'status' => 1,
        // ]);
    }

    protected function truncateTable(array $tables)
    {

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
