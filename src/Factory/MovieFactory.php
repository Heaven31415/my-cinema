<?php

namespace App\Factory;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use DateTime;
use Exception;
use Faker\Factory;
use Faker\Generator;

class MovieFactory
{
    private Generator $faker;

    public function __construct(private readonly MovieRepository $repository)
    {
        $this->faker = Factory::create();
    }

    /**
     * @throws Exception
     */
    public function create(): Movie
    {
        $movie = new Movie();

        $movie->setTitle(ucfirst($this->faker->word()))
            ->setDescription($this->faker->text())
            ->setLength(new DateTime($this->faker->time()))
            ->setReleaseDate(new DateTime($this->faker->date()));

        $this->repository->save($movie, true);

        return $movie;
    }
}