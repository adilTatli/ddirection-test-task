<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = $this->faker->randomElement(['male', 'female']);

        $firstName = $this->faker->firstName($gender);
        $lastName  = $this->faker->lastName($gender);

        return [
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'gender'     => $gender,
            'birth_date' => $this->faker->dateTimeBetween('-85 years', '-18 years')->format('Y-m-d'),
        ];
    }

    public function male(): static
    {
        return $this->state(fn () => [
            'gender'     => 'male',
            'first_name' => $this->faker->firstName('male'),
            'last_name'  => $this->faker->lastName('male'),
        ]);
    }

    public function female(): static
    {
        return $this->state(fn () => [
            'gender'     => 'female',
            'first_name' => $this->faker->firstName('female'),
            'last_name'  => $this->faker->lastName('female'),
        ]);
    }
}
