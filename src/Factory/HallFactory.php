<?php

namespace App\Factory;

use App\Entity\Hall;
use App\Repository\HallRepository;
use Faker\Factory;
use Faker\Generator;

class HallFactory
{
    private Generator $faker;

    public function __construct(private readonly HallRepository $hallRepository)
    {
        $this->faker = Factory::create();
    }

    public function create(array $data = []): Hall
    {
        $hall = new Hall();

        $hall->setName($data['name'] ?? $this->faker->word())
            ->setCapacity($data['capacity'] ?? $this->faker->randomElement([25, 50, 100, 200]));

        $this->hallRepository->save($hall, true);

        return $hall;
    }
}