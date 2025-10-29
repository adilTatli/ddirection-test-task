<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id'     => Patient::query()->inRandomOrder()->value('id') ?? Patient::factory(),
            'doctor_name'    => $this->faker->randomElement(['Иванов П.П.','Сидорова А.А.','Петров К.К.','Морозов Л.Н.']),
            'specialization' => $this->faker->randomElement(['Терапевт','Кардиолог','ЛОР','Невролог']),
            'date_time'      => $this->faker->dateTimeBetween('+1 day 09:00', '+5 days 17:00')->format('Y-m-d H:i:00'),
        ];
    }
}
