<?php

namespace Database\Factories;

use App\Models\Political;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PoliticalFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Political::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->political(),
            'user_id' => User::factory(),
            'personal_portal' => true,
        ];
    }
}
