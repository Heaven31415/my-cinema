<?php

namespace App\Factory;

use App\Entity\Movie;
use App\Repository\GenreRepository;
use App\Repository\MovieRepository;
use DateTime;
use Exception;
use Faker\Factory;
use Faker\Generator;

class MovieFactory
{
    private Generator $faker;

    public function __construct(
        private readonly GenreRepository $genreRepository,
        private readonly MovieRepository $movieRepository
    ) {
        $this->faker = Factory::create();
    }

    /**
     * @throws Exception
     */
    public function create(array $data = []): Movie
    {
        $movie = new Movie();

        $genres = $this->genreRepository->findAll();

        $movie->setTitle($data['title'] ?? ucfirst($this->faker->word()))
            ->setDescription($data['description'] ?? $this->faker->text())
            ->setDurationInMinutes(
                $data['durationInMinutes'] ?? $this->faker->numberBetween(60, 180)
            )
            ->setReleaseDate($data['releaseDate'] ?? new DateTime($this->faker->date()))
            ->setGenre($data['genre'] ?? $genres[rand(0, count($genres) - 1)]);

        $this->movieRepository->save($movie, true);

        return $movie;
    }
}