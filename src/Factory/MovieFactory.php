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
    public function create(): Movie
    {
        $movie = new Movie();

        // TODO: Find a better way to get a random genre
        $movie->setTitle(ucfirst($this->faker->word()))
            ->setDescription($this->faker->text())
            ->setLength(new DateTime($this->faker->time()))
            ->setReleaseDate(new DateTime($this->faker->date()))
            ->setGenre($this->genreRepository->find($this->faker->numberBetween(1, 18)));

        $this->movieRepository->save($movie, true);

        return $movie;
    }
}