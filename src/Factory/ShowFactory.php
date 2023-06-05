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
        private readonly MovieFactory $movieFactory,
        private readonly HallFactory $hallFactory,
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

        $movie = $data['movie'] ?? $this->movieFactory->create();
        $hall = $data['hall'] ?? $this->hallFactory->create();

        $movie->addShow($show);
        $hall->addShow($show);

        $show->setStart($data['start'] ?? $this->faker->dateTime());

        $this->showRepository->save($show, true);

        return $show;
    }
}