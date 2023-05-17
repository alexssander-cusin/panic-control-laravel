<?php

namespace PanicControl\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PanicControl\Models\PanicControl;
use Illuminate\Support\Str;

class PanicControlFactory extends Factory
{
    protected $model = PanicControl::class;

    public function definition()
    {
        return [
            'service' => Str::snake($this->faker->name),
            'description' => $this->faker->text,
            'status' => $this->faker->boolean(),
            'category_id' => $this->faker->numberBetween(1, 10), //TODO: create a category factory
        ];
    }
}
