<?php

namespace Database\Factories;

use Alirezasedghi\LaravelImageFaker\ImageFaker;
use Alirezasedghi\LaravelImageFaker\Services\Picsum;
use App\Models\Profil;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Profil>
 */
class ProfilFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $imageFaker = new ImageFaker(new Picsum());

        return [
            'firstname' => fake()->firstName,
            'lastname' => fake()->lastName,
            'status' => getProfilStatus(fake()->numberBetween(0, 2)),
            'picture' => $imageFaker->imageUrl(400, 400)
        ];
    }

    public function withUser($userId): static
    {
        return $this->state(function (array $attributes) use ($userId) {
            return [
                'user_id' => $userId
            ];
        });
    }

}
