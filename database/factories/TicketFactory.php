<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'subject'    => $this->faker->sentence,
            'content'    => $this->faker->text,
            'user_name'  => $this->faker->name,
            'user_email' => $this->faker->email,
            'status'     => $this->faker->boolean
        ];
    }

    public function unprocessed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 0
            ];
        });
    }

    public function processed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 1
            ];
        });
    }
}
