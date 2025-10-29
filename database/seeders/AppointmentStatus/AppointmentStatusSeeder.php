<?php

namespace Database\Seeders\AppointmentStatus;

use App\Models\AppointmentStatus;
use App\Enums\AppointmentStatus as AppointmentStatusEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppointmentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $rows = [
            ['slug' => AppointmentStatusEnum::SCHEDULED->value, 'title' => AppointmentStatusEnum::SCHEDULED->label(), 'created_at' => $now, 'updated_at' => $now],
            ['slug' => AppointmentStatusEnum::CANCELED->value,  'title' => AppointmentStatusEnum::CANCELED->label(),  'created_at' => $now, 'updated_at' => $now],
            ['slug' => AppointmentStatusEnum::COMPLETED->value, 'title' => AppointmentStatusEnum::COMPLETED->label(), 'created_at' => $now, 'updated_at' => $now],
            ['slug' => AppointmentStatusEnum::NO_SHOW->value,    'title' => AppointmentStatusEnum::NO_SHOW->label(),    'created_at' => $now, 'updated_at' => $now],
        ];

        AppointmentStatus::upsert($rows, ['slug'], ['title', 'updated_at']);
    }
}
