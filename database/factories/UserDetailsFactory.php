<?php

namespace Database\Factories;

use App\Models\UserDetails;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserDetailsFactory extends Factory
{
    protected $model = UserDetails::class;

    public function definition()
    {
        return [
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'address' => $this->faker->address,
        ];
    }
}
