<?php

namespace App\Factory;

use App\Entity\Show;
use App\Repository\ShowRepository;
use Exception;
use Faker\Factory;
use Faker\Generator;

class ShowFactory
{
    private Generator $faker;

    public function __construct(
        private readonly ShowRepository $showRepository
    ) {
        $this->faker = Factory::create();
    }

    /**
     * @throws Exception
     */
    public function create(array $data = []): Show
    {
        $show = new Show();

        $movie = $data['movie'] ?? MovieFactory::createOne();
        $hall = $data['hall'] ?? HallFactory::createOne();

        $movie->addShow($show);
        $hall->addShow($show);

        $show->setStartTime($data['startTime'] ?? $this->faker->dateTime());

        $this->showRepository->save($show, true);

        return $show;
    }
}