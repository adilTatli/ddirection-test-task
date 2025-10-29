<?php

namespace Database\Seeders\Patient;

use App\Models\Patient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $count = (int) (env('PATIENTS_SEED_COUNT', 50));
        Patient::factory()->count($count)->create();
    }
}
