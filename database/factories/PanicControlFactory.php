<?php

namespace PanicControl\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use PanicControl\Models\PanicControl;

class PanicControlFactory extends Factory
{
    protected $model = PanicControl::class;

    public function definition()
    {
        return [
            'name' => Str::snake($this->faker->name),
            'description' => $this->faker->text,
            'status' => $this->faker->boolean(),
            'rules' => [],
        ];
    }
}
