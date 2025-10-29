<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\Appointment\AppointmentSeeder;
use Database\Seeders\AppointmentStatus\AppointmentStatusSeeder;
use Database\Seeders\Patient\PatientSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            AppointmentStatusSeeder::class,
            PatientSeeder::class,
            AppointmentSeeder::class,
        ]);
    }
}
