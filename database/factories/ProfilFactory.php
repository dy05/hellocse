<?php

namespace Database\Factories;

use Alirezasedghi\LaravelImageFaker\Services\Picsum;
use App\Models\Profil;
use Illuminate\Database\Eloquent\Factories\Factory;
use AlirezaSedghi\LaravelImageFaker\ImageFaker;

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
}
