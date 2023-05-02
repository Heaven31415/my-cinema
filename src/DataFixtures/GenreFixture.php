<?php

namespace App\DataFixtures;

use App\Entity\Genre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class GenreFixture extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['dev', 'prod', 'test'];
    }

    public function load(ObjectManager $manager): void
    {
        $genreNames = array(
            'Action',
            'Adventure',
            'Animation',
            'Comedy',
            'Crime',
            'Documentary',
            'Drama',
            'Family',
            'Fantasy',
            'History',
            'Horror',
            'Music',
            'Mystery',
            'Romance',
            'Science Fiction',
            'Thriller',
            'War',
            'Western',
        );

        foreach ($genreNames as $genreName) {
            $genre = new Genre();
            $genre->setName($genreName);
            $manager->persist($genre);
        }

        $manager->flush();
    }
}
