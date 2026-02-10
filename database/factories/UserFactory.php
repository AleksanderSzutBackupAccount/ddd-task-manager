<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Src\Identity\Domain\ValueObjects\UserId;
use Src\Identity\Infrastructure\Laravel\Models\UserModel;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<UserModel>
 */
class UserFactory extends Factory
{
    protected $model = UserModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => UserId::generate()->value(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'external_id' => (string) fake()->randomNumber(5),
        ];
    }
}
