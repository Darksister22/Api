<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SemesterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'isEnded'=>false,
            'number'=>'second',
            'year'=>'2021-2022',
        ];
    }
}
