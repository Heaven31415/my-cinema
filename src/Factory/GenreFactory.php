<?php

namespace App\Factory;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use Faker\Factory;
use Faker\Generator;

class GenreFactory
{
    private Generator $faker;

    public function __construct(private readonly GenreRepository $genreRepository)
    {
        $this->faker = Factory::create();
    }

    public function create(array $data = []): Genre
    {
        $genre = new Genre();

        $genre->setName($data['name'] ?? $this->faker->word());

        $this->genreRepository->save($genre, true);

        return $genre;
    }
}