<?php

namespace App\DataFixtures;

use App\Factory\HallFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class HallFixture extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['dev', 'prod'];
    }

    public function load(ObjectManager $manager): void
    {
        HallFactory::createSequence([
            ['name' => 'A1', 'capacity' => 25],
            ['name' => 'A2', 'capacity' => 25],
            ['name' => 'A3', 'capacity' => 25],
            ['name' => 'A4', 'capacity' => 25],
            ['name' => 'B1', 'capacity' => 50],
            ['name' => 'B2', 'capacity' => 50],
            ['name' => 'C1', 'capacity' => 100],
            ['name' => 'C2', 'capacity' => 100],
            ['name' => 'D1', 'capacity' => 200],
        ]);
    }
}
